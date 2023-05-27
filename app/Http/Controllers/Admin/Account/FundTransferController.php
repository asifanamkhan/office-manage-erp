<?php

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use App\Models\Account\Bank;
use App\Models\Account\BankAccount;
use App\Models\Account\FundTransfer;
use App\Models\Account\Transaction;
use App\Repositories\Admin\Account\AccountsRepository;
use App\Repositories\UserRepository;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FundTransferController extends Controller
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
                $transactions = FundTransfer::latest()->with(['cashInBankAccount', 'cashOutBankAccount', 'createdBy'])->get();
                return DataTables::of($transactions)
                    ->addIndexColumn()
                    ->addColumn('cash_in', function ($transactions) {
                        return $transactions->cashInBankAccount->account_number . ' | ' . $transactions->cashInBankAccount->name;
                    })
                    ->addColumn('cash_out', function ($transactions) {
                        return $transactions->cashOutBankAccount->account_number . ' | ' . $transactions->cashOutBankAccount->name;
                    })
                    ->addColumn('action', function ($transactions) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                        <a class="btn btn-sm btn-success text-white " style="cursor:pointer"
                        href="' . route('admin.account.fund-transfer.edit', $transactions->id) . '" title="Edit"><i class="bx bxs-edit"></i></a><a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $transactions->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->rawColumns(['action', 'cash_in', 'cash_out'])
                    ->make(true);
            }
            return view('admin.account.fund-transfer.index');
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
            $bankAccounts = BankAccount::where('status', 1)->get();
            return view('admin.account.fund-transfer.create',compact('bankAccounts'));
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
            'fund_transaction_title' => 'required|string',
            'fund_transaction_date' => 'required|date',
            'form_account_id' => 'required',
            'to_account_id' => 'required',
            'amount' => 'required',
            'description' => 'nullable|string',
        ]);
        DB::beginTransaction();
        try {
            if ($request->form_account_id == $request->to_account_id) {
                return redirect()->back()->with('error', 'Same account not allowed !');
            } else {

                // Form
                $transactionForm = new Transaction();
                $transactionForm->transaction_title = $request->fund_transaction_title;
                $transactionForm->transaction_date = $request->fund_transaction_date;
                $transactionForm->account_id = $request->form_account_id;
                $transactionForm->transaction_purpose = 7;
                $transactionForm->transaction_type = 1;
                $transactionForm->amount = $request->amount;
                $transactionForm->description = strip_tags($request->description);
                $transactionForm->cheque_number = $request->cheque_number;
                $transactionForm->created_by = Auth::user()->id;
                $transactionForm->access_id = json_encode(UserRepository::accessId(Auth::id()));

                $balance = AccountsRepository::postBalance($request->form_account_id);
                $balance = $balance - $request->amount;

                if ($balance < 0) {
                    return redirect()->route('admin.account.fund-transfer.index')->with('error', 'Transaction failed for insufficient balance! ');
                } else {
                    // Transaction & TransactionSummary
                    $transactionForm->save();
                }

                // Transaction TO-Account Store
                $transactionTo = new Transaction();
                $transactionTo->transaction_title = $request->fund_transaction_title;
                $transactionTo->transaction_date = $request->fund_transaction_date;
                $transactionTo->account_id = $request->to_account_id;
                $transactionTo->transaction_purpose = 6;
                $transactionTo->transaction_type = 2;
                $transactionTo->amount = $request->amount;
                $transactionTo->description = strip_tags($request->description);
                $transactionTo->cheque_number = $request->cheque_number;
                $transactionTo->created_by = Auth::user()->id;
                $transactionTo->access_id = json_encode(UserRepository::accessId(Auth::id()));
                $transactionTo->save();

                // Fund Transfer
                $fundTransfer = new FundTransfer();
                $fundTransfer->date = $request->fund_transaction_date;
                $fundTransfer->cash_in_account = $request->to_account_id;
                $fundTransfer->cash_in_transaction = $transactionTo->id;
                $fundTransfer->cash_out_account = $request->form_account_id;
                $fundTransfer->cash_out_transaction = $transactionForm->id;
                $fundTransfer->amount = $request->amount;
                $fundTransfer->created_by = Auth::user()->id;
                $fundTransfer->access_id = json_encode(UserRepository::accessId(Auth::id()));
                $fundTransfer->save();

                DB::commit();
                return redirect()->route('admin.account.fund-transfer.index')->with('message', 'Fund Transfer successfully.');
            }
        } catch (\Exception $exception) {

            DB::rollBack();
            return redirect()->back()->with('error', 'Operation failed ! ' . $exception->getMessage());
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
            $bank = Bank::where('id', $id)->with('createdByUser')->first();
            return view('administrator.account.bank.bank_info.show', compact('bank'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('exception', 'Operation failed ! ' . $exception->getMessage());
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
            // Get All Bank-Account
            $bankAccounts = BankAccount::where('status', 1)->get();
            // Get Selected FundTransfer data
            $fundTransfer = FundTransfer::where('id', $id)->with('cashInBankAccount', 'cashOutBankAccount', 'createdBy', 'updatedBy')->first();
            // Get Selected Transaction data
            $transaction = Transaction::where('id', $fundTransfer->cash_in_transaction)->first();
            $balance = AccountsRepository::postBalance($fundTransfer->cashOutBankAccount->id);
            $accountBalance = $balance + $fundTransfer->amount;
            return view('admin.account.fund-transfer.edit',compact('fundTransfer', 'transaction', 'bankAccounts', 'accountBalance'));
        } catch (\Exception $exception) {
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
            'fund_transaction_title' => 'required|string',
            'fund_transaction_date' => 'required|date',
            'amount' => 'required',
            'description' => 'nullable|string',
        ]);

        try {
            $fundTransfer = FundTransfer::where('id', $id)->first();

            // Update Cash-Out Transaction
            $transactionForm = Transaction::where('id', $fundTransfer->cash_out_transaction)->first();

            // Balance
            $updateBalance = AccountsRepository::transactionUpdateBalanceCheck($transactionForm->account_id,$transactionForm->id, $request->amount);

            if ($updateBalance < 0) {
                return redirect()->route('admin.account.fund-transfer.index')->with('error', 'Transaction failed for insufficient balance! ');
            } else {
                $transactionForm->transaction_title = $request->fund_transaction_title;
                $transactionForm->transaction_date = $request->fund_transaction_date;
                $transactionForm->amount = $request->amount;
                $transactionForm->description =strip_tags($request->description) ;
                $transactionForm->cheque_number = $request->cheque_number;
                $transactionForm->updated_by = Auth::user()->id;
                $transactionForm->save();

                // Transaction TO-Account Update
                $transactionTo = Transaction::where('id', $fundTransfer->cash_in_transaction)->first();
                $transactionTo->transaction_title = $request->fund_transaction_title;
                $transactionTo->transaction_date = $request->fund_transaction_date;
                $transactionTo->amount = $request->amount;
                $transactionForm->description = strip_tags($request->description);
                $transactionTo->cheque_number = $request->cheque_number;
                $transactionTo->updated_by = Auth::user()->id;
                $transactionTo->update();

                // Fund Transfer
                $fundTransfer->date = $request->fund_transaction_date;
                $fundTransfer->amount = $request->amount;
                $fundTransfer->updated_by = Auth::user()->id;
                $fundTransfer->save();

                return redirect()->route('admin.account.fund-transfer.index')->with('message', 'Update successfully.');
            }


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
                $fundTransfer = FundTransfer::where('id', $id)->with('cashOutBankAccount')->first();
                // Balance Check
                $updateCashOutBalance = AccountsRepository::balanceCheckBeforeDelete($fundTransfer->cash_out_account, $fundTransfer->cash_out_transaction);
                $updateCashInBalance = AccountsRepository::balanceCheckBeforeDelete($fundTransfer->cash_in_account, $fundTransfer->cash_in_transaction);

                if ( $updateCashOutBalance < 0 || $updateCashInBalance < 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Transaction failed for insufficient balance!',
                    ]);
                }else{
                    Transaction::where('id', $fundTransfer->cash_in_transaction)->delete();
                    Transaction::where('id', $fundTransfer->cash_out_transaction)->delete();

                    FundTransfer::where('id', $id)->delete();
                    return response()->json([
                        'success' => true,
                        'message' => 'Item Deleted Successfully.',
                    ]);
                }
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
    //starts status change function
    public function statusUpdate(Request $request)
    {

        try {
            $reference=Bank::findOrFail($request->id);

            $reference->status == 1 ? $reference->status = 0 : $reference->status = 1;

            $reference->update();
            if ($reference->status == 1) {
                return "active";
                // exit();
            } else {
                return "inactive";
            }


        }
        catch (\Exception $exception) {
            return  $exception->getMessage();
        }
    }

}
