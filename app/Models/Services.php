<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    protected $table = 'services';
    protected $fillable = ['fk_category_id','product_id','product_name','specification','type','summery','status','created_by','updated_by','sales_price'];
}
