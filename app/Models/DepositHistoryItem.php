<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepositHistoryItem extends Model
{
    protected $table = "deposit_history_item";
    protected $fillable = ['fk_history_id','fk_deposit_item_id','last_due','paid'];

    public function depositItem(){
    	return $this->belongsTo(DepositCostItem::class,'fk_deposit_item_id','id');
    }
}
