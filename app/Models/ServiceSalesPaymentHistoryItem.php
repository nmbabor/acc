<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceSalesPaymentHistoryItem extends Model
{
    protected $table = 'service_sales_payment_history_item';
    protected $fillable = ['fk_payment_id','fk_sales_id','sales_last_due','sales_paid','type'];
    public function payment(){
        return $this->belongsTo(ServiceSalesPaymentHistory::class,'fk_payment_id','id');
    }
    public function sales(){
        return $this->belongsTo(ServiceSales::class,'fk_sales_id','id');
    }
}
