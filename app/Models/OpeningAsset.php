<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpeningAsset extends Model
{
    protected $table = "opening_assets";
    protected $fillable =['fk_sub_category_id','description','total_amount','current_amount','asset_type_id','asset_age','created_by','fk_company_id','fk_branch_id','date'];
}
