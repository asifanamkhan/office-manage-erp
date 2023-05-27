<?php

namespace App\Http\Controllers\Admin\Revenue;

use App\Http\Controllers\Controller;
use App\Models\Account\Bank;
use App\Models\Account\BankAccount;
use App\Models\Account\Transaction;
use App\Models\Documents;
use App\Models\Employee\Employee;
use App\Models\Revenue\Revenue;
use App\Models\Revenue\RevenueCategory;
use App\Models\Revenue\RevenueDetails;
use App\Repositories\UserRepository;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RevenueController extends Controller
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
                $revenue = Revenue::with('createdBy', 'revenueBy')->latest()->get();
                return DataTables::of($revenue)
                    ->addIndexColumn()
                    ->addColumn('transaction_way', function ($revenue) {
                        if ($revenue->transaction_way == 1) {
                            return '<span class="text-success">Cash</span>';
                        } else {
                            return '<span class="text-info">Bank</span>';
                        }
                    })
                    ->addColumn('revenueBy', function ($revenue) {
                        return $revenue->revenueBy->name;

                    })
                    ->addColumn('description', function ($revenue) {
                        return Str::limit($revenue->description, 20, $end = '.....');
                    })
                    ->addColumn('action', function ($revenue) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example"><a class="btn btn-sm btn-primary text-white " title="Show" style="cursor:pointer"
                        href="' . route('admin.revenue.show', $revenue->id) . '"><i class="bx bx-show"> </i> </a><a href="' . route('admin.revenue.edit', $revenue->id) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Edit"><i class="bx bxs-edit"></i></a><a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $revenue->id . ')" title="Delete"><i class="bx bxs-trash"></i></a></div>';
                    })
                    ->rawColumns(['transaction_way', 'action', 'revenueBy', 'description'])
                    ->make(true);
            }
            return view('admin.revenue.revenue_invoice.index');
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
            $revenueCategorys = RevenueCategory::where('status', 1)->get();
            $bankAccounts = BankAccount::where('status', 1)->get();
            $serial = Revenue::count()+1;
            return view('admin.revenue.revenue_invoice.create', compact('revenueCategorys', 'bankAccounts', 'serial'));
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
            'revenue_invoice_no' => 'required|unique:revenues,revenue_invoice_no',
            'revenue_date' => 'required',
            'revenue_by_id' => 'required',
            'revenue_categorie_id' => 'required',
            'description' => 'required',
            'amount' => 'required',
            'total_balance' => 'required',
            // 'note' => 'required',
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

            $revenue = new Revenue();

            $revenue->adjustment_balance = $request->adjustment_balance;
            $revenue->revenue_invoice_date = $request->invoice_date;
            $revenue->vat_type = $request->vat_type;
            $revenue->vat_rate = $request->vat_rate;
            $revenue->revenue_invoice_no = $request->revenue_invoice_no;
            $revenue->revenue_by = $request->revenue_by_id;
            $revenue->adjustment_type = $request->adjustment_type;
            $revenue->total = $request->total_balance;
            $revenue->transaction_way = $request->transaction_way;
            $revenue->description = $request->note;
            $revenue->created_by = Auth::user()->id;
            $revenue->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $revenue->save();

            if($request->file('documents')){
                foreach ($request->file('documents') as $key => $image) {

                    $name = $image->getClientOriginalName();
                    $image->move(public_path() . '/img/revenue/documents', $name);

                    $documents = new Documents();
                    $documents->document_id = $revenue->id;
                    $documents->document_file = $name;
                    $documents->document_name = $request->document_title[$key];
                    $documents->document_type = 4; //document_type 4 == revenue

                    $documents->created_by = Auth::user()->id;
                    $documents->access_id = json_encode(UserRepository::accessId(Auth::id()));

                    $documents->save();
                }
            }


            if ($request->transaction_way != null) {

                $transaction = new Transaction();

                $transaction->transaction_title = "Revenue";
                $transaction->transaction_date = $request->invoice_date;
                if ($request->transaction_way == 2) {
                    $transaction->account_id = $request->account_id;
                } else {
                    $transaction->account_id = 0;
                }

                $transaction->transaction_purpose = 3;
                $transaction->transaction_type = 2;
                $transaction->amount = $request->total_balance;
                $transaction->revenue_id = $revenue->id;
                $transaction->cheque_number = $request->cheque_number;
                $transaction->description = $request->note;
                $transaction->created_by = Auth::user()->id;
                $transaction->access_id = json_encode(UserRepository::accessId(Auth::id()));
                $transaction->save();
            }
            $con = count($request->amount);

            for ($i = 0; $i < $con; $i++) {
                $expense_details = new RevenueDetails();
                $expense_details->revenue_id = $revenue->id;
                $expense_details->revenue_category = $request->revenue_categorie_id[$i];
                $expense_details->revenue_date = $request->revenue_date[$i];
                $expense_details->description = $request->description[$i];
                $expense_details->amount = $request->amount[$i];


                $expense_details->created_by = Auth::user()->id;
                $expense_details->access_id = json_encode(UserRepository::accessId(Auth::id()));

                $expense_details->save();
            }
            DB::commit();
            return redirect()->route('admin.revenue.index')->with('message', 'Revenue  Successfully');
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
        $revenueInvoice = Revenue::with('revenueBy', 'createdBy')->findOrFail($id);
        $revenue_details = RevenueDetails::where('revenue_id', $id)->with('revenueCategory')->get();
        $totalBalance = RevenueDetails::where('revenue_id', $id)->sum('amount');
        $transaction = Transaction::where('revenue_id', $id)->with('bankAccount')->first();
        if ($transaction->bankAccount != null) {
            $bank = Bank::findOrFail($transaction->bankAccount->bank_id);
        } else {
            $bank = '';
        }
        $documents = json_decode($revenueInvoice->document);
        $documents_title = json_decode($revenueInvoice->document_title);

        return view('admin.revenue.revenue_invoice.show', compact('revenue_details', 'revenueInvoice', 'totalBalance', 'transaction', 'bank', 'documents', 'documents_title'));
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
            $revenue = Revenue::where('id', $id)->first();
            $revenueCategorys = RevenueCategory::where('status', 1)->get();
            $bankAccounts = BankAccount::where('status', 1)->get();
            $employees = Employee::get(['name','id']);
            $revenue_details = RevenueDetails::where('revenue_id', $id)->get();
            $transaction = Transaction::where('revenue_id', $id)->first();
            $total = $revenue_details->sum('amount');
            $documents = Documents::where('document_id', $id)->where('document_type', 4)->get();
            return view('admin.revenue.revenue_invoice.edit', compact('bankAccounts', 'revenueCategorys', 'revenue', 'employees', 'revenue_details', 'transaction', 'total', 'documents'));
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
            'revenue_invoice_no' => 'required',
            'revenue_date' => 'required',
            'revenue_by_id' => 'required',
            'revenue_categorie_id' => 'required',
            'description' => 'required',
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
            $revenue = Revenue::findOrFail($id);
            $revenue->adjustment_balance = $request->adjustment_balance;
            $revenue->revenue_invoice_date = $request->invoice_date;
            $revenue->vat_type = $request->vat_type;
            $revenue->vat_rate = $request->vat_rate;
            $revenue->revenue_invoice_no = $request->revenue_invoice_no;
            $revenue->revenue_by = $request->revenue_by_id;
            $revenue->adjustment_type = $request->adjustment_type;
            $revenue->total = $request->total_balance;
            $revenue->transaction_way = $request->transaction_way;
            $revenue->description = strip_tags($request->note);
            $revenue->updated_by = Auth::user()->id;
            $revenue->update();

            // revenue document Update
            $old_documents = Documents::where('document_type',4)
            ->where('document_id',$id)
            ->pluck('id')->toArray();

        if ($request->revenue_document_id) {
            $result=array_diff($old_documents,$request->revenue_document_id);
            if($result){
                Documents::whereIn('id', $result)->delete();
            }
            foreach ($request->revenue_document_id as $key=>$document_id) {
                $doc = Documents::findOrFail($document_id);
                $doc->document_name = $request->document_title[$key];
                $doc->update();
            }
        }else{
            Documents::whereIn('id', $old_documents)->delete();
        }

        if ($request->hasfile('documents')) {
            foreach ($request->file('documents') as $key => $image) {
                $name = $image->getClientOriginalName();
                $image->move(public_path() . '/img/revenue/documents', $name);
                $documents = new Documents();
                $documents->document_id = $revenue->id;
                $documents->document_file = $name;
                $documents->document_name = $request->document_title[$key];
                $documents->document_type = 4; //document_type 4 == revenue
                $revenue->updated_by = Auth::user()->id;
                $documents->save();
            }
        }

            if ($request->transaction_way) {
                $transaction = Transaction::where('revenue_id', $id)->first();

                if ($transaction) {
                    $transaction->transaction_title = "Revenue";
                    $transaction->transaction_date = $request->invoice_date;
                    if ($request->transaction_way == 2) {
                        $transaction->account_id = $request->account_id;
                    } else {
                        $transaction->account_id = 0;
                    }
                    $transaction->transaction_purpose = 3;
                    $transaction->transaction_type = 2;
                    $transaction->amount = $request->total_balance;
                    $transaction->revenue_id = $revenue->id;
                    $transaction->cheque_number = $request->cheque_number;
                    $transaction->description = $request->note;
                    $transaction->updated_by = Auth::user()->id;
                    $transaction->update();
                } else {
                    $transaction = new Transaction();
                    $transaction->transaction_title = "Revenue";
                    $transaction->transaction_date = $request->invoice_date;
                    if ($request->transaction_way == 2) {
                        $transaction->account_id = $request->account_id;
                    } else {
                        $transaction->account_id = 0;
                    }
                    $transaction->transaction_purpose = 3;
                    $transaction->transaction_type = 2;
                    $transaction->amount = $request->total_balance;
                    $transaction->revenue_id = $revenue->id;
                    $transaction->cheque_number = $request->cheque_number;
                    $transaction->description = $request->note;
                    $transaction->created_by = Auth::user()->id;
                    $transaction->access_id = json_encode(UserRepository::accessId(Auth::id()));
                    $transaction->save();
                }
            } else {
                $transaction = Transaction::where('revenue_id', $id)->first();
                if ($transaction) {
                    $transaction->delete();
                }
            }

            $revenue_details = RevenueDetails::where('revenue_id', $id)->get();
            foreach ($revenue_details as $key => $revenueDetails) {
                $revenueDetails->delete();
            }

            $con = count($request->amount);

            for ($i = 0; $i < $con; $i++) {
                $expense_details = new RevenueDetails();
                $expense_details->revenue_id = $revenue->id;
                $expense_details->revenue_category = $request->revenue_categorie_id[$i];
                $expense_details->revenue_date = $request->revenue_date[$i];
                $expense_details->description = strip_tags($request->description[$i]);
                $expense_details->amount = $request->amount[$i];

                $expense_details->updated_by = Auth::user()->id;
                $expense_details->save();
            }
            DB::commit();
            return redirect()->route('admin.revenue.index')->with('message', 'Revenue  Successfully');
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
                $revenue = Revenue::where('id', $id)->first();

                $transaction = Transaction::where('revenue_id', $revenue->id)->first();

                $revenue_details = RevenueDetails::where('revenue_id', $revenue->id)->get();

                foreach ($revenue_details as $key => $expenses) {
                    $expenses->delete();
                }
                $revenue->delete();
                $transaction->delete();


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


}




