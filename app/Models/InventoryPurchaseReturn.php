<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryPurchaseReturn extends Model
{
    protected $table = 'inventory_purchase_return';
    protected $fillable = ['fk_purchase_id','total_amount','total_return','back_amount','fk_account_id','fk_method_id','date'];
    public function items(){
        return $this->hasMany(InventoryPurchaseReturnItem::class,'fk_return_id','id');
    }
    public function purchase(){
        return $this->belongsTo(InventoryProductAdd::class,'fk_purchase_id','id');
    }

}
