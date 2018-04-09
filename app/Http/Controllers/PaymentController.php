<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\CompanyInfo;
use App\Models\EmailConfig;
use App\Models\TermsCondition;
use App\Models\Payment;
use App\Models\PaymentCostItem;
use App\Models\SubCategories;
use App\Models\Clients;
use App\Models\ProjectItem;
use App\Models\PaymentHistory;
use App\Models\PaymentHistoryItem;
use App\Models\AccountSetting;
use App\Models\PaymentMethod;
use Yajra\DataTables\DataTables;
use App\Models\GeneralAssetType;
use App\Models\Deposit;
use DB;
use Validator;
use response;
use Mail;
use PDF;
use Auth;
use URL;
use Form;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       

        return view('payment.viewPayments');
    }

    public function all(){
       $query = Payment::
        leftJoin('clients','payment.fk_client_id','=','clients.id')
        ->leftJoin('inventory_branch','clients.fk_branch_id','=','inventory_branch.id')
       
        ->select('payment.*','clients.client_name','branch_name')
        ->orderBy('payment.id','desc');
        if(Auth::user()->isRole('administrator')){
            $allData=$query;
        }else{
            $allData=$query->where(['payment.fk_branch_id'=>Auth::user()->fk_branch_id,'payment.fk_company_id'=>Auth::user()->fk_company_id]);
        }
        
        return Datatables::of($allData)
        ->editColumn('invoice_no','
            <a href="{{URL::to(\'payment/\')}}/{{$invoice_no}}">{{$invoice_no}}</a>
            ')
        ->addColumn('due','
            {{$amount-$total_paid}}
            ')
        ->addColumn('action',function($payment){
            $last_invoice=PaymentHistory::leftJoin('payment','payment_history.fk_payment_id','payment.id')->where('payment.id',$payment->id)->orderBy('payment_history.id','desc')->select('payment_history.invoice_id')->value('invoice_id');
            $result = '';
            if($payment->amount-$payment->total_paid!=0){

               $result .= '<a href="'.URL::to("due-payment/$payment->id/edit").'" class="btn btn-xs btn-warning">Due</a>';
            }else{

            $result .= '<a href="'.URL::to("payment/$last_invoice").'" class="btn btn-xs btn-success">Paid</a>';
            }
             $result.=' <a href="'.URL::to("payment/$payment->id/edit").'" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i></a>';
              $result .=Form::open(array('route'=> ['payment.destroy',$payment->id],'method'=>'DELETE')).Form::hidden('id',$payment->id).'
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
        $subCategories = SubCategories::where(['status'=>1,'type'=>'1'])->get();
        $getAccountData = AccountSetting::where('account_status',1)->get();
        $getMethodData = PaymentMethod::where('method_status',1)->get();
        $type = GeneralAssetType::where('status',1)->pluck('name','id');
        return view('payment.index', compact('subCategories','getAccountData','getMethodData','type'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
                'client_name' => 'required',
                'fk_method_id' => 'required',
                't_date' => 'required'

        ]);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $input = $request->except('_token');
        $lastId=PaymentHistory::max('id')+1;
        $input['invoice_no'] = date('ymd').$lastId;

        $input['amount'] = $input['total_amount'];
        $input['total_paid'] = $input['paid_amount'];
        $input['fk_branch_id']=Auth::user()->fk_branch_id;
        $input['fk_company_id']=Auth::user()->fk_company_id;
        DB::beginTransaction();
            $input['fk_client_id']=Clients::createNew($input['client_name']);
                if($input['fk_client_id']==null){
                    $input['fk_client_id']=$input['clientId'];
                }
            $clientId = $input['fk_client_id'];
        if(isset($request->fk_loan_id)){
            $loan = Deposit::where(['id'=>$request->fk_loan_id,'fk_client_id'=>$clientId])->first();
            if($loan!=null and $loan->total_paid>$loan->loan_paid){
                $loan->update([
                    'loan_paid'=>$loan->loan_paid+$request->paid_amount,
                ]);
            }else{
                return redirect()->back()->with('error','This loan is no validate!');
            }

        }
            $payment_id = Payment::create($input)->id; 
            $historyId = PaymentHistory::create([
                'fk_payment_id'=>$payment_id,
                'created_by'=>Auth::user()->id,
                'invoice_id'=>$input['invoice_no'],
                'last_total_due'=>$input['amount'],
                'total_paid'=>$input['total_paid'],
                'payment_date'=>date('Y-m-d',strtotime($request->t_date)),
                'type'=>1
            ])->id;
            for ($i=0; $i < sizeof($input['fk_sub_category_id']); $i++) { 
                if(!isset($input['asset_age'][$i])){
                    $input['asset_age'][$i]=null;
                }
                $createPaymentCost = PaymentCostItem::create([
                    'fk_payment_id'=>$payment_id,
                    'fk_sub_category_id'=>$input['fk_sub_category_id'][$i],
                    'description'=>$input['description'][$i],
                    'total_amount'=>$input['total'][$i],
                    'paid_amount'=>$input['paid'][$i],
                    'asset_type_id'=>$input['asset_type_id'][$i],
                    'asset_age'=>$input['asset_age'][$i],

                ])->id;
                PaymentHistoryItem::create([
                    'fk_history_id'=>$historyId,
                    'fk_payment_item_id'=>$createPaymentCost,
                    'last_due'=>$input['total'][$i],
                    'paid'=>$input['paid'][$i],
                ]);
            }
        try {
            $bug = 0;
            
        DB::commit();
         } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
            $bug1 = $e->errorInfo[2];
        }
        
        if($bug == 0){
            return redirect("payment/".$input['invoice_no'])->with('success','New Payment Created Successfully.');
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
        $data = PaymentHistory::where('invoice_id',$id)->first();
        $payment =  Payment::findOrFail($data->fk_payment_id);

        if($payment->fk_loan_id!=null){
            $loan = Deposit::where('id',$payment->fk_loan_id)->first();
        }
        return view('payment.invoice', compact('data','loan'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $subCategories = SubCategories::where(['status'=>1,'type'=>'1'])->get();
        $getClientData = Clients::all();
        $getAccountData = AccountSetting::all();
        $getMethodData = PaymentMethod::all();

        $data = Payment::where('payment.id',$id)->first();
        $payment = array();
        if($data->history!=null){
            $payment = $data->history->where('type',2);
        }
        if($payment!=null){
            return redirect()->back()->with('error','This Transaction is not eligible for edit!');
        }
        
        return view('payment.singlePaymentEdit', compact('subCategories','getClientData','getAccountData','getMethodData','data'));

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
        $getPaymentData = Payment::findOrFail($id);
        $validator = Validator::make($request->all(),[
            'fk_client_id' => 'required',
            'fk_method_id' => 'required',
            't_date' => 'required'
        ]);

        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $input = $request->except('_method','_token');
        try {
            if(isset($request->fk_loan_id) and $request->fk_loan_id!=null){
                $loan = Deposit::where(['id'=>$request->fk_loan_id,'fk_client_id'=>$request->fk_client_id])->first();
                if($loan!=null and $loan->total_paid>$loan->loan_paid){
                    $loan->update([
                        'loan_paid'=>$loan->loan_paid-$getPaymentData->total_paid+$request->total_paid,
                    ]);
                }else{
                    return redirect()->back()->with('error','This loan is no validate!');
                }

            }
         
            $getPaymentData->update($input);
            $paymentHistory = PaymentHistory::where(['fk_payment_id'=>$id,'type'=>1])->first();
           $paymentHistory->update([
                'last_total_due'=>$input['amount'],
                'total_paid'=>$input['total_paid'],
            ]);
            //payment item updated
            $itemOldDataCount = sizeof($input['fk_sub_category_old_id']);
                for ($i=0; $i <$itemOldDataCount ; $i++) {
                    $item = $input['payment_item_old_id'][$i];
                     if(!isset($input['asset_age_old'][$i])){
                        $input['asset_age_old'][$i]=null;
                    }
                    $paymentItemUpdated = PaymentCostItem::where('id',$item)->update([
                        'fk_sub_category_id'=>$input['fk_sub_category_old_id'][$i],
                        'description'=>$input['description_old'][$i],
                        'total_amount'=>$input['total_old'][$i],
                        'paid_amount'=>$input['paid_old'][$i],
                        'asset_type_id'=>$input['asset_type_id_old'][$i],
                        'asset_age'=>$input['asset_age_old'][$i],
                    ]);
                    
                    $historyId = $request->deposit_history_id_old[$i];
                    PaymentHistoryItem::where('id',$historyId)->update([
                        'last_due'=>$input['total_old'][$i],
                        'paid'=>$input['paid_old'][$i],
                    ]);
                }

                //old payment item delete on click delete button
                if(isset($input['deleteItem'])){
                    $itemDeleteDataCount = sizeof($input['deleteItem']);
                    for ($i=0; $i < $itemDeleteDataCount; $i++) {
                        $itemSetDetele[]=$input['deleteItem'][$i];
                        $itemId = $itemSetDetele[$i];
                        $itemData[] = PaymentCostItem::findOrFail($itemId);
                        //return $itemData;

                    }

                    $itemDeleted = PaymentCostItem::deleteItemId($itemData);


                }

                //new payment item created
                if(isset($input['fk_sub_category_id'])){

                    $paymentItemNewDataCount = sizeof($input['fk_sub_category_id']);
                    for ($i=0; $i <$paymentItemNewDataCount ; $i++) {
                        $paymentId = $input['fk_payment_id'];
                       

                        $paymentItemCreated = PaymentCostItem::create([
                            'fk_payment_id'=>$paymentId,
                            'fk_sub_category_id'=>$input['fk_sub_category_id'][$i],
                            'description'=>$input['description'][$i],
                            'total_amount'=>$input['total'][$i],
                            'paid_amount'=>$input['paid'][$i],
                            'asset_type_id'=>$input['asset_type_id'][$i],
                            'asset_age'=>$input['asset_age'][$i],

                        ])->id;
                        PaymentHistoryItem::create([
                            'fk_payment_item_id'=>$paymentItemCreated,
                            'fk_history_id'=>$paymentHistory->id,
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
        $getPaymentData = Payment::findOrFail($id);
        try {
           
             $items = PaymentHistory::where('fk_payment_id',$id)->get();
            foreach($items as $item){
                PaymentHistoryItem::where('fk_history_id',$item->id)->delete(); 
            }
            PaymentHistory::where('fk_payment_id',$id)->delete();
            PaymentCostItem::where('fk_payment_id',$id)->delete();
            $getPaymentData->delete();
            $bug=0;
        } catch (\Exception $e) {
            $bug = $e->errorInfo[1];
            $bug1 = $e->errorInfo[2];
        }
       
        if($bug == 0){
            return redirect()->back()->with('success','Payment Deleted Successfully.');
        }else{
            return redirect()->back()->with('error','Something Error Found !, Please try again.'.$bug1);
        }

    }

    public function publicInvoice($id)
    {
        $getInvoiceData = Payment::
        leftJoin('clients','payment.fk_client_id','=','clients.id')
        ->leftJoin('account','payment.fk_account_id','=','account.id')
        ->leftJoin('payment_method','payment.fk_method_id','=','payment_method.id')
        ->select('payment.*','account.account_name','clients.client_name','clients.mobile_no','clients.address','clients.email_id','payment_method.method_name')
        ->where('payment.id',$id)
        ->first();
        

        $getInvoiceItemData = PaymentCostItem::
        leftJoin('sub_category','payment_cost_item.fk_sub_category_id','=','sub_category.id')
        ->where('payment_cost_item.fk_payment_id',$id)
        ->select('payment_cost_item.*','sub_category.sub_category_name')
        ->get();

        $totalAmount = PaymentCostItem::
            where('fk_payment_id',$id)
            ->selectRaw('sum(total_amount) as total_amount')
            ->first('total_amount');

        $totalPaid = PaymentCostItem::
            where('fk_payment_id',$id)
            ->selectRaw('sum(paid_amount) as total_paid')
            ->first('total_paid');
        


        $data = [
        'getInvoiceData' => $getInvoiceData,
        'getInvoiceItemData' => $getInvoiceItemData,
        'totalAmount' => $totalAmount,
        'totalPaid' => $totalPaid
        ];
        //return $data;

        $companyInfo = CompanyInfo::first();
        $emailConfig = EmailConfig::first();
        $condition = TermsCondition::first();
        
        return view('common.invoice_public_view', compact('data','companyInfo','emailConfig','condition'));

    }
    public function generatePDF($id)
    {
        //$id = $request->get('generate_id');
        $getInvoiceData = Payment::
        leftJoin('clients','payment.fk_client_id','=','clients.id')
        ->leftJoin('account','payment.fk_account_id','=','account.id')
        ->leftJoin('payment_method','payment.fk_method_id','=','payment_method.id')
        ->select('payment.*','account.account_name','clients.client_name','clients.mobile_no','clients.address','clients.email_id','payment_method.method_name')
        ->where('payment.id',$id)
        ->first();
        

        $getInvoiceItemData = PaymentCostItem::
        leftJoin('sub_category','payment_cost_item.fk_sub_category_id','=','sub_category.id')
        ->where('payment_cost_item.fk_payment_id',$id)
        ->select('payment_cost_item.*','sub_category.sub_category_name')
        ->get();

        $totalAmount = PaymentCostItem::
            where('fk_payment_id',$id)
            ->selectRaw('sum(total_amount) as total_amount')
            ->first('total_amount');

        $totalPaid = PaymentCostItem::
            where('fk_payment_id',$id)
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
        //return "hi";
        $validator = Validator::make($request->all(),[
                'to' => 'required'
        ]);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $input = $request->all();


        $getInvoiceData = Payment::
        leftJoin('clients','payment.fk_client_id','=','clients.id')
        ->leftJoin('account','payment.fk_account_id','=','account.id')
        ->leftJoin('payment_method','payment.fk_method_id','=','payment_method.id')
        ->select('payment.*','account.account_name','clients.client_name','clients.mobile_no','clients.address','clients.email_id','payment_method.method_name')
        ->where('payment.id',$id)
        ->first();
        

        $getInvoiceItemData = PaymentCostItem::
        leftJoin('sub_category','payment_cost_item.fk_sub_category_id','=','sub_category.id')
        ->where('payment_cost_item.fk_payment_id',$id)
        ->select('payment_cost_item.*','sub_category.sub_category_name')
        ->get();

        $totalAmount = PaymentCostItem::
            where('fk_payment_id',$id)
            ->selectRaw('sum(total_amount) as total_amount')
            ->first('total_amount');

        $totalPaid = PaymentCostItem::
            where('fk_payment_id',$id)
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

    public function searchClient(Request $request){
        $name=$request->name;
        $data=array();

          $allData=Clients::where(['clients.fk_branch_id'=>Auth::user()->fk_branch_id,'clients.fk_company_id'=>Auth::user()->fk_company_id])->where('client_name', 'LIKE', '%'. $name .'%')->orderBy('client_name','ASC')->pluck('client_name','id');
          foreach ($allData as $key => $value) {
              $data[]=$value.'|'.$key;
          }
        
        return json_encode($data); 
    }
    

public function subCategoryAsset($id){
    return SubCategories::leftJoin('general_asset_type','sub_category.asset_type_id','general_asset_type.id')->select('sub_category.*','general_asset_type.type as asset_type','general_asset_type.name')->where('sub_category.id',$id)->first();
}










}
