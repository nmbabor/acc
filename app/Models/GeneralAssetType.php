<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralAssetType extends Model
{
    protected $table = 'general_asset_type';
    protected $fillable = ['name','type','status'];
}
