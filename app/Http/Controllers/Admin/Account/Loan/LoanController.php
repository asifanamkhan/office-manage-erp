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
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
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
                $loan = Loan::with('author')->latest()->get();
                return DataTables::of($loan)
                    ->addIndexColumn()
                    ->addColumn('author', function ($loan) {
                        return $loan->author->name;
                    })
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
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example"><a class="btn btn-sm btn-primary text-white " style="cursor:pointer"
                        href="' . route('admin.loan-return.show', $loan->id) . '" title="Return"><i class="bx bx-subdirectory-left"></i></a>
                        <a class="btn btn-sm btn-success text-white " style="cursor:pointer"
                        href="' . route('admin.loan.edit', $loan->id) . '" title="Edit"><i class="bx bxs-edit"></i></a><a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="loanDeleteConfirm(' . $loan->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->rawColumns(['author', 'loan_type', 'action'])
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
            'loan_title' => 'required|string',
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
        DB::beginTransaction();
        try {
            $loan = new Loan();
            $loan->loan_author_id = $request->loan_author_id;
            $loan->loan_date = $request->transaction_date;
            $loan->transaction_way = $request->transaction_way;
            $loan->loan_type = $request->loan_type;
            $loan->note = strip_tags($request->note);
            $loan->loan_title = $request->loan_title;
            $loan->loan_amount = $request->amount;
            $loan->created_by = Auth::user()->id;
            $loan->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $loan->save();

            //  Transaction Store
            $transaction = new Transaction();
            $transaction->transaction_title = $request->loan_title;
            $transaction->loan_author_id = $request->loan_author_id;
            $transaction->transaction_date = $request->transaction_date;

            if ($request->transaction_way == 2) {
                $transaction->account_id = $request->account_id;
            } else {
                $transaction->account_id = 0;
            }
            if ($request->loan_type == 2) {
                $transaction->transaction_purpose = 12;
                $transaction->transaction_type = 1;
            } else {
                $transaction->transaction_purpose = 13;
                $transaction->transaction_type = 2;
            }
            $transaction->amount = $request->amount;
            $transaction->cheque_number = $request->cheque_number;
            $transaction->description = $request->note;
            $loan->created_by = Auth::user()->id;
            $loan->access_id = json_encode(UserRepository::accessId(Auth::id()));;
            $transaction->loan_id = $loan->id;

            $transaction->save();
            DB::commit();
            return redirect()->route('admin.loan.show', $request->loan_author_id)->with('message', 'Loan successfully.');
        } catch (\Exception $exception) {
            DB::rollBack();
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
            $authority = Loan_Authority::findOrFail($id);
            $bankAccounts = BankAccount::where('status', 1)->get();
            return view('admin.account.loan.loan.show', compact('authority', 'bankAccounts'));
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
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example"><a class="btn btn-sm btn-success text-white " style="cursor:pointer"
                        href="' . route('admin.loan-return.show', $loan->id) . '" title="Return"><i class="bx bx-subdirectory-left"></i></a>
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
            $loan = Loan::with('author', 'transaction')->findOrFail($id);
            $loanauthorities = Loan_Authority::get();
            $bankAccounts = BankAccount::where('status', 1)->get();
            return view('admin.account.loan.loan.edit', compact('loanauthorities', 'bankAccounts', 'loan'));
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
        $request->validate([
            'loan_author_id' => 'required',
            'loan_type' => 'required',
            'loan_title' => 'required|string',
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

        DB::beginTransaction();
        try {
            $loan = Loan::findOrFail($id);
            $loan->loan_author_id = $request->loan_author_id;
            $loan->loan_date = $request->transaction_date;
            $loan->transaction_way = $request->transaction_way;
            $loan->loan_type = $request->loan_type;
            $loan->note = strip_tags($request->note);
            $loan->loan_title = $request->loan_title;
            $loan->loan_amount = $request->amount;
            $loan->updated_by = Auth::user()->id;
            $loan->update();


            $transaction = Transaction::where('loan_id', $loan->id)->first();
            $transaction->transaction_title = $request->loan_title;
            $transaction->loan_author_id = $request->loan_author_id;
            $transaction->transaction_date = $request->transaction_date;

            if ($request->transaction_way == 2) {
                $transaction->account_id = $request->account_id;
            } else {
                $transaction->account_id = 0;
            }

            if ($request->loan_type == 2) {
                $transaction->transaction_purpose = 12;
                $transaction->transaction_type = 1;
            } else {
                $transaction->transaction_purpose = 13;
                $transaction->transaction_type = 2;
            }

            $transaction->amount = $request->amount;
            $transaction->cheque_number = $request->cheque_number;
            $transaction->description = $request->note;
            $loan->created_by = Auth::user()->id;
            $loan->access_id = json_encode(UserRepository::accessId(Auth::id()));;
            $transaction->loan_id = $loan->id;

            $transaction->update();

            DB::commit();

            return redirect()->route('admin.loan.index')->with('message', 'Loan Update successfully.');
        } catch (\Exception $exception) {

            DB::rollBack();
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
                $loan = Loan::where('id', $id)->with('transaction')->first();
                $transaction = Transaction::where('loan_id', $loan->id)->first();

                $transaction->delete();
                $loan->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Loan Deleted Successfully.',
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
