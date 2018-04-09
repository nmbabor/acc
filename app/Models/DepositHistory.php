<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepositHistory extends Model
{
    protected $table = "deposit_history";
    protected $fillable = ['fk_deposit_id','created_by','invoice_id','last_total_due','total_paid','payment_date','type']; 
    public function items(){
    	return $this->hasMany(DepositHistoryItem::class,'fk_history_id','id');
    }
    public function deposit(){
    	return $this->belongsTo(Deposit::class,'fk_deposit_id','id');
    }
}
