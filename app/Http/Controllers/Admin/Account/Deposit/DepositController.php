<?php

namespace App\Http\Controllers\Admin\Account\Deposit;

use App\Http\Controllers\Controller;
use App\Models\Account\BankAccount;
use App\Models\Account\Transaction;
use App\Repositories\Admin\Account\AccountsRepository;
use App\Repositories\UserRepository;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepositController extends Controller
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
                $transactions = Transaction::whereIn('transaction_purpose', [2])->latest()->with(['bankAccount','createdByUser'])->get();
                return DataTables::of($transactions)
                    ->addIndexColumn()
                    ->addColumn('bankinfo', function ($transactions) {
                        if($transactions->account_id != 0){
                            return $transactions->bankAccount->account_number . ' | ' . $transactions->bankAccount->name;
                        }
                        else{
                            return " --";
                        }
                    })
                    ->addColumn('transaction_way', function ($transactions) {
                        if($transactions->account_id != 0){
                            return 'Bank';
                        }
                        else{
                            return "Cash";
                        }

                    })
                    ->addColumn('createdByUser', function ($transactions) {
                        return $transactions->createdByUser->name;
                    })
                    ->addColumn('action', function ($transactions) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                        <a class="btn btn-sm btn-success text-white " style="cursor:pointer" href="' . route('admin.account.deposit.edit', $transactions->id) . '" title="Edit"><i class="bx bxs-edit"></i></a><a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showTransactionDeleteConfirm(' . $transactions->id . ')" title="Delete"><i class="bx bxs-trash"></i></a> </div>';
                    })
                    ->rawColumns(['action','transaction_way','createdByUser', 'bankinfo', 'purpose'])
                    ->make(true);
            }
            return view('admin.account.deposit.index');
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
            // Get All Bank
            $bank_accounts = BankAccount::where('status', 1)->get();
            return view('admin.account.deposit.create',compact('bank_accounts'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
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
            'transaction_way' => 'required',
            'transaction_date' => 'required|date',
            'amount' => 'required',
        ]);
        try {
            //  Transaction Store
            $transaction = new Transaction();
            $transaction->transaction_title = $request->transaction_title;
            $transaction->transaction_date = $request->transaction_date;

            if ($request->transaction_way == 2) {
                $transaction->account_id = $request->account_id;
            } else {
                $transaction->account_id = 0;
            }

            $transaction->transaction_purpose =2;
            $transaction->transaction_type = 2;
            $transaction->amount = $request->amount;
            $transaction->cheque_number = $request->cheque_number;
            $transaction->description =strip_tags($request->description);
            $transaction->created_by = Auth::user()->id;
            $transaction->access_id = json_encode(UserRepository::accessId(Auth::id()));

            $transaction->save();

           return redirect()->route('admin.account.deposit.index')->with('message', 'Deposit  Successfully.');
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
            $bank_accounts = BankAccount::where('status', 1)->get();
            $transaction = Transaction::where('id', $id)->first();
            $accountBalance = AccountsRepository::postBalance($transaction->account_id);
           return view('admin.account.deposit.edit',compact('transaction','bank_accounts','accountBalance'));
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
            'transaction_way' => 'required',
            'amount' => 'required',
        ]);
        try {
            //  Transaction Store
            $transaction = Transaction::findOrFail($id);
            $transaction->transaction_title = $request->transaction_title;
            $transaction->transaction_date = $request->transaction_date;

           if ($request->transaction_way == 2) {
                $transaction->account_id = $request->account_id;
            } else {
                $transaction->account_id = 0;
            }

            $transaction->transaction_purpose = 2;
            $transaction->transaction_type = 2;
            $transaction->amount = $request->amount;
            $transaction->cheque_number = $request->cheque_number;
            $transaction->description = strip_tags($request->description);
            $transaction->updated_by = Auth::user()->id;

            $transaction->update();

           return redirect()->route('admin.account.deposit.index')->with('message', 'Deposit Update successfully.');
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
    public function destroy(Request $request,$id)
    {
        if ($request->ajax()) {
            try {
                Transaction::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Transaction Deleted Successfully.',
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
   //starts status change function
   public function statusUpdate(Request $request)
   {
       try {

           $reference=Transaction::findOrFail($request->id);
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
