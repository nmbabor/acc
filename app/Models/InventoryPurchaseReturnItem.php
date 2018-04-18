<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryPurchaseReturnItem extends Model
{
    protected $table = 'inventory_purchase_return_item';
    protected $fillable = ['fk_return_id','fk_purchase_item_id','qty','sub_total','inventory_item_id'];

    public function productReturn(){
        return $this->belongsTo(InventoryPurchaseReturn::class,'fk_return_id','id');
    }

    public function items(){
        return $this->belongsTo(InventoryProductAddItem::class,'fk_purchase_item_id','id');
    }
}
