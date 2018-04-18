<?php

namespace App\Providers;

use App\Models\InventoryBranch;
use App\Models\InventoryProductSales;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class CartServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer([
            '_partials.cartScript',
        ], function ($view) {



            $branch= InventoryBranch::where('status',1)->pluck('branch_name','id');
            foreach($branch as $id => $name){
                $branchSales[]=InventoryProductSales::whereYear('date',date('Y'))->where('fk_branch_id',$id)->select(DB::raw('SUM(total_amount) as total_amount'))->value('total_amount');
                $branchSales2[]=InventoryProductSales::whereYear('date',date('Y'))->whereMonth('date',date('m'))->where('fk_branch_id',$id)->select(DB::raw('SUM(total_amount) as total_amount'))->value('total_amount');
                $branches[]=$name;
            }
            $total = array_sum($branchSales2);
            foreach($branchSales2 as $new){
                if($new>0){
                    $newSales[]=100/($total/$new);
                }else{
                    $newSales[]=0;
                }
            }
            $branches = json_encode($branches,true);
            $branchSales = json_encode($branchSales,true);
            $branchSales2 = json_encode($branchSales2,true);

            $view->with(['branchSales'=>$branchSales,'branchSales2'=>$branchSales2,'branches'=>$branches]);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
