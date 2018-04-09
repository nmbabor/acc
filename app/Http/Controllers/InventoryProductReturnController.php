<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Models\InventoryClient;
use App\Models\InventoryProductSales;
use App\Models\InventoryProductSalesItem;
use App\Models\AccountSetting;
use App\Models\PaymentMethod;
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
        //
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
        if(isset($request->id)){
            $client = InventoryClient::where('id',$request->id)->select('id','client_name','company_name','client_id','address','mobile_no','email_id')->first();
            $from=date('Y-m-d',strtotime($request->from));
            $to=date('Y-m-d',strtotime($request->to));
            $sales=InventoryProductSales::whereBetween('date',[$from,$to])->where('fk_client_id',$request->id)->get();
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
        if(isset($request->payment_id) and $request->payment_id!=null){
            $paymentItem=InventorySalesPaymentHistoryItem::findOrFail($request->payment_id);
            $payment = InventorySalesPaymentHistory::findOrFail($paymentItem->fk_payment_id);
            return $payment;
        }
        $return = InventoryProductReturn::create([
            'fk_sales_id'=>$input['id'],
            'total_amount'=>$input['total_amount'],
        ])->id;
        for ($i=0;$i<sizeof($request->sales_item_id);$i++){
            if($request->sales_qty[$i]>0){
//           Item return to inventory
            $inventoryIds=json_decode($request->inventory_item_id[$i],true);

            if(count($inventoryIds)>0){
                foreach ($inventoryIds as $invId => $invValue) {
                    $inventoryItem = InventoryItem::where('id',$invId)->first();
                    if($inventoryItem!=null){
                        $inventoryItem->update([
                            'available_qty'=>$inventoryItem->available_qty+$invValue
                        ]);
                    }
                }
            }
            $getInventory = Inventory::leftJoin('inventory_product','inventory.fk_product_id','inventory_product.id')
                ->where(['fk_product_id'=>$request->fk_product_id[$i],'inventory.fk_branch_id'=>$sales->fk_branch_id,'inventory.fk_company_id'=>$sales->fk_company_id])
                ->select('inventory.id','available_qty','available_small_qty')
                ->orderBy('id','asc')
                ->first();

            if($getInventory!=null){
                $getInventory->update([
                    'available_qty' => $getInventory->available_qty+$request->qty,
                ]);
            }
// \Item Return to Inventory
                $totalAmount = ($sales->total_amount-$request->total_amount);
                $returnAmount = ($sales->paid_amount-$totalAmount)>0?$sales->paid_amount-$totalAmount:0;


                InventoryProductReturnItem::create([
                    'fk_return_id' => $return,
                    'fk_sales_item_id' => $request->sales_item_id[$i],
                    'qty' => $request->sales_qty[$i],
                    'sub_total' => $request->product_paid_amount[$i]
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
            return redirect("inventory-product-add/$invoice")->with('success','Order Created Successfully.');
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
        //
    }
}
