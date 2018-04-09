<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventorySalesPaymentHistory;
use App\Models\InventoryOrderPayment;
use App\Models\EmployeSalarySheet;
use App\Models\Deposit;
use App\Models\Payment;
use App\Models\BillTransaction;
use App\Models\AccountSetting;
use DB;

class HelperController extends Controller
{
    public static function account($id)
    {
        $result = new \stdClass();
        $account = AccountSetting::select('id','account_name','current_balance')->where(['account_status' => 1,'id' => $id])->first();
        if($account==null){
            $result->name = '';
            $result->balance = '';
            return $result;
        }
        $purchaseAccount =InventoryOrderPayment::where('fk_account_id', $id)->select(DB::raw('SUM(paid) as total_paid'))
            ->value('total_paid');
        $salesAccount = InventorySalesPaymentHistory::select(DB::raw('SUM(paid) as total_paid'))
            ->where('fk_account_id', $id)
            ->value('total_paid');
        $salaryAccount = EmployeSalarySheet::select(DB::raw('SUM(paid_amount) as paid_amount'))
            ->where('fk_account_id', $id)
            ->value('paid_amount');
        $depositAccount = Deposit::select(DB::raw('SUM(deposit.total_paid) as total_paid'))
            ->where('deposit.fk_account_id', $id)
            ->value('total_paid');
        $paymentAccount = Payment::select(DB::raw('SUM(payment.total_paid) as total_paid'))
            ->where('payment.fk_account_id', $id)
            ->value('total_paid');
        $accountTransfer = BillTransaction::select(DB::raw('SUM(amount) as total'))
            ->where('from_account', $id)
            ->value('total');
        $accountRecieve = BillTransaction::select(DB::raw('SUM(amount) as total'))
            ->where('to_account', $id)
            ->value('total');
        $total = $account->current_balance + $depositAccount + $accountRecieve + $salesAccount;
        $expense = $paymentAccount + $accountTransfer + $salaryAccount + $purchaseAccount;
        $balance = $total - $expense;

        $result->name = $account->account_name;
        $result->balance = $balance;

        return $result;

    }
}
