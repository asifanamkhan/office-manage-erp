<?php

namespace App\Http\Controllers\Admin\Account\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Account\BankAccount;
use App\Models\Account\Transaction;
use App\Repositories\UserRepository;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
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
                // whereIn('transaction_purpose', [0,1, 2])->
                $transactions = Transaction::whereNotIn('transaction_purpose', [0])->latest()->with(['bankAccount','createdByUser'])->get();
                return DataTables::of($transactions)
                    ->addIndexColumn()
                    ->addColumn('bankinfo', function ($transactions) {
                        if($transactions->transaction_account_type == 2){
                            return $transactions->bankAccount->bank->bank_name . ' | ' . $transactions->bankAccount->account_number . '<span style="color:#6B84F5">('.$transactions->bankAccount->name.') </span>';
                        }
                        else{
                            return " <b>Cash</b>";
                        }
                    })
                    ->addColumn('transaction_way', function ($transactions) {
                        if($transactions->transaction_account_type == 2){
                            return 'Bank';
                        }
                        else{
                            return "<b>Cash</b>";
                        }
                    })
                    ->addColumn('createdByUser', function ($transactions) {
                        return $transactions->createdByUser;
                    })
                    ->addColumn('purpose', function ($transactions) {
                        if ($transactions->transaction_purpose == 1) {
                            return '<span class="text-danger text-bold"><b>Withdraw - </b></span>';
                        } else if ($transactions->transaction_purpose == 2) {
                            return '<span class="text-success text-bold"><b>Deposit -</b></span>';
                        } else if ($transactions->transaction_purpose == 3) {
                            return '<span class="text-success text-bold"><b>Revenue - </b></span>';
                        } else if ($transactions->transaction_purpose == 4) {
                            return '<span class="text-success text-bold"><b>Given Payment - </b></span>';
                        } else if ($transactions->transaction_purpose == 5) {
                            return '<span class="text-danger text-bold"><b>Expense -</b></span>';
                        } else if ($transactions->transaction_purpose == 6) {
                            return '<span class="text-success text-bold"><b>Fund-Transfer - </b>(Cash-In)</span>';
                        } else if ($transactions->transaction_purpose == 7) {
                            return '<span class="text-danger text-bold"><b>  Fund-Transfer - (Cash-Out) </b></span>';
                        } else if ($transactions->transaction_purpose == 8) {
                            return '<span class="text-success text-bold"><b>Cash In -</b></span>';
                        } else if ($transactions->transaction_purpose == 9) {
                            return '<span class="text-success text-bold"><b>Investment - </b></span>';
                        } else if ($transactions->transaction_purpose == 10) {
                            return '<span class="text-success text-bold"><b>Investment return - </b></span>';
                        } else if ($transactions->transaction_purpose == 11) {
                            return '<span class="text-success text-bold"><b>Investment profit retrun - </b></span>';
                        } else if ($transactions->transaction_purpose == 12) {
                            return '<span class="text-success text-bold"><b>Giving loan - </b></span>';
                        } else if ($transactions->transaction_purpose == 13) {
                            return '<span class="text-success text-bold"><b>Taking loan - </b></span>';
                        } else if ($transactions->transaction_purpose == 14) {
                            return '<span class="text-success text-bold"><b>Return loan (Giving) - </b></span>';
                        } else if ($transactions->transaction_purpose == 15) {
                            return '<span class="text-success text-bold"></b>Return loan (Taking) - </b></span>';
                        } else {
                            return '...';
                        }
                    })
                    ->addColumn('action', function ($transactions) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example"><a class="btn btn-sm btn-primary text-white " style="cursor:pointer" href="' . route('admin.account.transaction.show', $transactions->id) . '" title="Show"><i class="bx bx-show"></i></a><a class="btn btn-sm btn-success text-white " style="cursor:pointer" href="' . route('admin.account.transaction.edit', $transactions->id) . '" title="Edit"><i class="bx bxs-edit"></i></a><a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showTransactionDeleteConfirm(' . $transactions->id . ')" title="Delete"><i class="bx bxs-trash"></i></a> </div>';
                    })
                    ->rawColumns(['action','transaction_way','createdByUser', 'bankinfo', 'purpose'])
                    ->make(true);
            }
            return view('admin.account.transaction.index');
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
            return view('admin.account.transaction.create',compact('bank_accounts'));
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
            'transaction_purpose' => 'required',
            'account_id' => 'required',
            'amount' => 'required',
        ]);
        try {
            //  Transaction Store
            $transaction = new Transaction();
            $transaction->transaction_title = $request->transaction_title;
            $transaction->transaction_date = $request->transaction_date;
            $transaction->account_id = $request->account_id;
            $transaction->transaction_purpose = $request->transaction_purpose;
            if ($request->transaction_purpose == 2) {
                $transaction->transaction_type = 2;
            } else {
                $transaction->transaction_type = 1;
                // Balance Check
            }
            $transaction->amount = $request->amount;
            $transaction->cheque_number = $request->cheque_number;
            $transaction->description =strip_tags($request->description);
            $transaction->created_by = Auth::user()->id;
            $transaction->access_id = json_encode(UserRepository::accessId(Auth::id()));

            $transaction->save();

           return redirect()->route('admin.account.transaction.index')->with('message', 'Transaction Successfully.');
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
        try {
            // Get All Bank
            $transaction = Transaction::where('id',$id)->with(['bankAccount','createdByUser'])->first();

            if ($transaction->transaction_purpose == 1) {
                $transaction_purpose = 'Withdraw';
            } elseif ($transaction->transaction_purpose == 2) {
                $transaction_purpose = 'Deposit';
            }
            if($transaction->account_id == 0){
                $transaction_way = "Cash";
            }
            else{
                $transaction_way = "Bank";
            }
            return view('admin.account.transaction.show',compact('transaction','transaction_purpose','transaction_way'));
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
            $bank_accounts = BankAccount::where('status', 1)->get();
            $transaction = Transaction::where('id', $id)->first();
           return view('admin.account.transaction.edit',compact('transaction','bank_accounts'));
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
            'transaction_purpose' => 'required',
            'account_id' => 'required',
            'amount' => 'required',
        ]);
        try {
            //  Transaction Store
            $transaction = Transaction::findOrFail($id);
            $transaction->transaction_title = $request->transaction_title;
            $transaction->transaction_date = $request->transaction_date;
            $transaction->account_id = $request->account_id;
            $transaction->transaction_purpose = $request->transaction_purpose;
            if ($request->transaction_purpose == 2) {
                $transaction->transaction_type = 2;
            } else {
                $transaction->transaction_type = 1;
                // Balance Check
            }
            $transaction->amount = $request->amount;
            $transaction->cheque_number = $request->cheque_number;
            $transaction->description =strip_tags($request->description);
            $transaction->updated_by = Auth::user()->id;

            $transaction->update();

           return redirect()->route('admin.account.transaction.index')->with('message', 'Transaction Update successfully.');
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
