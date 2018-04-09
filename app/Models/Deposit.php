<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $table = "deposit";
    protected $fillable = ['invoice_no','t_date','ref_id','fk_client_id','fk_account_id','fk_method_id','created_by','updated_by','amount','total_paid','fk_company_id','fk_branch_id','loan_paid','fk_loan_id']; 
    public function items(){
        return $this->hasMany(DepositCostItem::class,'fk_deposit_id','id');
    }
    public function history(){
    	return $this->hasMany(DepositHistory::class,'fk_deposit_id','id');
    }

    public function client(){
    	return $this->belongsTo(Clients::class,'fk_client_id','id');
    }
    public function branch(){
    	return $this->belongsTo(InventoryBranch::class,'fk_branch_id','id');
    }
}
