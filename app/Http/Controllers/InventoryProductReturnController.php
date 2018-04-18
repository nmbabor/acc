<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryClient;
use App\Models\InventoryProductSales;
use App\Models\InventoryProductSalesItem;
use App\Models\InventorySalesPaymentHistory;
use App\Models\InventorySalesPaymentHistoryItem;
use App\Models\InventoryProductReturn;
use App\Models\InventoryProductReturnItem;
use App\Models\Inventory;
use App\Models\InventoryItem;
use Yajra\DataTables\DataTables;
use Validator;
use Auth;
use DB;


class InventoryProductReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pos.return.viewAll');
    }
    public function all(){
        $count = InventoryProductReturn::count('*');
        DB::statement(DB::raw("set @rownum=$count+1"));
        $allData = InventoryProductReturn::leftJoin('inventory_product_sales','inventory_product_return.fk_sales_id','inventory_product_sales.id')
                   ->leftJoin('inventory_clients','inventory_product_sales.fk_client_id','inventory_clients.id')
                   ->leftJoin('inventory_branch','inventory_product_sales.fk_branch_id','inventory_branch.id')

                   ->select('inventory_product_return.*','inventory_product_sales.invoice_id','inventory_branch.branch_name'
                       ,'inventory_clients.company_name',DB::raw('@rownum := @rownum - 1 AS sl'));
        return Datatables::of($allData)
            ->addColumn('invoice_id','<a href=\'<? echo URL::to("inventory-sales-invoice/$invoice_id") ?>\' title="View Browser">{{$invoice_id}}</a>')
            ->addColumn('total_amount', '{{round($total_amount,2)}}')
            ->addColumn('total_return', '{{round($total_return,2)}}')
            ->addColumn('action', '
                <a href=\'{{URL::to("inventory-return-show/$id")}}\' class="btn btn-primary btn-xs" title="view description"><i class="fa fa-eye"></i></a>
                
                {!! Form::open(array(\'route\'=> [\'inventory-return.destroy\',$id],\'method\'=>\'DELETE\')) !!}
                    <button type="submit" onclick="return confirmDelete();" class="btn btn-xs btn-danger">
                      <i class="fa fa-trash-o" aria-hidden="true"></i>
                    </button>
                {!! Form::close() !!}
                ')

            ->rawColumns(['invoice_id','action','sl'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $branchId=Auth::user()->fk_branch_id;
        $companyId=Auth::user()->fk_company_id;
        $clients = InventoryClient::orderBy('id','desc')->where(['fk_branch_id'=>$branchId,'fk_company_id'=>$companyId])->pluck('company_name','id');

        if(isset($request->from)){

            $from=date('Y-m-d',strtotime($request->from));
            $to=date('Y-m-d',strtotime($request->to));
            $sales=InventoryProductSales::whereBetween('date',[$from,$to]);
            if(isset($request->id)){
                $client = InventoryClient::where('id',$request->id)->select('id','client_name','company_name','client_id','address','mobile_no','email_id')->first();
                $sales=$sales->where('fk_client_id',$request->id);
            }
            $sales=$sales->get();
        }
        return view('pos.return.index',compact('clients','sales','client'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->except('_token');
        $validator = Validator::make($request->all(),[
            'sales_item_id.*' => 'required',
            'sales_qty' => 'required',
            'total_amount' => 'required',
            'product_price_amount' => 'required',

        ]);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        $sales = InventoryProductSales::findOrFail($request->id);
        $account=1;
        $method=3;
        if(isset($request->payment_id) and $request->payment_id!=null){
            $paymentItem=InventorySalesPaymentHistoryItem::findOrFail($request->payment_id);
            $payment = InventorySalesPaymentHistory::findOrFail($paymentItem->fk_payment_id);
            $account=$payment->fk_account_id;
            $method=$payment->fk_method_id;
        }
        $totalAmount = ($sales->total_amount-$request->total_amount);
        $returnAmount = ($sales->paid_amount-$totalAmount)>0?$sales->paid_amount-$totalAmount:0;
        $return = InventoryProductReturn::create([
            'fk_sales_id'=>$input['id'],
            'total_amount'=>$sales->total_amount,
            'total_return'=>$request->total_amount,
            'back_amount'=>$returnAmount,
            'date'=>date('Y-m-d',strtotime($request->date)),
            'fk_account_id'=>$account,
            'fk_method_id'=>$method,
        ])->id;

        $sales->update([
            'total_amount'=>$totalAmount,
            'paid_amount'=>$sales->paid_amount-$returnAmount,
        ]);
        for ($i=0;$i<sizeof($request->sales_item_id);$i++){
            if($request->sales_qty[$i]>0){
//           Item return to inventory
            $inventoryIds=json_decode($request->inventory_item_id[$i],true);

                $qty = $request->sales_qty[$i];
                $inventory_Id = array();
                $price=0;
                if($inventoryIds!=null and count($inventoryIds)>0){

                foreach ($inventoryIds as $invId => $invValue) {
                    $inventoryItem = InventoryItem::where('id',$invId)->first();

                    if($qty>0){
                        if($qty>=$invValue){
                            if($inventoryItem!=null){
                                $inventoryItem->update([
                                    'available_qty'=>$inventoryItem->available_qty+$invValue,
                                ]);

                                $inventory_Id[$invId]=$invValue;
                                $price+=$inventoryItem->cost_per_unit*$invValue;
                                $qty =$qty-$invValue;

                            }
                        }else{
                            if($inventoryItem!=null){
                                $inventoryItem->update([
                                    'available_qty'=>$inventoryItem->available_qty+$qty
                                ]);
                                $inventory_Id[$invId]=$qty;
                                $price+=$inventoryItem->cost_per_unit*$qty;
                                $qty = $qty-$qty;

                            }
                        }

                    }

                }

            }

            $salesItem = InventoryProductSalesItem::where('id',$request->sales_item_id[$i])->first();
            $getInventory = Inventory::where(['fk_product_id'=>$salesItem->fk_product_id,
                'inventory.fk_model_id'=>$salesItem->fk_model_id,'inventory.fk_branch_id'=>$sales->fk_branch_id,
                'inventory.fk_company_id'=>$sales->fk_company_id])
                ->select('inventory.id','available_qty','available_small_qty')
                ->orderBy('id','asc')
                ->first();


            if($getInventory!=null){
                $getInventory->update([
                    'available_qty' => $getInventory->available_qty+$request->sales_qty[$i],
                ]);
            }
//              \Item Return to Inventory

                $salesItem->update([
                    'sales_qty'=>$salesItem->sales_qty-$request->sales_qty[$i],
                    'cost_amount'=>$salesItem->cost_amount-$price,
                    'product_paid_amount'=>$salesItem->product_paid_amount-$request->product_paid_amount[$i],
                ]);
                $inventoryId=json_encode($inventory_Id);
                InventoryProductReturnItem::create([
                    'fk_return_id' => $return,
                    'fk_sales_item_id' => $request->sales_item_id[$i],
                    'qty' => $request->sales_qty[$i],
                    'sub_total' => $request->product_paid_amount[$i],
                    'inventory_item_id' => $inventoryId
                ]);
            }
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
            return redirect("inventory-return-show/$return")->with('success','Order Created Successfully.');
        }else{
            return redirect()->back()->with('error','Something Error Found!, Please try again.'.$bug1);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function single($id){
        $data = InventoryProductReturn::findOrFail($id);
        return view('pos.return.invoice',compact('data'));
    }
    public function show($id)
    {
        $branchId=Auth::user()->fk_branch_id;
        $companyId=Auth::user()->fk_company_id;
       $data = InventoryProductSales::findOrFail($id);
        if($data==null){
            return redirect()->back();
        }

        $payment=$data->paymentItem->where('type',1)->first();

        if($payment==null){
            $anotherPayment=$data->paymentItem->first();
            if($anotherPayment!=null){
                return redirect()->back();
            }
        }

        return view('pos.return.show', compact('data','payment'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = InventoryProductReturn::findOrFail($id);
        $sales = InventoryProductSales::findOrFail($data->fk_sales_id);
        $sales->update([
            'total_amount'=>$sales->total_amount+$data->total_return,
            'paid_amount'=>$sales->paid_amount+$data->back_amount,
        ]);
        $items = InventoryProductReturnItem::where('fk_return_id',$id)->get();
        foreach($items as $item){
            $salesItem = InventoryProductSalesItem::where('id',$item->fk_sales_item_id)->first();

            //           Item return to inventory
            $inventoryIds=json_decode($item->inventory_item_id,true);
            $price=0;
            if($inventoryIds!=null and count($inventoryIds)>0){
                foreach ($inventoryIds as $invId => $invValue) {
                    $inventoryItem = InventoryItem::where('id',$invId)->first();
                    if($inventoryItem!=null){
                        $inventoryItem->update([
                            'available_qty'=>$inventoryItem->available_qty-$invValue
                        ]);
                        $price+=$inventoryItem->cost_per_unit*$invValue;
                    }
                }
            }
            $getInventory = Inventory::leftJoin('inventory_product','inventory.fk_product_id','inventory_product.id')
                ->where(['fk_product_id'=>$item->items->fk_product_id,'inventory.fk_model_id'=>$item->items->fk_model_id,
                    'inventory.fk_branch_id'=>$sales->fk_branch_id,'inventory.fk_company_id'=>$sales->fk_company_id])
                ->select('inventory.id','available_qty','available_small_qty')
                ->orderBy('id','asc')
                ->first();

            if($getInventory!=null){
                $getInventory->update([
                    'available_qty' => $getInventory->available_qty-$item->qty,
                ]);
            }
//              \Item Return to Inventory
            $salesItem->update([
                'sales_qty'=>$salesItem->sales_qty+$item->qty,
                'cost_amount'=>$salesItem->cost_amount+$price,
                'product_paid_amount'=>$salesItem->product_paid_amount+$item->sub_total,
            ]);
        }
        InventoryProductReturnItem::where('fk_return_id',$id)->delete();
        $data->delete();
        try {

            $bug=0;
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
}
