<?php

namespace App\Http\Controllers\Admin\Account\BalanceSheet;

use App\Http\Controllers\Controller;
use App\Models\Account\BankAccount;
use App\Models\Account\Transaction;
use App\Repositories\Admin\Account\AccountsRepository;
use App\Repositories\Admin\Account\TransactionRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BalanceSheetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            return view('admin.account.balance_sheet.bank_balance_sheet');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function balanceSheetData(Request $request)
    {
        try {
            if ($request->ajax()) {
                $debit = 0;
                $credit = 0;

                $transaction = Transaction::where('transaction_account_type',2)->with('bankAccount')->groupBy('account_id');

                //dd($transaction);

                return DataTables::of($transaction)
                    ->addIndexColumn()
                    ->addColumn('bankinfo', function ($transaction) {
                        return $transaction->bankAccount->account_number . ' | ' . $transaction->bankAccount->name;
                    })
                    ->addColumn('debit', function ($transaction) use (&$debit) {

                        $debit = AccountsRepository::debitBalance($transaction->account_id);
                        return $debit;
                    })
                    ->addColumn('credit', function ($transaction) use (&$credit) {
                        $credit = AccountsRepository::creditBalance($transaction->account_id);
                        return $credit;
                    })
                    ->addColumn('balance', function ($transaction) use (&$debit, &$credit) {
                        return $credit - $debit;
                    })
                    ->rawColumns(['bankinfo', 'debit', 'credit', 'balance'])
                    ->make(true);
            }

        }

        catch (\Exception $exception) {
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
            // Get All Bank
            $bank_accounts = BankAccount::where('status', 1)->get();
            return view('admin.account.cash_in.create', compact('bank_accounts'));
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
            'transaction_title' => 'required|string',
            'transaction_date' => 'required|date',
            'amount' => 'required',
        ]);
        try {
            //  Transaction Store
            $transaction = new Transaction();
            $transaction->transaction_title = $request->transaction_title;
            $transaction->transaction_date = $request->transaction_date;
            $transaction->account_id = 0;
            $transaction->transaction_purpose = 8;
            $transaction->transaction_type = 2;
            $transaction->amount = $request->amount;
            $transaction->description = strip_tags($request->description);
            $transaction->created_by = Auth::user()->id;
            $transaction->access_id = json_encode(UserRepository::accessId(Auth::id()));

            $transaction->save();

            return redirect()->route('admin.account.cash-in.index')->with('message', 'Cash In Successfully.');
        } catch (\Exception $exception) {
            return redirect()->back()->with('exception', 'Operation failed ! ' . $exception->getMessage());
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
        //
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
            $cashIn = Transaction::where('id', $id)->first();
            return view('admin.account.cash_in.edit', compact('cashIn'));
        } catch (\Exception $exception) {
            dd($exception->getMessage());
            return redirect()->back()->with('exception', 'Operation failed ! ' . $exception->getMessage());
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
        $request->validate([
            'transaction_title' => 'required|string',
            'transaction_date' => 'required|date',
            'amount' => 'required',
        ]);
        try {
            //  Transaction Store
            $transaction = Transaction::findOrFail($id);

            $transaction->transaction_title = $request->transaction_title;
            $transaction->transaction_date = $request->transaction_date;
            $transaction->account_id = 0;
            $transaction->transaction_purpose = 8;
            $transaction->transaction_type = 2;
            $transaction->amount = $request->amount;
            $transaction->description = strip_tags($request->description);
            $transaction->updated_by = Auth::user()->id;

            $transaction->update();

            return redirect()->route('admin.account.cash-in.index')->with('message', 'Cash In Update successfully.');
        } catch (\Exception $exception) {
            return redirect()->back()->with('exception', 'Operation failed ! ' . $exception->getMessage());
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
                Transaction::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Cash In Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }
    public function bankAccountBalance(Request $request, $id)
    {
        if ($request->ajax()) {
            $balance = AccountsRepository::postBalance($id);
            return response()->json($balance);
        }
    }

    /**
     * Update the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function cashBalanceSheet(Request $request)
    {
        try {
            if ($request->ajax()) {
                $postBalance = 0;
                $transactions = Transaction::where('transaction_account_type',1)->with('createdByUser')->get();
                return DataTables::of($transactions)
                    ->addIndexColumn()
                    ->addColumn('transaction_purpose', function ($transactions) {
                        return TransactionRepository::type($transactions);
                    })
                    ->addColumn('debit', function ($transactions) {
                        if ($transactions->transaction_type == 1) {
                            return $transactions->amount;
                        } else {
                            return ' ';
                        }
                    })
                    ->addColumn('credit', function ($transactions) {
                        if ($transactions->transaction_type == 2) {
                            return $transactions->amount;
                        } else {
                            return '';
                        }
                    })
                    ->addColumn('balance', function ($data) use (&$postBalance) {
                        if ($data->transaction_type == 1) {
                            $postBalance = $postBalance - $data->amount;
                            return $postBalance;
                        } else {
                            $postBalance = $postBalance + $data->amount;
                            return $postBalance;
                        }
                    })
                    ->rawColumns(['transaction_purpose', 'debit', 'credit', 'balance'])
                    ->make(true);
            }
            return view('admin.account.balance_sheet.cash_balance_sheet');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
     public function bankAccountStatement()
    {
        try {
            $bank_accounts = BankAccount::where('status',1)->get();
            return view('admin.account.account_statement.account_statement',compact('bank_accounts'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function accountStatementData(Request $request)
    {
        if ($request->ajax()) {
            $start_date = Carbon::parse($request->start_date)->format('Y-m-d');
            $end_date = Carbon::parse($request->end_date)->format('Y-m-d');
            $postBalance = AccountsRepository::previousCreditBalance($request->account_id, $request->start_date);
            $data = Transaction::with('bankAccount')
                ->where('account_id', $request->account_id)
                ->where('transaction_date', '>=', $start_date)
                ->where('transaction_date', '<=', $end_date)
                ->orderBy('transaction_date', 'asc');
                // \dd($data);

            return DataTables::of($data, $postBalance)
                ->addIndexColumn()
                ->addColumn('purpose', function ($transactions) {
                    return TransactionRepository::type($transactions);
                })
                ->addColumn('debit', function ($data) {
                    if ($data->transaction_type == 1) {
                        return $data->amount;
                    } else {
                        return '0';
                    }
                })
                ->addColumn('credit', function ($data) {
                    if ($data->transaction_type == 2) {
                        return $data->amount;
                    } else {
                        return '0';
                    }
                })
                ->addColumn('balance', function ($data) use (&$postBalance) {
                    if ($data->transaction_type == 1) {
                        $postBalance = $postBalance - $data->amount;
                        return $postBalance;
                    } else {
                        $postBalance = $postBalance + $data->amount;
                        return $postBalance;
                    }
                })
                ->with('prevBalance',$postBalance)
                ->rawColumns(['purpose', 'debit', 'credit', 'balance'])
                ->make(true);
        }
    }
}
