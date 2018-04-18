<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceSalesItem extends Model
{
    protected $table = 'service_sales_item';
    protected $fillable = ['fk_sales_id','fk_service_id','product_price_amount','product_wise_discount','product_paid_amount','qty'];
    public function service(){
        return $this->belongsTo(Services::class,'fk_service_id','id');
    }
    public function sales(){
        return $this->belongsTo(ServiceSales::class,'fk_sales_id','id');
    }
}
