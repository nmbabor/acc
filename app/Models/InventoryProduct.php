<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryProduct extends Model
{
    protected $table = 'inventory_product';
    protected $fillable = ['fk_category_id','fk_brand_id','fk_small_unit_id','product_id','product_name','specification','type','stock_limitation','summery','status','created_by','updated_by'];
    public function category(){
        return $this->belongsTo(InventoryCategories::class,'fk_category_id','id');
    }
    public function unit(){
        return $this->belongsTo(InventorySmallUnit::class,'fk_small_unit_id','id');
    }
}
