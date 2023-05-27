<?php

namespace App\Http\Controllers\Admin\Account\Investment;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Account\BankAccount;
use App\Models\Account\Investment\Investment;
use App\Models\Account\Investment\Investor;
use App\Models\Account\Transaction;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Illuminate\Support\Facades\DB;

class InvestmentController extends Controller
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
                $investment = Investment::with('investor')->latest()->get();
                return DataTables::of($investment)
                    ->addIndexColumn()
                    ->addColumn('transaction_way', function ($investment) {

                        if ($investment->transaction_way == 1) {
                            return '<span class="text-success">Cash</span>';
                        } else {
                            return '<span class="text-info">Bank</span>';
                        }

                    })
                    ->addColumn('investor', function ($investment) {
                        return $investment->investor->name;
                    })
                    ->addColumn('action', function ($investment) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example"><a class="btn btn-sm btn-primary text-white " style="cursor:pointer"
                        href="' . route('admin.investment-return.show', $investment->id) . '" title="Return"><i class="bx bx-subdirectory-left"></i></a>
                        <a class="btn btn-sm btn-success text-white " style="cursor:pointer"
                        href="' . route('admin.investment.edit', $investment->id) . '" title="Edit"><i class="bx bxs-edit"></i></a><a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="investmentDeleteConfirm(' . $investment->id . ')" title="Delete"><i class="bx bxs-trash"></i></a></div>';
                    })
                    ->rawColumns(['transaction_way','investor','action'])
                    ->make(true);
            }
            return view('admin.account.investment.investment.index');
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
            $investors = Investor::where('status',1)->get();
            $bankAccounts = BankAccount::where('status', 1)->get();
            return view('admin.account.investment.investment.create',compact('investors','bankAccounts'));
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
            'investor_id' => 'required',
            'transaction_title' => 'required|string',
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
             // investment
            $investment = new Investment();
            $investment->investor_id = $request->investor_id;
            $investment->date = $request->transaction_date;
            $investment->transaction_way = $request->transaction_way;
            $investment->note = $request->note;
            $investment->transaction_title = $request->transaction_title;
            $investment->amount = $request->amount;
            $investment->created_by = Auth::user()->id;
            $investment->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $investment->save();

            //  Transaction Store
            $transaction = new Transaction();
            $transaction->transaction_title = $request->transaction_title;

            $transaction->transaction_date = $request->transaction_date;
            if ($request->transaction_way == 2) {
                $transaction->account_id = $request->account_id;
            } else {
                $transaction->account_id = 0;
            }
            $transaction->transaction_purpose = 9;
            $transaction->transaction_type = 2;
            $transaction->amount = $request->amount;
            $transaction->cheque_number = $request->cheque_number;
            $transaction->description = strip_tags($request->note);
            $transaction->investment_id = $investment->id;
            $transaction->investor_id = $request->investor_id;
            $investment->created_by = Auth::user()->id;
            $investment->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $transaction->save();
            DB::commit();
           return redirect()->route('admin.investment.show',$request->investor_id)->with('message', 'Investment successfully.');
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
            $investor = Investor::findOrFail($id);
            $bankAccounts = BankAccount::where('status', 1)->get();
            return view('admin.account.investment.investment.show',compact('investor','bankAccounts'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function InvestmentList(Request $request, $id)
    {
        try {
            if ($request->ajax()) {
                $investment = Investment::with('investor')->where('investor_id',$id)->get();
                return DataTables::of($investment)
                    ->addIndexColumn()
                    ->addColumn('transaction_way', function ($investment) {

                        if ($investment->transaction_way == 1) {
                            return '<span class="text-success">Cash</span>';
                        } else {
                            return '<span class="text-info">Bank</span>';
                        }

                    })
                    ->addColumn('investor', function ($investment) {
                        return $investment->investor->name;
                    })
                    ->addColumn('action', function ($investment) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                        <a class="btn btn-sm btn-success text-white " style="cursor:pointer"
                        href="' . route('admin.investment-return.show', $investment->id) . '" title="Return"><i class="bx bx-subdirectory-left"></i></a><a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="investmentDeleteConfirm(' . $investment->id . ')" title="Delete"><i class="bx bxs-trash"></i></a></div>';
                    })
                    ->rawColumns(['transaction_way','investor','action'])
                    ->make(true);
            }
            return view('admin.account.investment.investment.show');
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
            $investors = Investor::where('status',1)->get();
            $investment = Investment::with('transaction')->findOrFail($id);
            $bankAccounts = BankAccount::where('status', 1)->get();

           return view('admin.account.investment.investment.edit',compact('investment','investors','bankAccounts'));
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
            'investor_id' => 'required',
            'transaction_title' => 'required|string',
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
            // investment
            $investment = Investment::findOrFail($id);
            $investment->investor_id = $request->investor_id;
            $investment->date = $request->transaction_date;
            $investment->transaction_way = $request->transaction_way;
            $investment->note = $request->note;
            $investment->transaction_title = $request->transaction_title;
            $investment->amount = $request->amount;
            $investment->updated_by = Auth::user()->id;
            $investment->update();

            //  Transaction Store
            $transaction = Transaction::where('investment_id', $investment->id)->first();
            $transaction->transaction_title = $request->transaction_title;

            $transaction->transaction_date = $request->transaction_date;
            if ($request->transaction_way == 2) {
                $transaction->account_id = $request->account_id;
            } else {
                $transaction->account_id = 0;
            }
            $transaction->transaction_purpose = 9;
            $transaction->transaction_type = 2;
            $transaction->amount = $request->amount;
            $transaction->cheque_number = $request->cheque_number;
            $transaction->description = $request->note;
            $transaction->investment_id = $investment->id;
            $transaction->investor_id = $request->investor_id;
            $investment->updated_by = Auth::user()->id;
            $transaction->update();

            DB::commit();
           return redirect()->route('admin.investment.index')->with('message', 'Investment Update successfully.');
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
    public function destroy(Request $request,$id)
    {
        if ($request->ajax()) {
            try {
                $investment = Investment::where('id', $id)->with('transaction')->first();
                $transaction = Transaction::where('investment_id', $investment->id)->first();

                $transaction->delete();
                $investment->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Investment Deleted Successfully.',
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
