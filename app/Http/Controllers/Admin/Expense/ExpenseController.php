<?php

namespace App\Http\Controllers\Admin\Expense;

use App\Http\Controllers\Controller;
use App\Models\Account\Bank;
use App\Models\Account\BankAccount;
use App\Models\Account\Transaction;
use App\Models\Documents;
use App\Models\Employee\Employee;
use App\Models\Expense\Expense;
use App\Models\Expense\ExpenseCategory;
use App\Models\Expense\ExpenseDetails;
use App\Repositories\Admin\Account\AccountsRepository;
use App\Repositories\Admin\Expense\ExpenseRepository;
use App\Repositories\UserRepository;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
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
                $expenses = Expense::with('expenseBy')->latest()->get();
                return DataTables::of($expenses)
                    ->addIndexColumn()
                    ->addColumn('transaction_way', function ($expenses) {
                        if ($expenses->transaction_way == 1) {
                            return '<span class="text-success">Cash</span>';
                        } else {
                            return '<span class="text-info">Bank</span>';
                        }
                    })
                    ->addColumn('expenseBy', function ($expenses) {
                        if($expenses->expenseBy)
                       {
                        return $expenses->expenseBy->name;
                       }
                       else{
                         return 'Admin';
                       }

                    })
                    ->addColumn('action', function ($expenses) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                    <a class="btn btn-sm btn-primary text-white " title="Show" style="cursor:pointer"
                                    href="' . route('admin.expense.expense.show', $expenses->id) . '"><i class="bx bx-show"> </i> </a>
                                    <a href="' . route('admin.expense.expense.edit', $expenses->id) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Edit"><i class="bx bxs-edit"></i></a><a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $expenses->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                                </div>';
                    })
                    ->rawColumns(['expenseBy', 'transaction_way', 'action'])
                    ->make(true);
            }
            return view('admin.expense.expense_invoice.index');
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
            $expenseCategorys = ExpenseCategory::where('status', 1)->get();
            $bankAccounts = BankAccount::where('status', 1)->get();
            $employees = Employee::all();
            $expense = Expense::all();
            $serial = count($expense) + 1;
            return view('admin.expense.expense_invoice.create', compact('expenseCategorys', 'bankAccounts', 'employees', 'serial'));
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
            'invoice_date' => 'required',
            'expense_invoice_no' => 'required|unique:expenses,expense_invoice_no',
            'expense_date' => 'required',
            'expense_by_id' => 'required',
            'expense_categorie_id' => 'required',
            'amount' => 'required',
            'total_balance' => 'required',
        ]);
        if ($request->transaction_way) {
            if ($request->transaction_way == 2) {
                $request->validate([
                    'account_id' => 'required',
                ]);
            }
        }

        DB::beginTransaction();
        try {
            $account_id = 0;
            if ($request->transaction_way) {
                $request->transaction_way == 2 ? $account_id = $request->account_id : $account_id = 0;
                $balance = AccountsRepository::postBalance($account_id);

                $balance = $balance - $request->total_balance;

                if ($balance < 0) {
                    return redirect()->back()->with('error', 'Transaction failed for insufficient balance! ');
                }
            }

            $expense = new Expense();
            $expense->adjustment_balance = $request->adjustment_balance;
            $expense->expense_invoice_no = $request->expense_invoice_no;
            $expense->vat_type = $request->vat_type;
            $expense->vat_rate = $request->vat_rate;
            $expense->expense_invoice_date = $request->invoice_date;
            $expense->expense_by = $request->expense_by_id;
            $expense->adjustment_type = $request->adjustment_type;
            $expense->total = $request->total_balance;
            $expense->transaction_way = $request->transaction_way;
            $expense->description = $request->note;
            $expense->created_by = Auth::user()->id;
            $expense->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $expense->save();

            //Expense Details
            $con = count($request->amount);

            for ($i = 0; $i < $con; $i++) {
                $expense_details = new ExpenseDetails();
                $expense_details->expense_id = $expense->id;
                $expense_details->expense_category = $request->expense_categorie_id[$i];
                $expense_details->expense_date = $request->expense_date[$i];
                $expense_details->description = $request->description[$i];
                $expense_details->amount = $request->amount[$i];
                $expense_details->created_by = Auth::user()->id;
                $expense_details->access_id = json_encode(UserRepository::accessId(Auth::id()));

                $expense_details->save();
            }

            //file
            if (isset($request->documents)){
                foreach ($request->file('documents') as $key => $image) {
                    $name = $image->getClientOriginalName();
                    $image->move(public_path() . '/img/expense/documents', $name);

                    $documents = new Documents();
                    $documents->document_id = $expense->id;
                    $documents->document_file = $name;
                    $documents->document_name = $request->document_title[$key];
                    $documents->document_type = 3; //document_type 3 == expense
                    $documents->created_by = Auth::user()->id;
                    $documents->access_id = json_encode(UserRepository::accessId(Auth::id()));

                    $documents->save();
                }
            }

            //Transaction
            if ($request->transaction_way) {
                $transaction = new Transaction();
                ExpenseRepository::transaction($transaction, $request, $expense->id);
            }

            DB::commit();
            return redirect()->route('admin.expense.expense.index')->with('message', 'Expense  Successfully');
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
        $expenseInvoice = Expense::with('expenseBy', 'createdBy')->findOrFail($id);
        $expense_details = ExpenseDetails::where('expense_id', $id)->with('expenseCategory')->get();
        $totalBalance = ExpenseDetails::where('expense_id', $id)->sum('amount');
        $transaction = Transaction::where('expense_id', $id)->with('bankAccount')->first();
        $bank = '';
        if ($transaction) {
            if ($transaction->bankAccount) {
                $bank = Bank::findOrFail($transaction->bankAccount->bank_id);
            }
        }
       // , 'documents', 'documents_title'
        return view('admin.expense.expense_invoice.show', compact('expense_details', 'expenseInvoice', 'totalBalance', 'transaction', 'bank'));
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
            $expense = Expense::where('id', $id)->with('createdBy')->first();
            $expenseCategorys = ExpenseCategory::where('status', 1)->get();
            $bankAccounts = BankAccount::where('status', 1)->get();
            $employees = Employee::all();
            $expense_details = ExpenseDetails::where('expense_id', $id)->get();
            $transaction = Transaction::where('expense_id', $id)->first();
            $total = ExpenseDetails::where('expense_id', $id)->sum('amount');
            $availableBalance = '';
            if ($transaction) {
                $availableBalance = AccountsRepository::postBalance($transaction->account_id);
            }
            $documents = Documents::where('document_id', $id)
                ->where('document_type', 3)->get();

            return view('admin.expense.expense_invoice.edit', compact('bankAccounts', 'expenseCategorys', 'expense', 'employees', 'expense_details', 'transaction', 'total', 'availableBalance', 'documents'));

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
            'invoice_date' => 'required',
            'expense_invoice_no' => 'required',
            'expense_date' => 'required',
            'expense_by_id' => 'required',
            'expense_categorie_id' => 'required',
            'amount' => 'required',
            'total_balance' => 'required',
        ]);

        if ($request->transaction_way) {
            if ($request->transaction_way == 2) {
                $request->validate([
                    'account_id' => 'required',
                ]);
            }
        }

        DB::beginTransaction();
        try {

            $account_id = 0;
            if ($request->transaction_way) {
                $request->transaction_way == 2 ? $account_id = $request->account_id : $account_id = 0;
                $balance = AccountsRepository::postBalance(0);
                $balance = $balance - $request->total_balance;

                if ($balance < 0) {
                    return redirect()->back()->with('error', 'Transaction failed for insufficient balance! ');
                }
            }

            $expense = Expense::findOrFail($id);
            $expense->adjustment_balance = $request->adjustment_balance;
            $expense->expense_invoice_no = $request->expense_invoice_no;
            $expense->vat_type = $request->vat_type;
            $expense->vat_rate = $request->vat_rate;
            $expense->expense_invoice_date = $request->invoice_date;
            $expense->expense_by = $request->expense_by_id;
            $expense->adjustment_type = $request->adjustment_type;
            $expense->total = $request->total_balance;
            $expense->transaction_way = $request->transaction_way;
            $expense->description = $request->note;
            $expense->updated_by = Auth::user()->id;

            $expense->update();

            $old_documents = Documents::where('document_type', 3)
                ->where('document_id', $id)
                ->pluck('id')->toArray();

            if ($request->expense_document_id) {
                $result = array_diff($old_documents, $request->expense_document_id);
                if ($result) {
                    Documents::whereIn('id', $result)->delete();
                }
                foreach ($request->expense_document_id as $key => $document_id) {
                    $doc = Documents::findOrFail($document_id);
                    $doc->document_name = $request->document_title[$key];
                    $doc->update();
                }
            } else {
                Documents::whereIn('id', $old_documents)->delete();
            }

            if ($request->hasfile('documents')) {
                foreach ($request->file('documents') as $key => $image) {
                    $name = $image->getClientOriginalName();
                    $image->move(public_path() . '/img/expense/documents', $name);
                    $documents = new Documents();
                    $documents->document_id = $expense->id;
                    $documents->document_file = $name;
                    $documents->document_name = $request->document_title[$key];
                    $documents->document_type = 3; //document_type 3 == expense
                    $expense->updated_by = Auth::user()->id;
                    $documents->save();
                }
            }

            if ($request->transaction_way) {
                $transaction = Transaction::where('expense_id', $id)->first();
                if ($transaction) {
                    ExpenseRepository::transaction($transaction, $request, $id);
                } else {
                    $transaction = new Transaction();
                    ExpenseRepository::transaction($transaction, $request, $id);
                }
            } else {
                $transaction = Transaction::where('expense_id', $id)->first();
                if ($transaction) {
                    $transaction->delete();
                }
            }

            $expense_details = ExpenseDetails::where('expense_id', $id)->get();
            foreach ($expense_details as $key => $expenseDetails) {
                $expenseDetails->delete();
            }

            $con = count($request->amount);
            $i = 0;
            for ($i = 0; $i < $con; $i++) {
                $expense_details = new ExpenseDetails();
                $expense_details->expense_id = $expense->id;
                $expense_details->document = $request->document;
                $expense_details->expense_category = $request->expense_categorie_id[$i];
                $expense_details->expense_date = $request->expense_date[$i];
                $expense_details->description = $request->description[$i];
                $expense_details->amount = $request->amount[$i];
                $expense_details->updated_by = Auth::user()->id;
                $expense_details->save();

            }
            DB::commit();
            return redirect()->route('admin.expense.expense.index')->with('message', 'Expense Update Successfully');
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
                $expense = Expense::where('id', $id)->first();
                $transaction = Transaction::where('expense_id', $expense->id)
                    ->first();

                $expense_details = ExpenseDetails::where('expense_id', $expense->id)
                    ->get();

                foreach ($expense_details as $key => $expenses) {
                    $expenses->delete();
                }

                $expense->delete();
                if ($transaction) {
                    $transaction->delete();
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Expense Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }

    /**
     * Status Change the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function employeeSearch(Request $request)
    {
        $result = Employee::query()
            ->where('name', 'LIKE', "%{$request->search}%")
            ->get(['name', 'id']);
        return $result;
    }

}
