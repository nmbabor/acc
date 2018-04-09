<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deposit;
use App\Models\DepositCostItem;
use App\Models\SubCategories;
use App\Models\Clients;
use App\Models\ProjectItem;
use App\Models\AccountSetting;
use App\Models\PaymentMethod;
use App\Models\DepositHistory;
use App\Models\DepositHistoryItem;
use DB;
use Validator;
use Auth;
use URL;
use Form;
use Yajra\DataTables\DataTables;

class DueDepositController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        return view('deposit.historylist');
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $query = Deposit::leftJoin('clients','deposit.fk_client_id','=','clients.id')
        ->leftJoin('inventory_branch','deposit.fk_branch_id','=','inventory_branch.id')
        ->leftJoin('account','deposit.fk_account_id','=','account.id')
        ->leftJoin('payment_method','deposit.fk_method_id','=','payment_method.id')
        ->whereColumn('deposit.amount', '>', 'deposit.total_paid')
        ->select('deposit.*','account.account_name','clients.client_name','payment_method.method_name','branch_name')
        ->orderBy('id','DESC');
        if(Auth::user()->isRole('administrator')){
            $getDueDeposit=$query->get();
        }else{
            $getDueDeposit=$query->where(['deposit.fk_branch_id'=>Auth::user()->fk_branch_id,'deposit.fk_company_id'=>Auth::user()->fk_company_id])->get();

        }
        //return $getDueDeposit;

        return view('deposit.viewDueDeposits', compact('getDueDeposit'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $query = DepositHistory::
        leftJoin('deposit','deposit_history.fk_deposit_id','=','deposit.id')
        ->leftJoin('clients','deposit.fk_client_id','=','clients.id')
        ->leftJoin('inventory_branch','clients.fk_branch_id','=','inventory_branch.id')
        ->select('deposit_history.*','deposit.invoice_no','clients.client_name','branch_name')
        ->orderBy('deposit.id','desc');
        if(Auth::user()->isRole('administrator')){
            $allData=$query;
        }else{
            $allData=$query->where(['deposit.fk_branch_id'=>Auth::user()->fk_branch_id,'deposit.fk_company_id'=>Auth::user()->fk_company_id]);
        }
        
        return Datatables::of($allData)
        ->editColumn('invoice_id','
            <a href="{{URL::to(\'deposit/\')}}/{{$invoice_id}}">{{$invoice_id}}</a>
            ')
        ->editColumn('invoice_no','
            <a href="{{URL::to(\'deposit/\')}}/{{$invoice_no}}">{{$invoice_no}}</a>
            ')
        ->addColumn('due','
            {{$last_total_due-$total_paid}}
            ')
        ->addColumn('action',function($deposit){
           
            $result = '';
            
              $result .=Form::open(array('route'=> ['due-deposit.destroy',$deposit->id],'method'=>'DELETE')).Form::hidden('id',$deposit->id).'
                        <button type="submit" onclick="return confirmDelete();" class="btn btn-xs btn-danger">
                          <i class="fa fa-trash-o" aria-hidden="true"></i>
                        </button>'.
               Form::close();
            return $result;

        })
        ->rawColumns(['action','invoice_id','invoice_no'])
        ->make(true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        

        $getDepositData =Deposit::leftJoin('clients','deposit.fk_client_id','=','clients.id')
        ->leftJoin('account','deposit.fk_account_id','=','account.id')
        ->leftJoin('payment_method','deposit.fk_method_id','=','payment_method.id')
        ->select('deposit.*','account.account_name','clients.client_name','payment_method.method_name')
        ->where('deposit.id',$id)
        ->first();

        
        $getDueDepositData = DepositCostItem::
        leftJoin('sub_category','deposit_cost_item.fk_sub_category_id','=','sub_category.id')
        ->where('fk_deposit_id',$id)
        ->whereColumn('total_amount', '>', 'paid_amount')
        ->select('deposit_cost_item.*','sub_category.sub_category_name')
        ->get();

        

        return view('deposit.singleDueDepositEdit', compact('getDepositData','getDueDepositData'));
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
        $getDepositData = Deposit::findOrFail($id);
        $validator = Validator::make($request->all(),[
            't_date' => 'required'
        ]);

        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $input = $request->except('_method','_token');
        $lastId=DepositHistory::max('id')+1;
        $input['invoice_no'] = date('ymd').$lastId;
        try {
            //print_r($input['paid']);exit;
             $historyId = DepositHistory::create([
                'fk_deposit_id'=>$id,
                'created_by'=>Auth::user()->id,
                'invoice_id'=>$input['invoice_no'],
                'last_total_due'=>$input['last_total_due'],
                'total_paid'=>$input['total_paid'],
                'payment_date'=>date('Y-m-d',strtotime($request->t_date)),
                'type'=>2
            ])->id;
            $depositExistsId = sizeof($input['deposit_item_old_id']);
            for ($i=0; $i < $depositExistsId; $i++) { 
                $depositItemId = $input['deposit_item_old_id'][$i];
                $existsPaid = DepositCostItem::findOrFail($depositItemId);
                $newPaidAmount = $existsPaid->paid_amount+$input['paid'][$i];
                $existsPaid->update([
                    'paid_amount'=>$newPaidAmount
                ]);
                DepositHistoryItem::create([
                    'fk_history_id'=>$historyId,
                    'fk_deposit_item_id'=>$depositItemId,
                    'last_due'=>$input['last_due'][$i],
                    'paid'=>$input['paid'][$i],
                ]);
            }
            $getDepositData->update([
                'total_paid'=>$getDepositData->total_paid+$input['total_paid'],
                ]);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->errorInfo[1];
            $bug1 = $e->errorInfo[2];
        }
       
        if($bug == 0){
            return redirect("deposit/".$input['invoice_no']);
        }else{
            return redirect()->back()->with('error','Something Error Found !, Please try again.'.$bug1);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
