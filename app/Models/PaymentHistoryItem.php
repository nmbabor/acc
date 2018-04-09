<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentHistoryItem extends Model
{
     protected $table = "payment_history_item";
    protected $fillable = ['fk_history_id','fk_payment_item_id','last_due','paid'];

    public function paymentItem(){
    	return $this->belongsTo(PaymentCostItem::class,'fk_payment_item_id','id');
    }
}
