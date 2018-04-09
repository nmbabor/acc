<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class PaymentCostItem extends Model
{
    protected $table = "payment_cost_item";
    protected $fillable = ['fk_payment_id','fk_sub_category_id','description','total_amount','paid_amount','asset_type_id','asset_age']; 
    public function asset(){
        return $this->belongsTo(GeneralAssetType::class,'asset_type_id','id');
    }
    public function subCategory(){
        return $this->belongsTo(SubCategories::class,'fk_sub_category_id','id');
    }
    public function history(){
        return $this->hasOne(PaymentHistoryItem::class,'fk_payment_item_id','id');
    }

    /*check is exists delete item */
    public static function isExistsPaymentItem($id){
        //print_r($id);exit;
        $result = DB::table('payment_cost_item')
        ->where('fk_payment_id', '=', $id)
        ->get();
        return $result;
    }

    /*delete Payment Item*/
    public static function deleteItemId($itemData){
        //print_r($itemData[1]->id);exit;
        for ($i=0; $i <sizeof($itemData); $i++) {
            $result = DB::table('payment_cost_item')
                ->where('id', $itemData[$i]->id)  // find your user by their id
                ->delete();
        }

    }

    /*update Payment amount paid*/
    public static function updatePaidAmount($paymentItemId,$newPaidAmount){
        //print_r($newPaidAmount);exit;
        $updatedItem = DB::table('payment_cost_item')
            ->where('id', $paymentItemId)
            ->update([
                'paid_amount' => $newPaidAmount,
                'updated_at' => date('Y-m-d h:i:s')
            ]);
        return true;
    }
}
