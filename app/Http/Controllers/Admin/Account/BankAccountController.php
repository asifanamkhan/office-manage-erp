<?php

namespace App\Http\Controllers\Admin\Account;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Account\Bank;
use App\Models\Account\BankAccount;
use App\Models\Account\Transaction;
use App\Models\CRM\Client\Client;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DataTables;

class BankAccountController extends Controller
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
                $bankAccounts = BankAccount::latest()->get();
                return DataTables::of($bankAccounts)
                    ->addIndexColumn()
                    ->addColumn('status', function ($bankAccounts) {
                        if ($bankAccounts->status == 1) {
                            return '<button
                            onclick="showStatusChangeAlert(' . $bankAccounts->id . ')"
                             class="btn btn-sm btn-primary">Active</button>';
                        } else {
                            return '<button
                            onclick="showStatusChangeAlert(' . $bankAccounts->id . ')"
                            class="btn btn-sm btn-warning">In Active</button>';
                        }
                    })
                    ->addColumn('bankinfo', function ($bankAccounts) {
                        if ($bankAccounts->type == 2) {
                            return $bankAccounts->branch_name;
                        }
                        else{
                            return '<span class="badge bg-success text-light">Cash</span>';
                        }
                    })
                    ->addColumn('action', function ($bankAccounts) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                        <a class="btn btn-sm btn-success text-white " style="cursor:pointer"
                        href="' . route('admin.account.bank-account.edit', $bankAccounts->id) . '" title="Edit"><i class="bx bxs-edit"></i></a><a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $bankAccounts->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->rawColumns(['action', 'status', 'bankinfo'])
                    ->make(true);
            }
            return view('admin.account.bank_account.index');
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
            $banks = Bank::where('status', 1)->get();
            return view('admin.account.bank_account.create',compact('banks'));
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
            'account_number' => 'required|unique:bank_accounts,account_number',
            'account_name' => 'required|unique:bank_accounts,name',
            'status' => 'required',
            'initial_balance' => 'required|numeric',
        ]);
        if($request->type == 2)//account 1 == cash ,2 == bank
        {
            $request->validate([
            'bank_id' => 'required',
            'branch_name' => 'required',
             ]);
        }
        DB::beginTransaction();
        try {
            // Bank Account Store
            $bankAccount = new BankAccount();
            if($request->type == 2){
                $bankAccount->branch_name = $request->branch_name;
                $bankAccount->bank_id = $request->bank_id;
            }
            $bankAccount->type = $request->type;
            $bankAccount->account_number = $request->account_number;
            $bankAccount->routing_no = $request->routing_no;
            $bankAccount->initial_balance = $request->initial_balance;
            $bankAccount->name = $request->account_name;
            $bankAccount->status = $request->status;
            $bankAccount->note = $request->description;
            $bankAccount->created_by = Auth::user()->id;
            $bankAccount->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $bankAccount->save();

            $transaction = new Transaction();
            $transaction->transaction_title = 'Initial Balance Deposit';
            $transaction->account_id = $bankAccount->id;
            $transaction->transaction_date = Carbon::now();
            $transaction->transaction_purpose = 0;
            $transaction->transaction_type = 2;
            $transaction->transaction_account_type = $request->type;
            $transaction->cheque_number = $request->cheque_number;
            $transaction->amount = $request->initial_balance;
            $transaction->status = $request->status;

            $transaction->created_by = Auth::user()->id;
            $transaction->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $transaction->save();

            DB::commit();
           return redirect()->route('admin.account.bank-account.index')->with('message', 'Add successfully.');
        } catch (\Exception $exception) {

            DB::rollBack();
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
            $parameter = "account-list";
            $banks = Bank::where('status', 1)->get();
            $bankAccount = BankAccount::where('id', $id)->first();
            $Client = '';
           return view('admin.account.bank_account.edit',compact('bankAccount','banks','parameter','Client'));
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

    }
    public function bankAccountUpdate(Request $request, $id)
    {
        $request->validate([
            'account_number' => 'required',
            'account_name' => 'required',
            'status' => 'required',
            'initial_balance' => 'required',
        ]);
        if($request->type == 2)//account 1 == cash ,2 == bank
        {
            $request->validate([
            'bank_id' => 'required',
            'branch_name' => 'required',
             ]);
        }
        DB::beginTransaction();
        try {
            // Bank Account Store
            $bankAccount = BankAccount::find($id);

            if($request->type == 2){
                $bankAccount->branch_name = $request->branch_name;
                $bankAccount->bank_id = $request->bank_id;
            }
            $bankAccount->type = $request->type;
            $bankAccount->account_number = $request->account_number;
            $bankAccount->routing_no = $request->routing_no;
            $bankAccount->initial_balance = $request->initial_balance;
            $bankAccount->name = $request->account_name;
            $bankAccount->status = $request->status;
            $bankAccount->note = $request->description;
            if($request->client_id){
                $bankAccount->user_id = $request->client_id;
                $bankAccount->account_type = 3; //type 3 == client bank account
            }
            $bankAccount->updated_by = Auth::user()->id;
            $bankAccount->update();

            $transaction =Transaction::where('account_id',$bankAccount->id)->first();
            $transaction->transaction_title = 'Initial Balance Deposit';
            $transaction->account_id = $bankAccount->id;
            $transaction->transaction_date = Carbon::now();
            $transaction->transaction_purpose = 0;
            $transaction->transaction_type = 2;
            $transaction->transaction_account_type = $request->type;
            $transaction->amount = $request->initial_balance;
            $transaction->status = $request->status;
            $transaction->updated_by = Auth::user()->id;
            $transaction->update();

            DB::commit();
            if($request->parameter == 'account-list'){
                return redirect()->route('admin.account.bank-account.index')->with('message', 'Update successfully.');
            }
            else{
                $Client = Client::findOrFail($request->client_id);
                return redirect()->route('admin.crm.client.show', $Client->id)->with('message', ' Update successfully.');
            }

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
                BankAccount::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Bank Account Deleted Successfully.',
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
           $reference=BankAccount::findOrFail($request->id);

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
