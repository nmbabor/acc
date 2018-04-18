<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class ServiceSalesPaymentHistory extends Model
{
    protected $table = 'service_sales_payment_history';
    protected $fillable = ['invoice_id','total_amount','last_due','paid','payment_date','created_by','fk_account_id','fk_method_id','ref_id','fk_received_id','fk_client_id','fk_branch_id','fk_company_id','type'];

    public function client(){
        return $this->belongsTo(InventoryClient::class,'fk_client_id','id');
    }
    public function user(){
        return $this->belongsTo(User::class,'created_by','id');
    }
    public function items(){
        return $this->hasMany(ServiceSalesPaymentHistoryItem::class,'fk_payment_id','id');
    }
}
