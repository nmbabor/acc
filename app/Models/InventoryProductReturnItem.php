<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryProductReturnItem extends Model
{
    protected $table = 'inventory_product_return_item';
    protected $fillable = ['fk_return_id','fk_sales_item_id','qty','sub_total'];

    public function productReturn(){
        return $this->belongsTo(InventoryProductReturn::class,'fk_return_id','id');
    }

    public function items(){
        return $this->belongsTo(InventoryProductSalesItem::class,'fk_sales_item_id','id');
    }

}
