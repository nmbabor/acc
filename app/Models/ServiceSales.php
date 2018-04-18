<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceSales extends Model
{
    protected $table = 'service_sales';
    protected $fillable = ['fk_client_id','summary','invoice_id','date','total_amount','discount','paid_amount','created_by'];


    public function client(){
    	return $this->belongsTo('App\Models\InventoryClient','fk_client_id','id');
    }
    public function items(){
        return $this->hasMany(ServiceSalesItem::class,'fk_sales_id','id');
    }
    public function payment(){
        return $this->hasMany(ServiceSalesPaymentHistoryItem::class,'fk_sales_id','id');
    }
}
