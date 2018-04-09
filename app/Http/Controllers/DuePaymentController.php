<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\PaymentCostItem;
use App\Models\SubCategories;
use App\Models\Clients;
use App\Models\ProjectItem;
use App\Models\AccountSetting;
use App\Models\PaymentMethod;
use App\Models\PaymentHistory;
use App\Models\PaymentHistoryItem;
use DB;
use Validator;
use Auth;
use Form;
use URL;
use Yajra\DataTables\DataTables;

class DuePaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('payment.historylist');
         
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $query = Payment::leftJoin('clients','payment.fk_client_id','=','clients.id')
         ->leftJoin('inventory_branch','payment.fk_branch_id','=','inventory_branch.id')
        ->leftJoin('account','payment.fk_account_id','=','account.id')
        ->leftJoin('payment_method','payment.fk_method_id','=','payment_method.id')
        ->whereColumn('payment.amount', '>', 'payment.total_paid')
        ->select('payment.*','account.account_name','clients.client_name','payment_method.method_name','branch_name')
        ->orderBy('id','DESC');
        if(Auth::user()->isRole('administrator')){
            $getDuePayment=$query->get();
        }else{
            $getDuePayment=$query->where(['payment.fk_branch_id'=>Auth::user()->fk_branch_id,'payment.fk_company_id'=>Auth::user()->fk_company_id])->get();

        }
        //return $getDuePayment;

        return view('payment.viewDuePayments', compact('getDuePayment'));
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
        $query = PaymentHistory::
        leftJoin('payment','payment_history.fk_payment_id','=','payment.id')
        ->leftJoin('clients','payment.fk_client_id','=','clients.id')
        ->leftJoin('inventory_branch','clients.fk_branch_id','=','inventory_branch.id')
        ->select('payment_history.*','payment.invoice_no','clients.client_name','branch_name')
        ->orderBy('payment.id','desc');
        if(Auth::user()->isRole('administrator')){
            $allData=$query;
        }else{
            $allData=$query->where(['payment.fk_branch_id'=>Auth::user()->fk_branch_id,'payment.fk_company_id'=>Auth::user()->fk_company_id]);
        }
        
        return Datatables::of($allData)
        ->editColumn('invoice_id','
            <a href="{{URL::to(\'payment/\')}}/{{$invoice_id}}">{{$invoice_id}}</a>
            ')
        ->editColumn('invoice_no','
            <a href="{{URL::to(\'payment/\')}}/{{$invoice_no}}">{{$invoice_no}}</a>
            ')
        ->addColumn('due','
            {{$last_total_due-$total_paid}}
            ')
        ->addColumn('action',function($payment){
           
            $result = '';
            
              $result .=Form::open(array('route'=> ['due-payment.destroy',$payment->id],'method'=>'DELETE')).Form::hidden('id',$payment->id).'
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
        $getPaymentData =Payment::leftJoin('clients','payment.fk_client_id','=','clients.id')
        ->leftJoin('account','payment.fk_account_id','=','account.id')
        ->leftJoin('payment_method','payment.fk_method_id','=','payment_method.id')
        ->select('payment.*','account.account_name','clients.client_name','payment_method.method_name')
        ->where('payment.id',$id)
        ->first();

        
        $getDuePaymentData = PaymentCostItem::
        leftJoin('sub_category','payment_cost_item.fk_sub_category_id','=','sub_category.id')
        ->where('fk_payment_id',$id)
        ->whereColumn('total_amount', '>', 'paid_amount')
        ->select('payment_cost_item.*','sub_category.sub_category_name')
        ->get();

        return view('payment.singleDuePaymentEdit', compact('getPaymentData','getDuePaymentData'));
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
        $getPaymentData = Payment::findOrFail($id);
        $validator = Validator::make($request->all(),[
            't_date' => 'required'
        ]);

        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $input = $request->all();
         $lastId=PaymentHistory::max('id')+1;
        $input['invoice_no'] = date('ymd').$lastId;
            $historyId = PaymentHistory::create([
                'fk_payment_id'=>$id,
                'created_by'=>Auth::user()->id,
                'invoice_id'=>$input['invoice_no'],
                'last_total_due'=>$input['last_total_due'],
                'total_paid'=>$input['total_paid'],
                'payment_date'=>date('Y-m-d',strtotime($request->t_date)),
                'type'=>2
            ])->id;
            //print_r($input['paid']);exit;
            $paymentExistsId = sizeof($input['payment_item_old_id']);
            for ($i=0; $i < $paymentExistsId; $i++) { 
                $paymentItemId = $input['payment_item_old_id'][$i];
                $existsPaid = PaymentCostItem::findOrFail($paymentItemId);
                $newPaidAmount = intval($existsPaid->paid_amount)+intval($input['paid'][$i]);
                $existsPaid->update([
                    'paid_amount'=>$newPaidAmount
                ]);
                PaymentHistoryItem::create([
                     'fk_history_id'=>$historyId,
                    'fk_payment_item_id'=>$paymentItemId,
                    'last_due'=>$input['last_due'][$i],
                    'paid'=>$input['paid'][$i],
                    ]);
                //return $newPaidAmount;
            }
            $getPaymentData->update([
                'total_paid'=>$getPaymentData->total_paid+$input['total_paid'],
                ]);
        try {
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->errorInfo[1];
            $bug1 = $e->errorInfo[2];
        }
       
        if($bug == 0){
            return redirect("payment/".$input['invoice_no']);
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
