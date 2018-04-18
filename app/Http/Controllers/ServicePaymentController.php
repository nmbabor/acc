<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryClient;
use App\Models\ServiceSales;
use App\Models\AccountSetting;
use App\Models\PaymentMethod;
use App\User;
use App\Models\ServiceSalesPaymentHistory;
use App\Models\ServiceSalesPaymentHistoryItem;
use Yajra\DataTables\DataTables;
use Validator;
use Auth;
use DB;

class ServicePaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('services.payment.viewAll');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $branchId=Auth::user()->fk_branch_id;
        $companyId=Auth::user()->fk_company_id;
        $clients = InventoryClient::orderBy('id','desc')->where(['fk_branch_id'=>$branchId,'fk_company_id'=>$companyId])->pluck('company_name','id');

        return view('services.payment.index',compact('clients'));
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
            'fk_client_id' => 'required',
            'fk_sales_id' => 'required',
            'payment_date' => 'required',

        ]);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $date = $input['payment_date'];
        $newDate = date("Y-m-d", strtotime($date));
        $input['payment_date'] = $newDate;
        $lastId=ServiceSalesPaymentHistory::max('id')+1;
        $input['invoice_id'] =date('ymd').$lastId;
        $invoice_id=$input['invoice_id'];
        if($input['paid']==0){
            return redirect()->back()->with('error','Payment is not possible without paid!');
        }
        DB::beginTransaction();
        $payment_id = ServiceSalesPaymentHistory::create([
            'invoice_id'=>$input['invoice_id'],
            'last_due'=>$input['last_due'],
            'paid'=>$input['paid'],
            'payment_date'=>$input['payment_date'],
            'created_by'=>Auth::user()->id,
            'total_amount'=>$input['total_amount'],
            'fk_account_id'=>$input['fk_account_id'],
            'fk_method_id'=>$input['fk_method_id'],
            'ref_id'=>$input['ref_id'],
            'fk_client_id'=>$input['fk_client_id'],
            'fk_branch_id'=>Auth::user()->fk_branch_id,
            'fk_company_id'=>Auth::user()->fk_company_id,
            'type'=>0,
        ])->id;
        $paid=$input['paid'];
        for ($i=0; $i < sizeof($input['fk_sales_id']); $i++) {
            $last_due=$input['sales_last_due'][$i];
            $sales_id=$input['fk_sales_id'][$i];
            if($last_due>$paid){
                $payable=$paid;
                $paid=0;
            }else{
                $payable=$last_due;
                $paid=$paid-$last_due;
            }
            $sales=ServiceSales::where('id',$sales_id)->first();
            $sales->update([
                'paid_amount'=>$payable+$sales->paid_amount,
            ]);
            ServiceSalesPaymentHistoryItem::create([
                'fk_payment_id'=>$payment_id,
                'fk_sales_id'=>$input['fk_sales_id'][$i],
                'sales_last_due'=>$input['sales_last_due'][$i],
                'sales_paid'=>$payable,
                'type'=>0

            ]);
        }
        try {

            DB::commit();
            $bug = 0;

        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
            $bug1 = $e->errorInfo[2];
        }

        if($bug == 0){
            return redirect("service-payment-invoice/$invoice_id")->with('success','New payment Created Successfully.');
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
        $branchId=Auth::user()->fk_branch_id;
        $companyId=Auth::user()->fk_company_id;
        $clients = InventoryClient::orderBy('id','desc')->where(['fk_branch_id'=>$branchId,'fk_company_id'=>$companyId])->pluck('company_name','id');
        $client = InventoryClient::where('id',$id)->select('id','client_name','company_name','client_id','address','mobile_no','email_id')->first();
        $allDue  = ServiceSales::select('id','invoice_id','date','total_amount','paid_amount')
            ->where('fk_client_id',$id)
            ->whereColumn('total_amount','>','paid_amount')
            ->orderBy('service_sales.id','ASC')
            ->get();
        $account=AccountSetting::where('account_status',1)->pluck('account_name','id');
        $method=PaymentMethod::where('method_status',1)->pluck('method_name','id');
        $receiver = User::where('status',1)->where(['fk_branch_id'=>$branchId,'fk_company_id'=>$companyId])->pluck('name','id');
        return view('services.payment.index',compact('clients','client','allDue','account','method','receiver'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $branchId=Auth::user()->fk_branch_id;
        $companyId=Auth::user()->fk_company_id;
        $data=ServiceSalesPaymentHistory::findOrFail($id);
        if($data==null){
            return redirect()->back()->with('error',"ID ($id) is not found!");
        }
        $account=AccountSetting::where('account_status',1)->pluck('account_name','id');
        $method=PaymentMethod::where('method_status',1)->pluck('method_name','id');
        $receiver = User::where('status',1)->where(['fk_branch_id'=>$branchId,'fk_company_id'=>$companyId])->pluck('name','id');
        return view('services.payment.edit', compact('data','account','method','receiver'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input=$request->except('_token','_method');
        $validator = Validator::make($request->all(),[
            'fk_sales_id' => 'required',
            'payment_date' => 'required',

        ]);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $date = $input['payment_date'];
        $newDate = date("Y-m-d", strtotime($date));
        $input['payment_date'] = $newDate;
        if($input['paid']==0){
            return redirect()->back()->with('error','Payment is not possible without paid!');
        }
        $payment=ServiceSalesPaymentHistory::findOrFail($id);
        $payment->update([
            'payment_date'=>$input['payment_date'],
            'paid'=>$input['paid'],
            'last_due'=>$input['last_due'],
            'fk_account_id'=>$input['fk_account_id'],
            'fk_method_id'=>$input['fk_method_id'],
            'ref_id'=>$input['ref_id'],
            'created_by'=>Auth::user()->id,
        ]);
        $paid=$input['paid'];
        for ($i=0; $i < sizeof($input['fk_sales_id']); $i++) {
            $last_due=$input['sales_last_due'][$i];
            $sales_id=$input['fk_sales_id'][$i];
            $item_id=$input['item_id'][$i];


            if($last_due>$paid){
                $payable=$paid;
                $paid=0;
            }else{
                $payable=$last_due;
                $paid=$paid-$last_due;
            }
            $paymentItem=ServiceSalesPaymentHistoryItem::where('id',$item_id)->first();
            $sales=ServiceSales::where('id',$sales_id)->first();
            $sales->update([
                'paid_amount'=>$payable+$sales->paid_amount-$paymentItem->sales_paid,
            ]);
            $paymentItem->update([
                'sales_paid'=>$payable,
            ]);
        }
        try {

            $bug = 0;

        } catch (\Exception $e) {
            $bug = $e->errorInfo[1];
            $bug1 = $e->errorInfo[2];
        }

        if($bug == 0){
            return redirect("service-payment-invoice/$payment->invoice_id")->with('success','Updated Successfully.');
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
        $payment=ServiceSalesPaymentHistory::findOrFail($id);
        $paymentItem=ServiceSalesPaymentHistoryItem::where('fk_payment_id',$id)->get();
        foreach ($paymentItem as $key => $value) {
            $sales=ServiceSales::where('id',$value->fk_sales_id)->first();
            $sales->update([
                'paid_amount'=>$sales->paid_amount-$value->sales_paid,
            ]);
            ServiceSalesPaymentHistoryItem::where('id',$value->id)->delete();

        }
        $payment->delete();
        try {

            $bug = 0;

        } catch (\Exception $e) {
            $bug = $e->errorInfo[1];
            $bug1 = $e->errorInfo[2];
        }

        if($bug == 0){
            return redirect()->back()->with('success','Deleted Successfully.');
        }else{
            return redirect()->back()->with('error','Something Error Found !, Please try again.'.$bug1);
        }
    }
    public function invoice($id)
    {
        $data=ServiceSalesPaymentHistory::where('invoice_id',$id)->first();
        if($data==null){
            return redirect()->back()->with('error',"Invoice ($id) is not found!");
        }

        return view('services.payment.invoice', compact('data'));
    }

    public function allPayment(){
        $branchId=Auth::user()->fk_branch_id;
        $companyId=Auth::user()->fk_company_id;
        $query  = ServiceSalesPaymentHistory::leftJoin('inventory_branch','service_sales_payment_history.fk_branch_id','inventory_branch.id')
            ->leftJoin('inventory_clients','service_sales_payment_history.fk_client_id','inventory_clients.id')
            ->select('service_sales_payment_history.*','inventory_clients.client_name','inventory_clients.company_name','branch_name')
            ->orderBy('service_sales_payment_history.id','DESC');

        if(Auth::user()->isRole('administrator')){
            $sales=$query;
        }else{
            $sales=$query->where(['inventory_payment_history.fk_branch_id'=>$branchId,'inventory_payment_history.fk_company_id'=>$companyId]);
        }
        return Datatables::of($sales)
            ->addColumn('invoice_id','<a href=\'<? echo URL::to("service-payment-invoice/$invoice_id") ?>\' target="_blank" title="View Browser">{{$invoice_id}}</a>')
            ->addColumn('total_amount', '{{round($last_due,2)}}')
            ->addColumn('paid', '{{round($paid,2)}}')
            ->addColumn('due_amount', '{{round($last_due-$paid,2)}}')
            ->addColumn('action', '
                <a href=\'{{URL::to("service-payment/$id/edit")}}\' class="btn btn-warning btn-xs"><i class="fa fa-pencil-square"></i></a>
                {!! Form::open(array(\'route\'=> [\'service-payment.destroy\',$id],\'method\'=>\'DELETE\')) !!}
                    <button type="submit" onclick="return confirmDelete();" class="btn btn-xs btn-danger">
                      <i class="fa fa-trash-o" aria-hidden="true"></i>
                    </button>
                {!! Form::close() !!}
                ')

            ->rawColumns(['invoice_id','action'])
            ->make(true);
    }








}
