<?php

namespace App\Http\Controllers\Admin\Account\Loan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Account\BankAccount;
use App\Models\Account\Loan\Loan;
use App\Models\Account\Loan\Loan_Authority;
use App\Models\Account\Transaction;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use DataTables;

class LoanReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $transaction = Transaction::where('transaction_purpose', 14)->orWhere('transaction_purpose', 15)->get();

                return DataTables::of($transaction)
                    ->addIndexColumn()
                    ->addColumn('note', function ($transaction) {
                        return $transaction->description;
                    })
                    ->addColumn('action', function ($transaction) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                        <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="loanReturnDeleteConfirm(' . $transaction->id . ')" title="Delete"><i class="bx bxs-trash"></i></a></div>';
                    })
                    ->rawColumns(['note', 'action'])
                    ->make(true);
            }
            return view('admin.account.loan.loan.index');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $loanauthorities = Loan_Authority::get();
            $bankAccounts = BankAccount::where('status', 1)->get();
            return view('admin.account.loan.loan.create', compact('loanauthorities', 'bankAccounts'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('exception', 'Operation failed ! ' . $exception->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'loan_author_id' => 'required',
            'loan_type' => 'required',
            'return_title' => 'required|string',
            'transaction_date' => 'required|date',
            'transaction_way' => 'required',
            'amount' => 'required',
            'note' => 'nullable|string',
        ]);

        if ($request->transaction_way == 2) {
            $request->validate([
                'account_id' => 'required',
            ]);
        }
        try {
            // investment
            $loan = Loan::findOrFail($request->loan_id);
            $transaction = new Transaction();
            $transaction->transaction_title = $request->return_title;
            $transaction->transaction_date = $request->transaction_date;
            $transaction->loan_author_id = $request->loan_author_id;

            if ($request->transaction_way == 2) {
                $transaction->account_id = $request->account_id;
            } else {
                $transaction->account_id = 0;
            }

            if ($request->loan_type == 2) {
                $transaction->transaction_purpose = 14;
                $transaction->transaction_type = 2;
            } else {
                $transaction->transaction_purpose = 15;
                $transaction->transaction_type = 1;
            }

            $transaction->amount = $request->amount;
            $transaction->cheque_number = $request->cheque_number;
            $transaction->description = strip_tags($request->note);
            $transaction->loan_id = $loan->id;
            $transaction->created_by = Auth::user()->id;
            $transaction->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $transaction->save();



            return redirect()->route('admin.loan-return.show', $request->loan_id)->with('message', 'Loan successfully.');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $transaction = Transaction::where('loan_id', $id)->first();
            $loan = Loan::with('author')
                ->where('id', $id)
                ->first();
            $loan_amount = $loan->loan_amount;
            if ($transaction->transaction_purpose == 12) {
                $return_amount = Transaction::where('loan_id', $id)
                    ->where('transaction_purpose', 14)
                    ->sum('amount');

            } else {
                $return_amount = Transaction::where('loan_id', $id)
                    ->where('transaction_purpose', 15)
                    ->sum('amount');
            }
            $due = $loan_amount - $return_amount;
            $authority = Loan_Authority::where('id', $loan->loan_author_id)->first();
            $bankAccounts = BankAccount::where('status', 1)->get();
            return view('admin.account.loan.loan.loan-return.show',compact('loan','authority','bankAccounts','loan_amount', 'return_amount', 'due'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function LoanList(Request $request,$id)
    {
        try {
            if ($request->ajax()) {
                $loan = Loan::with('author')
                ->where('loan_author_id',$id)
                ->get();

                return DataTables::of($loan)
                    ->addIndexColumn()
                    ->addColumn('loan_type', function ($loan) {
                        if ($loan->loan_type == 1) {
                            return "Taking";
                        }
                        return "Giving";
                    })
                    ->addColumn('transaction_way', function ($loan) {
                        if ($loan->transaction_way == 1) {
                            return "Cash";
                        }
                        return "Bank";
                    })
                    ->addColumn('action', function ($loan) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                        <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="loanDeleteConfirm(' . $loan->id . ')" title="Delete"><i class="bx bxs-trash"></i></a></div>';
                    })
                    ->rawColumns([ 'loan_type', 'action'])
                    ->make(true);
            }
            return view('admin.account.loan.loan.show');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {

        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      try {

        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                $transaction = Transaction::findOrFail($id);
                $transaction->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Loan Return Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }

    /**
     * Update the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
}
