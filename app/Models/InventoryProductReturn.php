<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryProductReturn extends Model
{
    protected $table = 'inventory_product_return';
    protected $fillable = ['fk_sales_id','total_amount','total_return','back_amount','fk_account_id','fk_method_id','date'];
    public function items(){
        return $this->hasMany(InventoryProductReturnItem::class,'fk_return_id','id');
    }
    public function sales(){
        return $this->belongsTo(InventoryProductSales::class,'fk_sales_id','id');
    }
}
