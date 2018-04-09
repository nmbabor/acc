<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = "payment";
    protected $fillable = ['invoice_no','t_date','ref_id','fk_client_id','fk_account_id','fk_method_id','amount','total_paid','created_by','updated_by','fk_company_id','fk_branch_id','fk_loan_id','loan_paid']; 
    public function items(){
    	return $this->hasMany(PaymentCostItem::class,'fk_payment_id','id');
    }
    public function client(){
    	return $this->belongsTo(Clients::class,'fk_client_id','id');
    }
    public function branch(){
    	return $this->belongsTo(InventoryBranch::class,'fk_branch_id','id');
    }

}
