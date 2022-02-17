<?php

namespace App\Http\Controllers\Api;

use App\Enums\LoanStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoanRequest;
use App\Http\Requests\ProcessRequest;
use App\Http\Requests\RepaymentRequest;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    /*
     * Let user apply for the loan with loan type, amount and terms
     *
     * Assumed terms are in weeks
     * */
    public function apply(LoanRequest $request) {

        $loan = Auth::user()->loans()->create($request->validated());

        return response()->json(['status' => true, 'loan' => $loan]);
    }

    /*
     * Show to loan details
     * */
    public function show(Loan $loan) {

        return response()->json(['status' => true, 'loan' => $loan]);
    }

    /*
     * Allow admin to process the loan, and it will calculate payable interest (Calculations are bit wrong, its calculating interest on base amount, not reducing interest as installments are being paid)
     * */
    public function process(ProcessRequest $request, Loan $loan, LoanStatus $status) {

        if ($loan->status !== LoanStatus::Pending->value)
            return response()->json(['status' => false, 'message' => 'Loan has been processed'], 422);

        if ($status === LoanStatus::Approved) {

            $interest_amount = $loan->principal_amount * ($request->interest_rate / 100) * ($loan->terms / 52);
            $loan->update(['status' => LoanStatus::Approved, 'approved_at' => Carbon::today(), 'interest_rate' => $request->interest_rate, 'interest_amount' => $interest_amount]);

            return response()->json(['status' => true, 'message' => 'Loan Approved']);
        }

        $loan->update(['status' => LoanStatus::Rejected]);
        return response()->json(['status' => true, 'message' => 'Loan Rejected']);
    }

    /*
     * Allow users to repay their loan
     *
     * Assumed payment is being made regularly on given date as in tenure list from loan detail API
     * */
    public function repayment(RepaymentRequest $request, Loan $loan) {

        $loan->repayments()->create(['amount' => $request->amount, 'payment_date' => $request->payment_date]);
    }
}
