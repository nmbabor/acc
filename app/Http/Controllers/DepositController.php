<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\CompanyInfo;
use App\Models\EmailConfig;
use App\Models\TermsCondition;
use App\Models\Deposit;
use App\Models\DepositCostItem;
use App\Models\SubCategories;
use App\Models\Clients;
use App\Models\ProjectItem;
use App\Models\AccountSetting;
use App\Models\PaymentMethod;
use App\Models\DepositHistory;
use App\Models\DepositHistoryItem;
use App\Models\Payment;
use DB;
use Validator;
use response;
use Mail;
use PDF;
use URL;
use Form;
use Auth;
use Yajra\DataTables\DataTables;

class DepositController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       

        return view('deposit.viewDeposits');
    }
    public function all(){
       $query = Deposit::
        leftJoin('clients','deposit.fk_client_id','=','clients.id')
        ->leftJoin('inventory_branch','clients.fk_branch_id','=','inventory_branch.id')
        ->select('deposit.*','clients.client_name','branch_name')
        ->orderBy('deposit.id','desc');
        if(Auth::user()->isRole('administrator')){
            $allData=$query;
        }else{
            $allData=$query->where(['deposit.fk_branch_id'=>Auth::user()->fk_branch_id,'deposit.fk_company_id'=>Auth::user()->fk_company_id]);
        }
        
        return Datatables::of($allData)
        ->editColumn('invoice_no','
            <a href="{{URL::to(\'deposit/\')}}/{{$invoice_no}}">{{$invoice_no}}</a>
            ')
        ->addColumn('due','
            {{$amount-$total_paid}}
            ')
        ->addColumn('action',function($deposit){
            $last_invoice=DepositHistory::leftJoin('deposit','deposit_history.fk_deposit_id','deposit.id')->where('deposit.id',$deposit->id)->orderBy('deposit_history.id','desc')->select('deposit_history.invoice_id')->value('invoice_id');
            $result = '';
            if($deposit->amount-$deposit->total_paid!=0){

               $result .= '<a href="'.URL::to("due-deposit/$deposit->id/edit").'" class="btn btn-xs btn-warning">Due</a>';
            }else{

            $result .= '<a href="'.URL::to("deposit/$last_invoice").'" class="btn btn-xs btn-success">Paid</a>';
            }
            $result.=' <a href="'.URL::to("deposit/$deposit->id/edit").'" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i></a>';
              $result .=Form::open(array('route'=> ['deposit.destroy',$deposit->id],'method'=>'DELETE')).Form::hidden('id',$deposit->id).'
                        <button type="submit" onclick="return confirmDelete();" class="btn btn-xs btn-danger">
                          <i class="fa fa-trash-o" aria-hidden="true"></i>
                        </button>'.
               Form::close();
            return $result;

        })
        ->rawColumns(['action','invoice_no'])
        ->make(true);

    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $subCategories = SubCategories::where(['status'=>1,'type'=>2])->get();
        $getClientData = Clients::where('client_status',1)->get();
        $getAccountData = AccountSetting::where('account_status',1)->get();
        $getMethodData = PaymentMethod::where('method_status',1)->get();
        return view('deposit.index', compact('subCategories','getClientData','getAccountData','getMethodData'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($request->all(),[
                'client_name' => 'required',
                'fk_method_id' => 'required',
                't_date' => 'required'

        ]);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $lastId=DepositHistory::max('id')+1;
        $input['invoice_no'] = date('ymd').$lastId;

        $input['amount'] = $input['total_amount'];
        $input['total_paid'] = $input['paid_amount'];
        $input['fk_branch_id']=Auth::user()->fk_branch_id;
        $input['fk_company_id']=Auth::user()->fk_company_id;
        DB::beginTransaction();
        try {
            
            $input['fk_client_id']=Clients::createNew($input['client_name']);
                if($input['fk_client_id']==null){
                    $input['fk_client_id']=$input['clientId'];
                }
            $clientId= $input['fk_client_id'];
            if(isset($request->fk_loan_id)){
                $loan = Payment::where(['id'=>$request->fk_loan_id,'fk_client_id'=>$clientId])->first();
                if($loan!=null and $loan->total_paid>$loan->loan_paid){
                    $loan->update([
                        'loan_paid'=>$loan->loan_paid+$request->paid_amount,
                    ]);
                }else{
                    return redirect()->back()->with('error','This loan is no validate!');
                }

            }
            $deposit_id = Deposit::create($input)->id; 
            $historyId = DepositHistory::create([
                'fk_deposit_id'=>$deposit_id,
                'created_by'=>Auth::user()->id,
                'invoice_id'=>$input['invoice_no'],
                'last_total_due'=>$input['amount'],
                'total_paid'=>$input['total_paid'],
                'payment_date'=>date('Y-m-d',strtotime($request->t_date)),
                'type'=>1
            ])->id;
            for ($i=0; $i < sizeof($input['fk_sub_category_id']); $i++) { 
                $createDepositCost = DepositCostItem::create([
                    'fk_deposit_id'=>$deposit_id,
                    'fk_sub_category_id'=>$input['fk_sub_category_id'][$i],
                    'description'=>$input['description'][$i],
                    'total_amount'=>$input['total'][$i],
                    'paid_amount'=>$input['paid'][$i],
                ])->id;
                DepositHistoryItem::create([
                    'fk_history_id'=>$historyId,
                    'fk_deposit_item_id'=>$createDepositCost,
                    'last_due'=>$input['total'][$i],
                    'paid'=>$input['paid'][$i],
                ]);
            }
            $bug = 0;
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
            $bug1 = $e->errorInfo[2];
        }
        
        if($bug == 0){
            return redirect("deposit/".$input['invoice_no'])->with('success','New Deposit Created Successfully.');
        }else{
            return redirect()->back()->with('error','Something Error Found !, Please try again.'.$bug1);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = DepositHistory::where('invoice_id',$id)->first();
        $payment =  Deposit::findOrFail($data->fk_deposit_id);

        if($payment->fk_loan_id!=null){
            $loan = Payment::where('id',$payment->fk_loan_id)->first();
        }
        return view('deposit.invoice', compact('data','loan'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $subCategories = SubCategories::where(['status'=>1,'type'=>2])->get();
        $getClientData = Clients::all();
        $getProjectData = ProjectItem::all();
        $getAccountData = AccountSetting::all();
        $getMethodData = PaymentMethod::all();

        $data = Deposit::where('deposit.id',$id)->first();
        $payment = $data->history->where('type',2);
        if(count($payment)>0){
            return redirect()->back()->with('error','This Transaction is not eligible for edit!');
        }

        if($data->fk_loan_id!=null){
            $loan = Payment::where('id',$data->fk_loan_id)->first();
        }
        return view('deposit.singleDepositEdit', compact('subCategories','getClientData','getProjectData','getAccountData','getMethodData','data','loan'));

    }

    /**
     * Update the specified resource in storage contact us page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $getDepositData = Deposit::findOrFail($id);
        $validator = Validator::make($request->all(),[
            'fk_client_id' => 'required',
            'fk_method_id' => 'required',
            't_date' => 'required'
        ]);

        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $input = $request->all();

        try {
             if(isset($request->fk_loan_id)){
                $loan = Payment::where(['id'=>$request->fk_loan_id,'fk_client_id'=>$request->fk_client_id])->first();
                if($loan!=null and $loan->total_paid>$loan->loan_paid){
                    $loan->update([
                        'loan_paid'=>$loan->loan_paid-$getDepositData->total_paid+$request->total_paid,
                    ]);
                }else{
                    return redirect()->back()->with('error','This loan is no validate!');
                }

            }
            $getDepositData->update($input);
            $depositHistory = DepositHistory::where(['fk_deposit_id'=>$id,'type'=>1])->first();
           $depositHistory->update([
                'last_total_due'=>$input['amount'],
                'total_paid'=>$input['total_paid'],
            ]);
            //deposit item updated
            $itemOldDataCount = sizeof($input['fk_sub_category_old_id']);
                for ($i=0; $i <$itemOldDataCount ; $i++) {
                    $depositItemId = $input['deposit_item_old_id'][$i];

                    $depositItemUpdated = DepositCostItem::where('id',$depositItemId)->update([
                        'fk_sub_category_id'=>$input['fk_sub_category_old_id'][$i],
                        'description'=>$input['description_old'][$i],
                        'total_amount'=>$input['total_old'][$i],
                        'paid_amount'=>$input['paid_old'][$i],
                    ]);
                    $historyId = $request->deposit_history_id_old[$i];
                    DepositHistoryItem::where('id',$historyId)->update([
                        'last_due'=>$input['total_old'][$i],
                        'paid'=>$input['paid_old'][$i],
                    ]);
                }

                //old deposit item delete on click delete button
                if(isset($input['deleteItem'])){
                    $itemDeleteDataCount = sizeof($input['deleteItem']);
                    for ($i=0; $i < $itemDeleteDataCount; $i++) {
                        $itemSetDetele[]=$input['deleteItem'][$i];
                        $itemId = $itemSetDetele[$i];
                        $itemData[] = DepositCostItem::findOrFail($itemId);
                        //return $itemData;

                    }

                    $itemDeleted = DepositCostItem::deleteItemId($itemData);


                }

                //new deposit item created
                if(isset($input['fk_sub_category_id'])){

                    $depositItemNewDataCount = sizeof($input['fk_sub_category_id']);
                    for ($i=0; $i <$depositItemNewDataCount ; $i++) {
                        $createDepositCost = DepositCostItem::create([
                            'fk_deposit_id'=>$getDepositData->id,
                            'fk_sub_category_id'=>$input['fk_sub_category_id'][$i],
                            'description'=>$input['description'][$i],
                            'total_amount'=>$input['total'][$i],
                            'paid_amount'=>$input['paid'][$i],
                        ])->id;
                        DepositHistoryItem::create([
                            'fk_deposit_item_id'=>$createDepositCost,
                            'fk_history_id'=>$depositHistory->id,
                            'last_due'=>$input['total'][$i],
                            'paid'=>$input['paid'][$i],
                            ]);
                    }
                }


            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->errorInfo[1];
            $bug1 = $e->errorInfo[2];
        }
       
        if($bug == 0){
            return redirect()->back()->with('success','Updated Successfully.');
        }else{
            return redirect()->back()->with('error','Something Error Found !, Please try again.'.$bug1);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $getDepositData = Deposit::findOrFail($id);
        try {
           $items = DepositHistory::where('fk_deposit_id',$id)->get();
            foreach($items as $item){
                DepositHistoryItem::where('fk_history_id',$item->id)->delete(); 
            }
            DepositHistory::where('fk_deposit_id',$id)->delete();
            DepositCostItem::where('fk_deposit_id',$id)->delete();
            $getDepositData->delete();
            $bug=0;
        } catch (\Exception $e) {
            $bug = $e->errorInfo[1];
            $bug1 = $e->errorInfo[2];
        }
       
        if($bug == 0){
            return redirect()->back()->with('success','Deposit Deleted Successfully.');
        }else{
            return redirect()->back()->with('error','Something Error Found !, Please try again.'.$bug1);
        }

    }

    public function publicInvoice($id)
    {
        $getInvoiceData = Deposit::
        leftJoin('clients','deposit.fk_client_id','=','clients.id')
        ->leftJoin('account','deposit.fk_account_id','=','account.id')
        ->leftJoin('payment_method','deposit.fk_method_id','=','payment_method.id')
        ->select('deposit.*','account.account_name','clients.client_name','clients.mobile_no','clients.address','clients.email_id','payment_method.method_name')
        ->where('deposit.id',$id)
        ->first();
        

        $getInvoiceItemData = DepositCostItem::
        leftJoin('sub_category','deposit_cost_item.fk_sub_category_id','=','sub_category.id')
        ->where('deposit_cost_item.fk_deposit_id',$id)
        ->select('deposit_cost_item.*','sub_category.sub_category_name')
        ->get();

        $totalAmount = DepositCostItem::
            where('fk_deposit_id',$id)
            ->selectRaw('sum(total_amount) as total_amount')
            ->first('total_amount');

        $totalPaid = DepositCostItem::
            where('fk_deposit_id',$id)
            ->selectRaw('sum(paid_amount) as total_paid')
            ->first('total_paid');
        


        $data = [
        'getInvoiceData' => $getInvoiceData,
        'getInvoiceItemData' => $getInvoiceItemData,
        'totalAmount' => $totalAmount,
        'totalPaid' => $totalPaid
        ];

        $companyInfo = CompanyInfo::first();
        $emailConfig = EmailConfig::first();
        $condition = TermsCondition::first();
        //return $data;
        return view('common.invoice_public_view', compact('data','companyInfo','emailConfig','condition'));

    }
    public function generatePDF($id)
    {
        //$id = $request->get('generate_id');
        $getInvoiceData = Deposit::
        leftJoin('clients','deposit.fk_client_id','=','clients.id')
        ->leftJoin('account','deposit.fk_account_id','=','account.id')
        ->leftJoin('payment_method','deposit.fk_method_id','=','payment_method.id')
        ->select('deposit.*','account.account_name','clients.client_name','clients.mobile_no','clients.address','clients.email_id','payment_method.method_name')
        ->where('deposit.id',$id)
        ->first();
        

        $getInvoiceItemData = DepositCostItem::
        leftJoin('sub_category','deposit_cost_item.fk_sub_category_id','=','sub_category.id')
        ->where('deposit_cost_item.fk_deposit_id',$id)
        ->select('deposit_cost_item.*','sub_category.sub_category_name')
        ->get();

        $totalAmount = DepositCostItem::
            where('fk_deposit_id',$id)
            ->selectRaw('sum(total_amount) as total_amount')
            ->first('total_amount');

        $totalPaid = DepositCostItem::
            where('fk_deposit_id',$id)
            ->selectRaw('sum(paid_amount) as total_paid')
            ->first('total_paid');

        $data = [
        'getInvoiceData' => $getInvoiceData,
        'getInvoiceItemData' => $getInvoiceItemData,
        'totalAmount' => $totalAmount,
        'totalPaid' => $totalPaid
        ];
        //return $data;
        //return view('common.invoice_public_view', compact('data'));
        $companyInfo = CompanyInfo::first();
        $emailConfig = EmailConfig::first();
        $condition = TermsCondition::first();

        $pdf = \PDF::loadView('common.invoice_public_view', compact('data','companyInfo','emailConfig','condition'));
        return $pdf->stream("invoice_$id.pdf");
    }

    public function sendEmail(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
                'to' => 'required'
        ]);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $input = $request->all();


        $getInvoiceData = Deposit::
        leftJoin('clients','deposit.fk_client_id','=','clients.id')
        ->leftJoin('account','deposit.fk_account_id','=','account.id')
        ->leftJoin('payment_method','deposit.fk_method_id','=','payment_method.id')
        ->select('deposit.*','account.account_name','clients.client_name','clients.mobile_no','clients.address','clients.email_id','payment_method.method_name')
        ->where('deposit.id',$id)
        ->first();
        

        $getInvoiceItemData = DepositCostItem::
        leftJoin('sub_category','deposit_cost_item.fk_sub_category_id','=','sub_category.id')
        ->where('deposit_cost_item.fk_deposit_id',$id)
        ->select('deposit_cost_item.*','sub_category.sub_category_name')
        ->get();

        $totalAmount = DepositCostItem::
            where('fk_deposit_id',$id)
            ->selectRaw('sum(total_amount) as total_amount')
            ->first('total_amount');

        $totalPaid = DepositCostItem::
            where('fk_deposit_id',$id)
            ->selectRaw('sum(paid_amount) as total_paid')
            ->first('total_paid');

        $data = [
        'getInvoiceData' => $getInvoiceData,
        'getInvoiceItemData' => $getInvoiceItemData,
        'totalAmount' => $totalAmount,
        'totalPaid' => $totalPaid
        ];
        
        $companyInfo = CompanyInfo::first();
        $emailConfig = EmailConfig::first();
        $condition = TermsCondition::first();

        $pdf = PDF::loadView('common.invoice_public_view', compact('data','companyInfo','emailConfig','condition'));
        Mail::send('common.mail_body', array('body' => $input['body']), function($message) use ($input, $data, $pdf, $id)
        {
        $message->from($input['from'], $input['from']);
        if(!empty($input['cc'])){

            $message->cc($input['cc'], $input['cc']);
        }

        $message->to($input['to'])->subject($input['subject']);
        $message->attachData($pdf->output(), "invoice_$id.pdf");
        });

        try {
            

            $bug=0;
            
        } catch (\Exception $e) {
            $bug = $e->errorInfo[1];
            $bug1 = $e->errorInfo[2]; 
        }
        if($bug == 0){
            return redirect()->back()->with('success','Send Successfully.');
        }else{
            return redirect()->back()->with('error','Something Error Found !, Please try again.'.$bug1);
        }
    }
}
