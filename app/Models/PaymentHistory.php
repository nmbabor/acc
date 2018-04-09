<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    protected $table = "payment_history";
    protected $fillable = ['fk_payment_id','created_by','invoice_id','last_total_due','total_paid','payment_date','type']; 
    public function items(){
    	return $this->hasMany(PaymentHistoryItem::class,'fk_history_id','id');
    }
    public function payment(){
    	return $this->belongsTo(Payment::class,'fk_payment_id','id');
    }
}
