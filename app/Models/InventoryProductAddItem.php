<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryProductAddItem extends Model
{
    protected $table = 'inventory_product_add_item';
    protected $fillable = ['fk_product_add_id','fk_product_id','qty','cost_per_unit','sales_per_unit','payable_amount','fk_inventory_id','fk_model_id','fk_branch_id'];

    public function product(){
        return $this->belongsTo(InventoryProduct::class,'fk_product_id','id');
    }
    public function model(){
        return $this->belongsTo(InventoryProductModel::class,'fk_model_id','id');
    }

    
}
