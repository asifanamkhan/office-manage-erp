<?php

namespace App\Http\Controllers\Admin\CRM\Client;

use App\Exports\ClientExport;
use App\Http\Controllers\Controller;
use App\Imports\ClientImport;
use App\Models\Account\Bank;
use App\Models\Account\BankAccount;
use App\Models\CRM\Client\Client;
use App\Models\CRM\Client\ClientType;
use App\Models\CRM\Client\ContactThrough;
use App\Models\CRM\Client\InterestedOn;
use App\Models\Employee\EmployeeDocuments;
use App\Models\Employee\EmployeeIdentity;
use App\Models\Employee\EmployeeReference;
use App\Models\Identity;
use App\Models\Settings\Priority;
use App\Models\User;
use App\Repositories\UserRepository;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class ClientController extends Controller
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
                if(Auth::id() != 1){
                    $Client = DB::table('clients')
                        ->where('deleted_at', null)
                        ->whereJsonContains('assign_to',Auth::id())
                        ->orwhere('is_assign',false)
                        ->latest()->get();

                }else{
                    $Client = DB::table('clients')->latest()->get();
                }

                return DataTables::of($Client)
                    ->addIndexColumn()
                    ->addColumn('image', function ($Client) {
                        $url = asset('img/client/' . $Client->image);
                        $url2 = asset('img/no-image/noman.jpg');
                        if ($Client->image) {
                            return '<img src="' . $url . '" border="0" width="40"  align="center" />';
                        }
                        return '<img src="' . $url2 . '" border="0" width="40"  align="center" />';
                    })
                    ->addColumn('name', function ($Client) {
                        return '<a class="text-primary" style="cursor:pointer;text-decoration: none;"
                                 href="' . route('admin.crm.client.show', $Client->id) . '"> ' . $Client->name . ' </a>';
                    })
                    ->addColumn('client_type_priority', function ($Client) {
                        if ($Client->client_type_priority == 1) {
                            return '<span style="color:#536DE7 ">First </span>';
                        } else if ($Client->client_type_priority == 2) {
                            return '<span style="color:#536DE7 ">Second</span>';
                        } else if ($Client->client_type_priority == 3) {
                            return '<span style="color:#536DE7 ">Third</span>';
                        } else {
                            return '--';
                        }
                    })
                    ->addColumn('status', function ($Client) {
                        if ($Client->status == 1) {
                            return '<button onclick="showStatusChangeAlert(' . $Client->id . ')"class="btn btn-sm btn-primary">Active</button>';
                        } else {
                            return '<button onclick="showStatusChangeAlert(' . $Client->id . ')"class="btn btn-sm btn-warning">Inactive</button>';
                        }
                    })
                    ->addColumn('action', function ($Client) {
                        return '<div class="btn-group" role="group"aria-label="Basic mixed styles example">
                                    <a class="btn btn-sm btn-info text-white " title="Comment" style="cursor:pointer"href="' . route('admin.crm.client.comment', $Client->id) . '"><i class="bx bx-comment-check"></i></a>
                                    <a class="btn btn-sm btn-success text-white " title="Reminder" style="cursor:pointer"href="' . route('admin.crm.client.reminder', $Client->id) . '"><i class="bx bx-stopwatch"></i> </a>
                                    <a class="btn btn-sm btn-info text-white " title="Profile"style="cursor:pointer"href="' . route('admin.crm.client.profile', $Client->id) . '"><i class="bx bx-show"></i> </a>
                                    <a class="btn btn-sm btn-primary text-white " title="Show"style="cursor:pointer"href="' . route('admin.crm.client.show', $Client->id) . '"><i class="bx bx-user "> </i> </a>
                                    <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $Client->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                                </div>';
                    })
                    ->rawColumns(['image', 'name', 'status', 'client_type_priority', 'action'])
                    ->make(true);
            }
            return view('admin.crm.client.index');
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
        try{
            $ClientTypes = ClientType::where('status', 1)->get();
            $ContactThrough = ContactThrough::where('status', 1)->get();
            $InterestedsOn = InterestedOn::where('status', 1)->get();
            $priorities = Priority::where('status', 1)->get();
            return view('admin.crm.client.create', compact('ClientTypes', 'ContactThrough', 'InterestedsOn','priorities'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validation Start
        $request->validate([
            'client_name' => 'required',
            'client_type' => 'required',
            'contact_through' => 'required',
            'email' => 'required|unique:users,email,NULL,id,deleted_at,NULL',
            'primary_phone' => 'required|unique:users,mobile,NULL,id,deleted_at,NULL',
        ]);
        // Validation End
        // Store Data
        DB::beginTransaction();
        try {
            $data = new Client();
            $data->name = $request->client_name;
            $data->email = $request->email;
            $data->phone_primary = $request->primary_phone;
            $data->client_type = $request->client_type;
            $data->client_type_priority = $request->client_type_priority;
            $data->contact_through = $request->contact_through;
            $data->interested_on = $request->interested_on;
            $data->status = $request->status;
            $data->description = $request->description;
            $data->created_by = Auth::user()->id;
            $data->assign_to = json_encode([Auth::id()]);
            Auth::id() == 1 ? $data->is_assign = 0 : $data->is_assign = 1;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $data->save();

            $user = new User();
            $user->name = $request->client_name;
            $user->email = $request->email;
            $user->mobile = $request->primary_phone;
            $user->password = Hash::make('client');
            $user->user_id = $data->id;
            $user->user_type = 2; // client userType = 2
            $user->record_access = 1;
            $user->role_id = 2;
            $user->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $user->created_by = Auth::id();
            $user->save();

            DB::table('model_has_roles')->insert([
                'role_id' => 2,
                'model_type' => 'App\\Models\\User',
                'model_id' => $user->id,
            ]);

            DB::commit();

            return redirect()->route('admin.crm.client.index')->with('message', 'Create successfully.');
        } catch (\Exception $exception) {

            DB::rollBack();
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $Client = Client::findOrFail($id);
            $ClientTypes = ClientType::where('status', 1)->get();
            $ContactThrough = ContactThrough::where('status', 1)->get();
            $InterestedsOn = InterestedOn::where('status', 1)->get();
            $ClientIdentity = EmployeeIdentity::where('employee_id', $id)->where('user_type', 2)->get();
            $identities = Identity::where('status', 1)->get();
            $banks = Bank::where('status', 1)->get();
            return view('admin.crm.client.show', compact('Client', 'ClientTypes', 'ContactThrough', 'InterestedsOn', 'identities', 'ClientIdentity', 'banks'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user =User::where('user_id',$id)->where('user_type',2)->first();
        // Validation Start
        $request->validate([
            'client_name' => 'required',
            'email' => 'required|unique:users,email,NULL,id,deleted_at,NULL'.$user->user_id,
            'primary_phone' => 'required|unique:users,mobile,NULL,id,deleted_at,NULL'.$user->user_id,
            'client_type' => 'required',
            'contact_through' => 'required',
        ]);
        // Validation End

        try {
            $data = Client::findOrFail($id);
            $data->name = $request->client_name;
            $data->email = $request->email;
            $data->phone_primary = $request->primary_phone;
            $data->phone_secondary = $request->phone_secondary;
            $data->client_type = $request->client_type;
            $data->client_type_priority = $request->client_type_priority;
            $data->contact_through = $request->contact_through;
            $data->interested_on = $request->interested_on;
            $data->address = $request->address;
            $data->description = $request->description;
            $data->updated_by = Auth::user()->id;

            if ($request->file()) {
                $file = $request->file('image');
                $filename = time() . $file->getClientOriginalName();
                $file->move(public_path('/img/client/'), $filename);
                $data->image = $filename;
            }

            $data->update();
            //store data user table
            $user =User::where('user_id',$id)->where('user_type',2)->first();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->primary_phone;
            $user->user_id =$data->id;
            $user->update();

            return redirect()->route('admin.crm.client.show', $id)->with('message', 'Update successfully.');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                $client = Client::findOrFail($id);
                if ($client) {
                    $user = User::where('user_id', $id)->where('user_type', 2)->first();
                    DB::table('model_has_roles')->where('role_id', 2)->where('model_id', $user->id)->delete();
                    $user->delete();
                    $client->delete();

                    return response()->json([
                        'success' => true,
                        'message' => 'Client Deleted Successfully.',
                    ]);
                }
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }
    public function getClientTypePriority(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                $ClientType = ClientType::findOrFail($id);
                return response()->json($ClientType);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }
    //starts status change function
    public function statusUpdate(Request $request)
    {
        try {
            $client = Client::findOrFail($request->id);

            $client->status == 1 ? $client->status = 0 : $client->status = 1;

            $client->update();

            if ($client->status == 1) {
                return "active";
            } else {
                return "inactive";
            }
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }
    public function ClientAddressUpdate(Request $request, $id)
    {
        $request->validate([
            'present_address' => 'required',
            'permanent_address' => 'required',
            'country' => 'required|numeric',
            'states' => 'required|numeric',
            'cities' => 'required|numeric',
            'zip' => 'required',
        ]);
        try {
            $data = Client::findOrFail($id);
            $data->present_address = $request->present_address;
            $data->permanent_address = $request->permanent_address;
            $data->country_id  = $request->country;
            $data->state_id = $request->states;
            $data->city_id = $request->cities;
            $data->zip = $request->zip;
            $data->update();

            return redirect()->route('admin.crm.client.show', $id)->with('message', 'Address Update successfully.');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function clientComment($id)
    {
        try{
            $Client = Client::findOrFail($id);
            $ClientTypes = ClientType::where('status', 1)->get();
            $ContactThrough = ContactThrough::where('status', 1)->get();
            $InterestedsOn = InterestedOn::where('status', 1)->get();
            $ClientIdentity = EmployeeIdentity::where('employee_id', $id)->where('user_type', 2)->get();
            $identities = Identity::where('status', 1)->get();
            return view('admin.crm.client.comment.comment-show', compact('Client', 'ClientTypes', 'ContactThrough', 'InterestedsOn', 'identities', 'ClientIdentity'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function clientReminder($id)
    {
        try{
            $Client = Client::findOrFail($id);
            $ClientTypes = ClientType::where('status', 1)->get();
            $ContactThrough = ContactThrough::where('status', 1)->get();
            $InterestedsOn = InterestedOn::where('status', 1)->get();
            $ClientIdentity = EmployeeIdentity::where('employee_id', $id)->where('user_type', 2)->get();
            $identities = Identity::where('status', 1)->get();
            return view('admin.crm.client.reminder.reminder-show', compact('Client', 'ClientTypes', 'ContactThrough', 'InterestedsOn', 'identities', 'ClientIdentity'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function clientProfile($id)
    {
        try{
            $Client = Client::with('interestedOn','contactThrough','comments','reminders')->findOrFail($id);
            $ClientTypes = ClientType::where('status', 1)->get();
            $documents = EmployeeDocuments::where('employee_id',$id)->where('user_type',2)->get();
            $EmployeeIdentity = EmployeeIdentity::where('employee_id',$id)
                    ->where('user_type',2)
                    ->with('employee','identity')
                    ->get();
            $ClientReferences = EmployeeReference::where('employee_id',$id) ->where('user_type',2)
            ->with('reference')
            ->get();
            $BankAccounts = BankAccount::where('user_id', $id)->where('account_type', 3)->with('bank')->get();
            return view('admin.crm.client.client-details',compact('Client','documents','EmployeeIdentity','ClientReferences','BankAccounts'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function import()
    {
        Excel::import(new ClientImport,request()->file('file'));
        return redirect()->back()->with('message', 'Client Import successfully.');

    }
    public function export()
    {
        return Excel::download(new ClientExport, 'users.xlsx');
        //return (new ClientExport)->download('users.xlsx',Excel::XLSX);
    }

}
