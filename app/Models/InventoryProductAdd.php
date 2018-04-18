<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryProductAdd extends Model
{
    protected $table = 'inventory_product_add';
    protected $fillable = ['fk_supplier_id','inventory_order_id','total_amount','summery','created_by','updated_by','status','date','total_paid','challan_id','fk_account_id','fk_method_id','ref_id'];
    public function supplier(){
        return $this->belongsTo(InventorySupplier::class,'fk_supplier_id','id');
    }
    public function items(){
        return $this->hasMany(InventoryProductAddItem::class,'fk_product_add_id','id');
    }
    public function paymentItem(){
        return $this->hasMany(InventoryOrderPaymentItem::class,'fk_order_id','id');
    }
}
