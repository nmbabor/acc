<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategories extends Model
{
    protected $table = 'sub_category';
    protected $fillable = ['sub_category_name','type','status','created_by','asset_type_id'];
    
    public function asset(){
        return $this->belongsTo(GeneralAssetType::class,'asset_type_id','id');
    }
}
