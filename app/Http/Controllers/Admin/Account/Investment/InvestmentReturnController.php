<?php

namespace App\Http\Controllers\Admin\Account\Investment;

use App\Http\Controllers\Controller;
use App\Models\Account\BankAccount;
use App\Models\Account\Investment\Investment;
use App\Models\Account\Investment\Investor;
use App\Models\Account\Transaction;
use App\Repositories\Admin\Account\AccountsRepository;
use App\Repositories\UserRepository;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvestmentReturnController extends Controller
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
                $investment_return = Transaction::with('investor')
                ->where('transaction_purpose', 10)
                ->orWhere('transaction_purpose', 11);

                return DataTables::of($investment_return)
                    ->addIndexColumn()
                    ->addColumn('transaction_type', function ($investment_return) {
                        if ($investment_return->transaction_type == 1) {
                            return "Debit";
                        } else {
                            return "Credit";
                        }
                    })
                    ->addColumn('name', function ($investment_return) {
                            return $investment_return->investor->name;

                    })
                    ->addColumn('return_type', function ($investment_return) {
                        if ($investment_return->transaction_purpose == 11) {
                            return "Profit-Return";
                        } else if ($investment_return->transaction_purpose == 10) {
                            return "Investment-Return";
                        }
                    })
                    ->addColumn('action', function ($investment_return) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                        <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="investmentReturnDeleteConfirm(' . $investment_return->id . ')" title="Delete"><i class="bx bxs-trash"></i></a></div>';
                    })
                    ->rawColumns(['name','note', 'action'])
                    ->make(true);
            }
            return view('admin.account.investment.investment.investment-return.show');
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
            return view('admin.account.investment.investor.create');
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
            'return_title' => 'required',
            'transaction_purpose' => 'required',
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
            if ($request->transaction_way == 2) {
                $balance = AccountsRepository::postBalance($request->account_id);
            } else {
                $balance = AccountsRepository::postBalance(0);
            }
            $balance = $balance - $request->amount;
            if ($balance < 0) {
                return redirect()->back()->with('error', 'Transaction failed for insufficient balance! ');
            } else {
                //  Transaction Store
                $transaction = new Transaction();
                $transaction->transaction_title = $request->return_title;
                $transaction->investor_id = $request->investor_id;
                $transaction->investment_id = $request->investment_id;
                $transaction->transaction_date = $request->transaction_date;
                if ($request->transaction_way == 2) {
                    $transaction->account_id = $request->account_id;
                } else {
                    $transaction->account_id = 0;
                }
                $transaction->transaction_purpose = $request->transaction_purpose;
                $transaction->transaction_type = 1;
                $transaction->amount = $request->amount;
                $transaction->cheque_number = $request->cheque_number;
                $transaction->description = $request->note;
                $transaction->created_by = Auth::user()->id;
                $transaction->access_id = json_encode(UserRepository::accessId(Auth::id()));
                $transaction->save();

                return redirect()->route('admin.investment-return.show',$request->investment_id)->with('message', 'Investment Return Successfully.');
            }
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
            $invested_amount = Transaction::where('investment_id', $id)
                ->where('transaction_purpose', 9)
                ->sum('amount');
            $return_amount = Transaction::where('investment_id', $id)
                ->where('transaction_purpose', 10)
                ->sum('amount');
            $profit_amount = Transaction::where('investment_id', $id)
                ->where('transaction_purpose', 11)
                ->sum('amount');

            $due = $invested_amount - $return_amount;

            $investment = Investment::findOrFail($id);
            $investor = Investor::where('id', $investment->investor_id)->first();
            $bankAccounts = BankAccount::where('status', 1)->get();

            return view('admin.account.investment.investment.investment-return.show',compact('investment','investor','bankAccounts','invested_amount','return_amount','due','profit_amount'));
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

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        if ($request->ajax()) {
            try {
                $return = Transaction::where('id', $id)->first();
                $return->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Return Deleted Successfully.',
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
