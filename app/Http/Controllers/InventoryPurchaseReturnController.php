<?php

namespace App\Http\Controllers;

use App\Models\InventoryOrderPayment;
use App\Models\InventoryOrderPaymentItem;
use App\Models\InventoryProductAdd;
use App\Models\InventoryProductAddItem;
use App\Models\InventoryPurchaseReturn;
use App\Models\InventoryPurchaseReturnItem;
use App\Models\InventorySupplier;
use App\Models\Inventory;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Auth;
use DB;
use Validator;
use Yajra\DataTables\DataTables;

class InventoryPurchaseReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pos.addItem.return.viewAll');
    }
    public function all(){
        $count = InventoryPurchaseReturn::count('*');
        DB::statement(DB::raw("set @rownum=$count+1"));
        $allData = InventoryPurchaseReturn::leftJoin('inventory_product_add','inventory_purchase_return.fk_purchase_id','inventory_product_add.id')
            ->leftJoin('inventory_supplier','inventory_product_add.fk_supplier_id','inventory_supplier.id')
            ->select('inventory_purchase_return.*','inventory_product_add.inventory_order_id','inventory_supplier.company_name',DB::raw('@rownum := @rownum - 1 AS sl'));
        return DataTables::of($allData)
            ->addColumn('inventory_order_id','<a href=\'<? echo URL::to("inventory-product-add/$inventory_order_id") ?>\' title="View Browser">{{$inventory_order_id}}</a>')
            ->addColumn('total_amount', '{{round($total_amount,2)}}')
            ->addColumn('total_return', '{{round($total_return,2)}}')
            ->addColumn('action', '
                <a href=\'{{URL::to("inventory-purchase-return-show/$id")}}\' class="btn btn-primary btn-xs" title="view description"><i class="fa fa-eye"></i></a>
                
                {!! Form::open(array(\'route\'=> [\'inventory-purchase-return.destroy\',$id],\'method\'=>\'DELETE\')) !!}
                    <button type="submit" onclick="return confirmDelete();" class="btn btn-xs btn-danger">
                      <i class="fa fa-trash-o" aria-hidden="true"></i>
                    </button>
                {!! Form::close() !!}
                ')

            ->rawColumns(['inventory_order_id','action','sl'])
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
        $clients = InventorySupplier::orderBy('id','desc')->where(['fk_branch_id'=>$branchId,'fk_company_id'=>$companyId])->pluck('company_name','id');
        if(isset($request->id)){
            $client = InventorySupplier::where('id',$request->id)->select('id','representative','company_name','supplier_id','address','mobile_no','email_id')->first();
            $from=date('Y-m-d',strtotime($request->from));
            $to=date('Y-m-d',strtotime($request->to));
            $sales=InventoryProductAdd::whereBetween('date',[$from,$to])->where('fk_supplier_id',$request->id)->get();
        }
        return view('pos.addItem.return.index',compact('clients','sales','client'));
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
            'fk_purchasae_item_id.*' => 'required',
            'qty' => 'required',
            'total_amount' => 'required',
            'cost_per_unit.*' => 'required',
            'payable_amount' => 'required',

        ]);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        $purchase = InventoryProductAdd::findOrFail($request->id);
        $account=1;
        $method=3;
        if(isset($request->payment_id) and $request->payment_id!=null){
            $paymentItem=InventoryOrderPaymentItem::findOrFail($request->payment_id);
            $payment = InventoryOrderPayment::findOrFail($paymentItem->fk_order_payment_id);
            $account=$payment->fk_account_id;
            $method=$payment->fk_method_id;
        }
        $totalAmount = ($purchase->total_amount-$request->total_amount);
        $returnAmount = ($purchase->total_paid-$totalAmount)>0?$purchase->total_paid-$totalAmount:0;
        $return = InventoryPurchaseReturn::create([
            'fk_purchase_id'=>$input['id'],
            'total_amount'=>$purchase->total_amount,
            'total_return'=>$request->total_amount,
            'back_amount'=>$returnAmount,
            'date'=>date('Y-m-d',strtotime($request->date)),
            'fk_account_id'=>$account,
            'fk_method_id'=>$method,
        ])->id;

        $purchase->update([
            'total_amount'=>$totalAmount,
            'total_paid'=>$purchase->total_paid-$returnAmount,
        ]);
        for ($i=0;$i<sizeof($request->fk_purchasae_item_id);$i++){
            if($request->qty[$i]>0){
//           Item return to inventory
                $inventoryItem = InventoryItem::where('id',$request->fk_inventory_item_id[$i])->first();
                $inventoryItem->update([
                    'available_qty'=>$inventoryItem->available_qty-$request->qty[$i],
                ]);

                $purchaseItem = InventoryProductAddItem::where('id',$request->fk_purchasae_item_id[$i])->first();
                $getInventory = Inventory::where('id',$inventoryItem->fk_inventory_id)->first();
                $getInventory->update([
                    'available_qty' => $getInventory->available_qty-$request->qty[$i],
                ]);

//              \Item Return to Inventory

                $purchaseItem->update([
                    'qty'=>$purchaseItem->qty-$request->qty[$i],
                    'payable_amount'=>$purchaseItem->payable_amount-$request->payable_amount[$i],
                ]);

                InventoryPurchaseReturnItem::create([
                    'fk_return_id' => $return,
                    'fk_purchase_item_id' => $request->fk_purchasae_item_id[$i],
                    'qty' => $request->qty[$i],
                    'sub_total' => $request->payable_amount[$i],
                    'inventory_item_id' => $request->fk_inventory_item_id[$i]
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
            return redirect("inventory-purchase-return-show/$return")->with('success','Order Created Successfully.');
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
        $data = InventoryPurchaseReturn::findOrFail($id);
        return view('pos.addItem.return.invoice',compact('data'));
    }
    public function show($id)
    {

        $data = InventoryProductAdd::findOrFail($id);
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

        return view('pos.addItem.return.show', compact('data','payment'));
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
        $data = InventoryPurchaseReturn::findOrFail($id);
        $purchase = InventoryProductAdd::findOrFail($data->fk_purchase_id);
        $purchase->update([
            'total_amount'=>$purchase->total_amount+$data->total_return,
            'total_paid'=>$purchase->total_paid+$data->back_amount,
        ]);
        $items = InventoryPurchaseReturnItem::where('fk_return_id',$id)->get();
        foreach($items as $item){
            $purchasaeItem = InventoryProductAddItem::where('id',$item->fk_purchase_item_id)->first();
            $purchasaeItem->update([
                'qty'=>$purchasaeItem->qty+$item->qty,
                'payable_amount'=>$purchasaeItem->payable_amount+$item->sub_total,
            ]);
//           Item return to inventory
            $inventoryItem = InventoryItem::where('id',$item->inventory_item_id)->first();
            $inventoryItem->update([
                'available_qty'=>$inventoryItem->available_qty+$item->qty,
            ]);


            $getInventory = Inventory::where('id',$inventoryItem->fk_inventory_id)->first();
            $getInventory->update([
                'available_qty' => $getInventory->available_qty+$item->qty,
            ]);

//              \Item Return to Inventory

        }
        InventoryPurchaseReturnItem::where('fk_return_id',$id)->delete();
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
