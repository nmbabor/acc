<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\GeneralAssetType;
use App\Models\SubCategories;
use App\Models\OpeningAsset;
use DB;
use Validator;
use Auth;

class OpeningAssetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('payment.openingAsset.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        $subCategories= SubCategories::leftJoin('general_asset_type','asset_type_id','general_asset_type.id')->where('sub_category.status',1)->where('general_asset_type.type',1)->pluck('sub_category_name','sub_category.id');
        return view('payment.openingAsset.create',compact('subCategories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->except('_token');
        $validator = Validator::make($request->all(),[
                'fk_sub_category_id.*' => 'required',
                'date' => 'required'

        ]);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }
         try {
            for ($i=0; $i < sizeof($request->fk_sub_category_id); $i++) { 
                $asset_type_id = SubCategories::where('id',$request->fk_sub_category_id[$i])->value('asset_type_id');
                OpeningAsset::create([
                    'fk_sub_category_id'=>$request->fk_sub_category_id[$i],
                    'description'=>$request->description[$i],
                    'total_amount'=>$request->total_amount[$i],
                    'current_amount'=>$request->current_amount[$i],
                    'asset_type_id'=>$asset_type_id,
                    'asset_age'=>$request->asset_age[$i],
                    'date'=>date('Y-m-d',strtotime($request->date)),
                    'created_by'=>Auth::user()->id,
                    'fk_branch_id'=>Auth::user()->fk_branch_id,
                    'fk_company_id'=>Auth::user()->fk_company_id,

                ]);
            }
            $bug=0;
        } catch (\Exception $e) {
            $bug = $e->errorInfo[1];
            $bug1 = $e->errorInfo[2];
        }
       
        if($bug == 0){
            return redirect()->back()->with('success','Asset Added Successfully.');
        }else{
            return redirect()->back()->with('error','Something Error Found !, Please try again.'.$bug1);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
         DB::statement(DB::raw('set @rownum=0'));
        $allData = OpeningAsset::leftJoin('inventory_branch','fk_branch_id','inventory_branch.id')
                ->leftJoin('sub_category','fk_sub_category_id','sub_category.id')
                ->select('opening_assets.*','sub_category_name','branch_name',DB::raw('@rownum := @rownum + 1 AS sl'));
        return DataTables::of($allData)
                
                ->addColumn('action','
                    
                    {!! Form::open(array("route"=> ["opening-asset.destroy",$id],"method"=>"DELETE")) !!}
                    {{ Form::hidden("id",$id)}}
                    <button type="submit" onclick="return confirmDelete();" class="btn btn-xs btn-danger">
                      <i class="fa fa-trash-o" aria-hidden="true"></i>
                    </button>
                {!! Form::close() !!}
                    ') 
                ->rawColumns(['action'])
                ->make(true);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
        $data = OpeningAsset::findOrFail($id);
            $data->delete();
            $bug=0;
        } catch (\Exception $e) {
            $bug = $e->errorInfo[1];
            $bug1 = $e->errorInfo[2];
        }
       
        if($bug == 0){
            return redirect()->back()->with('success','Asset Deleted Successfully.');
        }else{
            return redirect()->back()->with('error','Something Error Found !, Please try again.'.$bug1);
        }
    }
}
