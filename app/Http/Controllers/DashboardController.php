<?php

namespace App\Http\Controllers;

use App\Models\InventoryProductSales;
use App\Models\InventoryProductSalesItem;
use App\Models\InventorySalesChallanItem;
use App\Models\PaymentHistory;
use App\Models\PaymentHistoryItem;
use Illuminate\Http\Request;

use App\Models\CompanyInfo;
use App\Models\EmailConfig;
use App\Models\TermsCondition;
use App\Models\InventoryBranch;
use App\Models\InventoryOrderPayment;
use App\Models\InventoryOrderPaymentItem;
use App\Http\Requests;
use Validator;
use Response;
use DB;
use Auth;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource article table.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $companyInfo = CompanyInfo::first();
        $emailConfig = EmailConfig::first();
        $condition = TermsCondition::first();
        $branch=InventoryBranch::where('status',1)->pluck('branch_name','id');

        if(isset($request->start) and isset($request->end)){
            $start = $request->start;
            $start = strtotime($start);
            $end = $request->end;
            $end = strtotime($end);
            if($start>$end){
                return redirect()->back()->with('error','Date select is not correct!');
            }
            for ($i=$start; $i<=$end; $i+=86400) {
                $date  = date("Y-m-d", $i);
                $sales[] = InventoryProductSales::whereDate('date',$date)->select(DB::raw('SUM(total_amount) as total_amount'))->value('total_amount');
                $dates[] =date('M jS',strtotime($date));
            }

        }else{
            $days = 14;
            for ($i=$days;$i>=0;$i--){
                $date = date('Y-m-d', strtotime('today - '.$i.' days'));
                $sales[] = InventoryProductSales::whereDate('date',$date)->select(DB::raw('SUM(total_amount) as total_amount'))->value('total_amount');
                $dates[] =date('M jS',strtotime($date));
            }
        }
        $dates = json_encode($dates,true);
        $sales = json_encode($sales,true);
        return view('index' ,compact('companyInfo','emailConfig','condition','branch','sales','dates'));

        
    }
     public function databaseTable(){
        $tables = DB::select('SHOW TABLES');
        foreach ($tables as $table) {
            foreach ($table as $key => $value)
                $accounting[]=$value;       
           DB::statement('ALTER TABLE ' . $value . ' ENGINE = InnoDB'); 
        }
            return $accounting;
    }
    public function branch($id){
        Auth::user()->update(['fk_branch_id'=>$id]);
        return redirect()->back();
    }

    public function allTable(){
        $tables = DB::select('SHOW TABLES');
        foreach ($tables as $k => $table) {
            foreach ($table as $key => $value)
                    $allData[$k]['table']=$value;
                    $allData[$k]['row']=DB::table("$value")->count();
        }
        return view('truncate',compact('allData'));
    }
    public function truncateTable($table){


        try {
            DB::statement('DELETE FROM ' . $table); 
            DB::statement('ALTER TABLE ' . $table . ' AUTO_INCREMENT = 1'); 
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->errorInfo[1];
            $bug1 = $e->errorInfo[2];
        }
        
        if($bug == 0){
            return redirect()->back()->with('success','SuccessFully Truncate.');
        }else{
            return redirect()->back()->with('error','Error: '.$bug1);
        }
    }


    public function productOrder(){
        $all=InventoryOrderPaymentItem::leftJoin('inventory_product_add','inventory_order_payment_item.fk_order_id','inventory_product_add.id')->leftJoin('inventory_order_payment','inventory_order_payment_item.fk_order_payment_id','inventory_order_payment.id')->select('inventory_order_payment_item.fk_order_payment_id','inventory_product_add.fk_supplier_id')->where('inventory_order_payment.fk_supplier_id','=',null)->groupBy('inventory_order_payment_item.fk_order_payment_id')->get();
        foreach($all as $d){
            InventoryOrderPayment::where('id',$d->fk_order_payment_id)->update([
                'fk_supplier_id'=>$d->fk_supplier_id,
            ]);
            
        }
        return $all;
    }
    public function costPrice(){
        $allData=InventoryProductSalesItem::where('cost_amount',null)->get();
        foreach($allData as $data){
            $amount = InventorySalesChallanItem::where('fk_sales_item_id',$data->id)->value('cost_amount');
            InventoryProductSalesItem::where('id',$data->id)->update(['cost_amount'=>$amount]);
        }
        return "Complete Sir";
    }
    public function historyItem(){
        $allData = PaymentHistory::get();
        foreach ($allData as $item) {
            PaymentHistoryItem::create([
                'fk_history_id'=>$item->id,
                'fk_payment_item_id'=>$item->fk_payment_id,
                'last_due'=>$item->last_total_due,
                'paid'=>$item->total_paid,
            ]);
        }
        return "Complete sir";
    }
    public function paymentHistory(){
        $allData = PaymentHistory::leftJoin('payment_cost_item','payment_history.fk_payment_item_id','payment_cost_item.id')->select('payment_history.id as id','payment_cost_item.fk_payment_id as payment_id',DB::raw('SUM(last_total_due) as last_total_due'),DB::raw('SUM(total_paid) as total_paid'))->groupBy('payment_history.fk_payment_id')->get();
        foreach ($allData as $item) {
            PaymentHistory::where('id',$item->id)->update([
                'total_paid'=>$item->total_paid,
                'last_total_due'=>$item->last_total_due,
            ]);
        }
        return "Complete sir";
    }
}
