<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryProductReturn extends Model
{
    protected $table = 'inventory_product_return';
    protected $fillable = ['fk_sales_id','total_amount'];
    public function items(){
        return $this->hasMany(InventoryProductReturnItem::class,'fk_return_id','id');
    }
}
