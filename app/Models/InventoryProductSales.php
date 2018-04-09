<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryProductSales extends Model
{
    protected $table = 'inventory_product_sales';
    protected $fillable = ['fk_client_id','summary','invoice_id','date','total_amount','discount','paid_amount','created_by','shipping_address','order_id','type','shipping_date','fk_user_id','fk_branch_id','fk_company_id','sales_type','transport_bill','sales_type','prev_amount','prev_paid'];

    public function client(){
        return $this->belongsTo(InventoryClient::class,'fk_client_id','id');
    }
    public function items(){
        return $this->hasMany(InventoryProductSalesItem::class,'fk_sales_id','id');
    }
    public function paymentItem(){
        return $this->hasMany(InventorySalesPaymentHistoryItem::class,'fk_sales_id','id');
    }
}
