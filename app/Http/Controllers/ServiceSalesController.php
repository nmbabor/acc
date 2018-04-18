<?php

namespace App\Http\Controllers;

use App\Models\ServiceSalesPaymentHistoryItem;
use Illuminate\Http\Request;
use App\Models\Services;
use App\Models\InventoryClient;
use App\Models\ServiceSales;
use App\Models\ServiceSalesItem;
use App\Models\ServiceSalesPaymentHistory;
use App\Models\AccountSetting;
use App\Models\PaymentMethod;
use Yajra\DataTables\DataTables;
use Validator;
use DB;
use Auth;
use App\Models\Inventory;

class ServiceSalesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        return view('services.service_sales.viewSales');

    }

    public function allData(){
        $sales  = ServiceSales::
        leftJoin('inventory_clients','service_sales.fk_client_id','inventory_clients.id')
        ->select('service_sales.*','inventory_clients.client_name')
        ->orderBy('service_sales.id','DESC');
            //return $customer;
        return Datatables::of($sales)
            ->addColumn('action','')
            ->addColumn('invoice_id','<a href=\'<? echo URL::to("services-sales/$invoice_id") ?>\'  title="View Browser">{{$invoice_id}}</a>')
            ->addColumn('total_amount', '{{round($total_amount,2)}}')
            ->addColumn('paid_amount', '{{round($paid_amount,2)}}')
            ->addColumn('due_amount', '{{round($total_amount-$paid_amount,2)}}')
            ->editColumn('action',function($data){


               $file='<a href="'.\URL::to("services-sales/$data->id/edit").'" class="btn btn-warning btn-xs" title="view description"><i class="fa fa-pencil-square"></i></a>  <form method="POST" action="'. url('services-sales/'.$data->id).'" accept-charset="UTF-8"><input name="_method" type="hidden" value="DELETE">'.csrf_field().'<button type="submit" onclick="return confirmDelete();" class="btn btn-xs btn-danger"> <i class="fa fa-trash-o" aria-hidden="true"></i> </button> </form>';

                   return $file;
            })
            ->rawColumns(['invoice_id','action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('services.service_sales.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $branchId=Auth::user()->fk_branch_id;
        $companyId=Auth::user()->fk_company_id;

        $validator = Validator::make($request->all(),[
                'fk_product_id' => 'required',
                'client_name' => 'required',

        ]);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $input = $request->all();
        
        $date = $input['date'];
        $newDate = date("Y-m-d", strtotime($date));
        $input['date'] = $newDate;
        $lastId=ServiceSalesPaymentHistory::max('id')+1;
        $input['invoice_id'] =date('ymd').'-'.$lastId;
        $invoice_id=$input['invoice_id'];

        $client_id=InventoryClient::createNew($input['client_name']);

        DB::beginTransaction();
        try {
        $sales_id = ServiceSales::create([
            'fk_client_id'=>$client_id,
            'summary'=>$input['summary'],
            'invoice_id'=>$input['invoice_id'],
            'date'=>$input['date'],
            'total_amount'=>$input['total_amount'],
            'paid_amount'=>$input['paid_amount'],
            'created_by'=>Auth::user()->id
            ])->id;
            $payment_id = ServiceSalesPaymentHistory::create([
                'invoice_id'=>$input['invoice_id'],
                'last_due'=>$input['total_amount'],
                'paid'=>$input['paid_amount'],
                'payment_date'=>$input['date'],
                'created_by'=>Auth::user()->id,
                'total_amount'=>$input['total_amount'],
                'fk_account_id'=>1,
                'fk_method_id'=>3,
                'ref_id'=>'',
                'fk_client_id'=>$client_id,
                'fk_branch_id'=>$branchId,
                'fk_company_id'=>$companyId,
                'type'=>1,
                ])->id;
            ServiceSalesPaymentHistoryItem::create([
                'fk_payment_id'=>$payment_id,
                'fk_sales_id'=>$sales_id,
                'sales_last_due'=>$input['total_amount'],
                'sales_paid'=>$input['paid_amount'],
                'type'=>1

            ]);
            for ($i=0; $i < sizeof($input['fk_product_id']); $i++) { 
                $product_id = $input['fk_product_id'][$i];
                $price_amount = $input['product_price_amount'][$i];
                $price_discount = $input['product_wise_discount'][$i];
                $price_paid = $input['product_paid_amount'][$i];
                $qty = $input['qty'][$i];

                $createSalesItem = ServiceSalesItem::create([
                    'fk_sales_id' => $sales_id,
                    'fk_service_id' => $product_id,
                    'product_price_amount' => $price_amount,
                    'product_wise_discount' => $price_discount,
                    'product_paid_amount' => $price_paid,
                    'qty' => $qty,
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
            return redirect("services-sales/$invoice_id")->with('success','New payment Created Successfully.');
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
    public function show($invoice)
    {
       $data = ServiceSales::where('invoice_id',$invoice)->first();
       // return $getInvoiceData->id;
        return view('services.service_sales.salesInvoice', compact('data'));
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       $data = ServiceSales::where('service_sales.id',$id)->first();
        if($data==null){
            return redirect()->back();
        }

        $history=$data->payment->where('type',1)->first();

        $anotherPayment=$data->payment->where('type',2)->first();
        if($anotherPayment!=null){
            return redirect()->back();
        }
        $account=AccountSetting::where('account_status',1)->pluck('account_name','id');
        $method=PaymentMethod::where('method_status',1)->pluck('method_name','id');
        return view('services.service_sales.edit', compact('data','account','method','history'));
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

        $input = $request->except('_token','_method');
        $validator = Validator::make($input,[
                'sales_item_id' => 'required',

        ]);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $newDate = date("Y-m-d", strtotime($request->date));
        $data=ServiceSales::findOrFail($id);

        $data->update([
            'paid_amount'=>$request->paid_amount,
            'total_amount'=>$request->total_amount,
            'summary'=>$request->summary,
            'date'=>$newDate
            ]);
        $paymentItem = ServiceSalesPaymentHistoryItem::findOrFail($request->payment_id);

        $payment = ServiceSalesPaymentHistory::findOrFail($paymentItem->fk_payment_id);
        $payment->update([
            'last_due'=>$input['total_amount'],
            'paid'=>$input['paid_amount'],
            'payment_date'=>$newDate,
            'created_by'=>Auth::user()->id,
            'total_amount'=>$input['total_amount'],
            'fk_account_id'=>1,
            'fk_method_id'=>3,
            'ref_id'=>'',
        ]);
        $paymentItem->update([
            'sales_last_due'=>$input['total_amount'],
            'sales_paid'=>$input['paid_amount'],
        ]);
        for ($i=0; $i <sizeof($input['sales_item_id']) ; $i++) {
            $itemId= $input['sales_item_id'][$i];
            ServiceSalesItem::where('id',$itemId)->update([
            'product_price_amount' => $input['product_price_amount'][$i],
            'product_wise_discount' => $input['product_wise_discount'][$i],
            'product_paid_amount' => $input['product_paid_amount'][$i],
            'qty' => $input['qty'][$i],
            ]);
        }

        try {
            
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
        $productData = ServiceSales::findOrFail($id);
           $item = ServiceSalesPaymentHistoryItem::where(['fk_sales_id'=>$id,'type'=>1])->first();
           $payment = ServiceSalesPaymentHistory::findOrFail($item->fk_payment_id);
           $item->delete();
           $payment->delete();
            ServiceSalesItem::where('fk_sales_id',$id)->delete();
            $productData->delete();
        try {
            
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->errorInfo[1];
            $bug1 = $e->errorInfo[2];
        }
        
        if($bug == 0){
            return redirect()->back()->with('success', 'Deleted Successfully .');
        }elseif($bug == 1451){
                return redirect()->back()->with('error','This Data Used AnyWhere.');
            }else{
            return redirect()->back()->with('error','Something Error Found !, Please try again.'.$bug1);
        }
    }

    public function productInfo(Request $request)
    {
        $id = $request->get('id');
        $getProduct = InventoryProduct::
        leftJoin('inventory','services.id','=','inventory.fk_product_id')
        ->where('services.id',$id)
        ->select('inventory.available_qty','inventory.sales_per_unit')
        ->first();
        return $getProduct;
    }

    public function searchService(Request $request){
        $type = $request->get('type');
        $name = $request->get('name_startsWith'); 
        
        $data = array();
        $result=Services::where('product_name', 'LIKE', '%'. $name .'%')->select('id','product_name','sales_price')->get();
        $getResult = $result->toArray();
        foreach ($getResult as $row) {
            $product_result = $row['id'].'|'.$row['product_name'].'|'.$row['sales_price'];
            array_push($data, $product_result);
        }
        //return $name;exit;
          
        return json_encode($data);exit;   
    }
    public function due(){
        return view('services.service_sales.salesDue');
    }

    public function viewAllDue(){
        $sales  = ServiceSales::
        leftJoin('inventory_clients','service_sales.fk_client_id','inventory_clients.id')
        ->select('service_sales.*','inventory_clients.client_name')
        ->whereColumn('total_amount','>','paid_amount')
        ->orderBy('service_sales.id','desc')
        ->get();
        $j=0;
        foreach($sales as $key => $sell){
            $j++; 
            $sales[$key]['sl']=$j;
            $due=round($sell->total_amount,2)-round($sell->paid_amount,2);
            if($due==0){
                unset($sales[$key]);
            }

        }
        
            //return $customer;
        return Datatables::of($sales)
            ->addColumn('invoice_id','
                <a  href=\'{{URL::to("services-sales/$invoice_id")}}\'  title="View Browser">{{$invoice_id}}</a>
                ')
            ->addColumn('total_amount', '{{round($total_amount,2)}}')
            ->addColumn('paid_amount', '{{round($paid_amount,2)}}')
            ->addColumn('due_amount', '{{round($total_amount-$paid_amount,2)}}')
            ->addColumn('action', '
                @if(round($total_amount,2)>round($paid_amount,2))

                    <a  href=\'{{URL::to("services-sales-due-paid/$id")}}\' class="btn btn-xs btn-danger">Due</a>
                @else
                    <a class="btn btn-xs btn-info">Paid</a>

                @endif
                ')
            ->rawColumns(['invoice_id','action'])
            ->make(true);
    }
     public function clientDue($id){
        return view('services.service_sales.clientsDue',compact('id'));
    }
    public function viewClientDue(Request $request){
        $id=$request->id;
        $sales  = ServiceSales::
        leftJoin('inventory_clients','service_sales.fk_client_id','inventory_clients.id')
        ->select('service_sales.*','inventory_clients.client_name')
        ->where('inventory_clients.id',$id)
        ->orderBy('service_sales.id','desc')
        ->get();
        $j=0;
        foreach ($sales as $key => $value) {
            $j++; 
            $sales[$key]['sl']=$j;
        }
            //return $customer;
        return Datatables::of($sales)
            ->editColumn('action',function($data){
                $lastInvoice=ServiceSalesPaymentHistory::where("fk_sales_id",$data->id)->orderBy("id","desc")->value('invoice_id');
                $domain=\URL::to('/');
                if($lastInvoice==null){
                  $lastInvoice=0;
                   }
                   if(round($data->total_amount,2)>round($data->paid_amount,2)){

                        return '<a  href="'. $domain.'/services-sales-due-paid/'.$data->id.'" class="btn btn-xs btn-danger">Due</a>';
                    }else{

                        return '<a href="'.$domain.'/services-sales/'.$lastInvoice.'"  title="View Paid Browser" class="btn btn-xs btn-info">Paid</a>';
                    }
                            
            })
            ->addColumn('invoice_id','
                <a href=\'{{URL::to("services-sales/$invoice_id")}}\'  title="View Browser">{{$invoice_id}}</a>
                ')
            ->addColumn('total_amount', '{{round($total_amount,2)}}')
            ->addColumn('paid_amount', '{{round($paid_amount,2)}}')
            ->addColumn('due_amount', '{{round($total_amount-$paid_amount,2)}}')
            ->rawColumns(['action','invoice_id'])
            ->make(true);
    }

    public function loadClientInfo($id){
        $client=InventoryClient::findOrFail($id);
        return view('services.service_sales.clientInfo',compact('client'));
    }

    /*Due Paid*/
     public function duePaid($id)
    {
        $getInvoiceData = ServiceSales::
        leftJoin('inventory_clients','service_sales.fk_client_id','=','inventory_clients.id')
        ->where('service_sales.id',$id)
        ->select('service_sales.id','service_sales.invoice_id','service_sales.summary','service_sales.date','service_sales.total_amount','service_sales.paid_amount','inventory_clients.client_id','inventory_clients.client_name','inventory_clients.mobile_no','inventory_clients.address','inventory_clients.email_id')
        ->first();
        
        $getProductData = ServiceSalesItem::
        leftJoin('service_sales','service_sales_item.fk_sales_id','=','service_sales.id')
        ->leftJoin('services','service_sales_item.fk_service_id','=','services.id')
        ->where('service_sales.id',$id)
        ->select('service_sales_item.product_price_amount','service_sales_item.product_wise_discount','service_sales_item.product_paid_amount','services.product_name','services.specification')
        ->get();
        $history=ServiceSalesPaymentHistory::where('invoice_id',$getInvoiceData->invoice_id)->first();
        $reference = Doctor::where('status',1)->pluck('doctor_name','id');
        return view('services.service_sales.duePaid', compact('getInvoiceData','getProductData','history','reference'));
    }

    public function dueUpdate(Request $request, $id)
    {
        $newDate = date("Y-m-d", strtotime($request->date));
        $data=ServiceSales::findOrFail($id);
        $data->update([
            'paid_amount'=>$request->last_paid+$request->paid_amount,
            'summary'=>$request->summary
            ]);
        $lastId=ServiceSalesPaymentHistory::max('id')+1;
        $invoice=date('ymd').$lastId;
        if($request->last_due>0 and $request->paid_amount>0){
        ServiceSalesPaymentHistory::create([
            'invoice_id'=>$invoice,
            'fk_sales_id'=>$id,
            'last_due'=>$request->last_due,
            'paid'=>$request->paid_amount,
            'payment_date'=>$newDate,
            'created_by'=>Auth::user()->id,
            'fk_reference_id'=>$request->fk_reference_id,
            ]);
        }else{
           return redirect("/services-sales/$data->invoice_id");
        }

        try {
            
            $bug = 0;
            
        } catch (\Exception $e) {
            $bug = $e->errorInfo[1];
            $bug1 = $e->errorInfo[2];
        }
        
        if($bug == 0){
            return redirect("/services-sales/$invoice")->with('success','Due Payment Successfully.');
        }else{
            return redirect()->back()->with('error','Something Error Found !, Please try again.'.$bug1);
        }
        
    }


















    
}
