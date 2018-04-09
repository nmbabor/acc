<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deposit;
use App\Models\Payment;
use App\Models\Clients;
use App\Models\DepositHistory;
use DB;
use Auth;
use URL;
use Form;
use App\Models\DepositCostItem;
use App\Models\PaymentCostItem;
use Yajra\DataTables\DataTables;

class BankLoanController extends Controller
{

   public function payable(){
      
      return view('deposit.loan.payable');

    }
    /*Load Payable loan by datatables*/
    public function payableAll(){
      $query = DepositCostItem::leftJoin('deposit','fk_deposit_id','deposit.id')
      ->leftJoin('sub_category','fk_sub_category_id','sub_category.id')
      ->leftJoin('general_asset_type','asset_type_id','general_asset_type.id')
      ->leftJoin('clients','deposit.fk_client_id','=','clients.id')
      ->leftJoin('inventory_branch','deposit.fk_branch_id','=','inventory_branch.id')
      ->select('deposit.*','clients.client_name','branch_name',DB::raw('(total_paid - loan_paid) as loan_due'))
      ->where('general_asset_type.type',2)
      ->orderByRaw('(total_paid - loan_paid) DESC');
        if(Auth::user()->isRole('administrator')){
            $allData=$query;
        }else{
            $allData=$query->where(['deposit.fk_branch_id'=>Auth::user()->fk_branch_id,'deposit.fk_company_id'=>Auth::user()->fk_company_id]);
        }
        return Datatables::of($allData)
        ->editColumn('invoice_no','
            <a href="{{URL::to(\'deposit/\')}}/{{$invoice_no}}">{{$invoice_no}}</a>
            ')
        ->addColumn('due','
            {{$amount-$total_paid-$loan_paid}}
            ')
        ->addColumn('action',function($deposit){
           if($deposit->total_paid>$deposit->loan_paid){
            return '<a class="btn btn-xs btn-warning" href="'.URL::to('payment/create').'">Due</a>';
           }else{
            return '<button class="btn btn-xs btn-success">Paid</button>';
           }

        })
        ->rawColumns(['action','invoice_no'])
        ->make(true);

    }
    public function receivable(){
      
      return view('payment.loan.receivable');

    }
    /*Load receivable loan by datatables*/
    public function receivableAll(){
   		$query = PaymentCostItem::leftJoin('payment','fk_payment_id','payment.id')
   		->leftJoin('sub_category','fk_sub_category_id','sub_category.id')
   		->leftJoin('general_asset_type','sub_category.asset_type_id','general_asset_type.id')
   		->leftJoin('clients','payment.fk_client_id','=','clients.id')
      ->leftJoin('inventory_branch','payment.fk_branch_id','=','inventory_branch.id')
      ->select('payment.*','clients.client_name','branch_name',DB::raw('(total_paid - loan_paid) as loan_due'))
      ->where('general_asset_type.type',2)
      ->orderByRaw('(total_paid - loan_paid) DESC');
        if(Auth::user()->isRole('administrator')){
            $allData=$query;
        }else{
            $allData=$query->where(['payment.fk_branch_id'=>Auth::user()->fk_branch_id,'payment.fk_company_id'=>Auth::user()->fk_company_id]);
        }
        return Datatables::of($allData)
        ->editColumn('invoice_no','
            <a href="{{URL::to(\'payment/\')}}/{{$invoice_no}}">{{$invoice_no}}</a>
            ')
        ->addColumn('due','
            {{$amount-$total_paid-$loan_paid}}
            ')
        ->addColumn('action',function($payment){
           if($payment->total_paid>$payment->loan_paid){
            return '<a class="btn btn-xs btn-warning" href="'.URL::to('deposit/create').'">Due</a>';
           }else{
            return '<button class="btn btn-xs btn-success">Paid</button>';
           }

        })
        ->rawColumns(['action','invoice_no'])
        ->make(true);

    }



    public function payableId(Request $request){
      
      $allData = DepositCostItem::leftJoin('deposit','fk_deposit_id','deposit.id')
      ->leftJoin('sub_category','fk_sub_category_id','sub_category.id')
      ->leftJoin('general_asset_type','asset_type_id','general_asset_type.id')
      ->select('deposit.id','deposit.amount','deposit.total_paid','deposit.invoice_no')
      ->where(['general_asset_type.type'=>2])
      ->whereColumn('deposit.total_paid','>','deposit.loan_paid');
      if(isset($request->bank) and $request->bank!=null){
        $allData=$allData->where('deposit.fk_client_id',$request->bank);
      }
      $data=$allData->pluck('invoice_no','id');
      return view('deposit.loan.payableLoan',compact('data'));
    }

    public function receivableId(Request $request){
    	
    	$allData = PaymentCostItem::leftJoin('payment','fk_payment_id','payment.id')
   		->leftJoin('sub_category','fk_sub_category_id','sub_category.id')
   		->leftJoin('general_asset_type','sub_category.asset_type_id','general_asset_type.id')
   		->select('payment.id','payment.amount','payment.total_paid','payment.invoice_no')
   		->where(['general_asset_type.type'=>2])
   		->whereColumn('payment.total_paid','>','payment.loan_paid');
   		if(isset($request->bank) and $request->bank!=null){
   			$allData=$allData->where('payment.fk_client_id',$request->bank);
   		}
   		$data=$allData->pluck('invoice_no','id');
   		return view('deposit.loan.payableLoan',compact('data'));
    }

    public function payableLoanCheck($id){
      $data = DepositCostItem::leftJoin('deposit','fk_deposit_id','deposit.id')
      ->leftJoin('sub_category','fk_sub_category_id','sub_category.id')
      ->leftJoin('general_asset_type','asset_type_id','general_asset_type.id')
      ->select('deposit.id','deposit.amount','deposit.total_paid')
      ->where(['general_asset_type.type'=>2,'deposit.id'=>$id])->first();
      if($data!=null){
        $paid = Payment::where('fk_loan_id',$data->id)->select(DB::raw('SUM(total_paid) as total_paid'))->value('total_paid');
        return [$data->total_paid,$paid];
      }
      return ['',''];

    }
     public function receivableLoanCheck($id){
    	$data = PaymentCostItem::leftJoin('payment','fk_payment_id','payment.id')
   		->leftJoin('sub_category','fk_sub_category_id','sub_category.id')
   		->leftJoin('general_asset_type','sub_category.asset_type_id','general_asset_type.id')
   		->select('payment.id','payment.amount','payment.total_paid')
   		->where(['general_asset_type.type'=>2,'payment.id'=>$id])->first();
   		if($data!=null){
   			$paid = Deposit::where('fk_loan_id',$data->id)->select(DB::raw('SUM(total_paid) as total_paid'))->value('total_paid');
   			return [$data->total_paid,$paid];
   		}
   		return ['',''];

    }
}
