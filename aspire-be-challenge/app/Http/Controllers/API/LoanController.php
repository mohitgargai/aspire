<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanRepayment;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function createLoan(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|regex:/^\d{1,6}(\.\d{2})?$/',
            'term' => 'required|numeric|min:1|max:100',
        ]);

        $loan = Loan::create([
            'user_id' => Auth::user()->id,
            'amount' => $request->amount,
            'term' => $request->term,
        ]);

        return response()->json([
            'data' => [
                'loan_id' => $loan->id,
            ]
        ]);
    }

    public function approveLoan(Request $request): JsonResponse
    {
        $request->validate([
            'loan_id' => 'required|numeric',
        ]);

        $loan = Loan::find($request->loan_id);

        if (is_null($loan)) {
            return response()->json([
                'message' => 'Loan does not exist',
            ], 400);
        }

        if ($loan->state != 'PENDING') {
            return response()->json([
                'message' => 'Loan is already approved',
            ], 400);
        }

        $loan->approved_on = now();
        $loan->state = 'APPROVED';
        $loan->save();

        $total_instalments = $loan->term;
        $instalment_amount = $loan->amount / $total_instalments;

        for ($i = 1; $i <= $total_instalments; $i++) {
            LoanRepayment::create([
                'loan_id' => $loan->id,
                'amount' => $instalment_amount,
                'due_date' => Carbon::now()->addDays($i * 7),
            ]);
        }

        return response()->json([
            'data' => []
        ]);
    }

    public function getLoans(Request $request): JsonResponse
    {
        $loans = Loan::select(
            'id',
            'amount',
            'term',
            'state',
            'created_at',
            'approved_on'
        )->where('user_id', Auth::user()->id)->get();

        return response()->json([
            'data' => [
                'loans' => $loans
            ]
        ]);
    }

    public function addRepayment(Request $request): JsonResponse
    {
        $request->validate([
            'loan_id' => 'required|numeric',
            'amount' => 'required|regex:/^\d{1,6}(\.\d{2})?$/',
        ]);

        // check if loan exists and belongs to the authenticated user
        $loan = Loan::find($request->loan_id);

        if (is_null($loan) || $loan->user_id != Auth::user()->id) {
            return response()->json([
                'message' => 'Bad request',
            ], 400);
        }

        // check if loan is still pending
        if ($loan->state == 'PENDING') {
            return response()->json([
                'message' => 'Loan is not approved yet',
            ], 400);
        }

        // check if loan is already paid
        if ($loan->state == 'PAID') {
            return response()->json([
                'message' => 'Loan is already paid',
            ], 400);
        }

        $loanRepayments = LoanRepayment::where([
            'loan_id' => $request->loan_id,
            'state' => 'PENDING',
        ])->orderBy('due_date', 'ASC')->get();

        $loanRepayment = $loanRepayments[0];

        // check if the amount being paid is valid
        if ($request->amount < $loanRepayment->amount) {
            return response()->json([
                'message' => 'Amount is less than the instalment amount',
            ], 400);
        }

        $loanRepayment->state = 'PAID';
        $loanRepayment->paid_on = now();
        $loanRepayment->save();

        if (count($loanRepayments) == 1) {
            $loan->state = 'PAID';
            $loan->paid_on = now();
            $loan->save();
        }

        return response()->json([
            'data' => [
                'loan_repayment_id' => $loanRepayment->id,
            ]
        ]);
    }
}
