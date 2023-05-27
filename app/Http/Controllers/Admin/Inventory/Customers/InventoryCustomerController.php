<?php

namespace App\Http\Controllers\Admin\Inventory\Customers;

use DataTables;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\CRM\Client\Client;
use App\Models\CRM\Client\InterestedOn;
use App\Models\CRM\Client\ClientAssign;
use App\Models\CRM\Client\ClientType;
use App\Models\CRM\Client\ContactThrough;
use App\Models\Inventory\Customers\InventoryCustomer;

class InventoryCustomerController extends Controller
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
                // $Warehouse = Warehouse::latest()->get();
                $customers = InventoryCustomer::get();
                return DataTables::of($customers)
                    ->addIndexColumn()

                    ->addColumn('status', function ($customers) {
                        if ($customers->status == 1) {
                            $status = '<button type="submit" class="btn btn-sm btn-success mb-2 text-white" onclick="showStatusChangeAlert(' . $customers->id . ')">Active</button>';
                        } else {
                            $status = '<button type="submit" class="btn btn-sm btn-danger mb-2 text-white" onclick="showStatusChangeAlert(' . $customers->id . ')">Inactive</button>';
                        }
                        return $status;
                    })

                    ->addColumn('customer_type_priority', function ($customers) {
                        if ($customers->customer_type_priority == 1) {
                            return '<span style="color:#536DE7 ">First </span>';
                        } else if ($customers->customer_type_priority == 2) {
                            return '<span style="color:#536DE7 ">Second</span>';
                        } else if ($customers->customer_type_priority == 3) {
                            return '<span style="color:#536DE7 ">Third</span>';
                        } else {
                            return '--';
                        }
                    })

                    ->addColumn('description', function ($customers) {
                        return Str::limit($customers->description, 20, $end = '.....');
                    })

                    ->addColumn('action', function ($customers) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                    <a class="btn btn-sm btn-primary text-white " title="Show"style="cursor:pointer"href="' . route('admin.inventory.customers.customer.show', $customers->id) . '"><i class="bx bx-show"> </i> </a>
                                    <a href="' . route('admin.inventory.customers.customer.edit', $customers->id) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Edit"><i class="bx bxs-edit"></i></a>
                                    <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $customers->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                                </div>';
                    })
                    ->rawColumns(['action', 'status', 'description', 'customer_type_priority'])
                    ->make(true);
            }
            return view('admin.inventory.customers.customer.index');
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
            $ClientTypes       =   ClientType::where('status', 1)->get();
            $InterestedsOn     =   InterestedOn::where('status', 1)->get();
            return view('admin.inventory.customers.customer.create',
                compact('ClientTypes', 'InterestedsOn'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function getCustomerTypePriority(Request $request, $id)
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        // Validation Start
        $request->validate([
            'name'              =>      'required',
            'email'             =>      'required',
            'phone'             =>      'required',
            'customer_type'     =>      'required',
        ]);
        // Validation End
        // Store Data
        try {
            $data                           =       new InventoryCustomer();
            $data->name                     =       $request->name;
            $data->email                    =       $request->email;
            $data->phone                    =       $request->phone;
            $data->customer_type            =       $request->customer_type;
            $data->customer_type_priority   =       $request->customer_type_priority;
            $data->interested_on            =       $request->interested_on;
            $data->status                   =       $request->status;
            $data->description              =       strip_tags($request->description);
            $data->created_by               =       Auth::user()->id;
            $data->access_id                =       json_encode(UserRepository::accessId(Auth::id()));
            $data->save();

            return redirect()->route('admin.inventory.customers.customer.index')
                    ->with('toastr-success', 'Customer Created Successfully');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer           =       InventoryCustomer::findOrFail($id);
        $ClientTypes        =       ClientType::where('status', 1)->get();
        $InterestedsOn      =       InterestedOn::where('status', 1)->get();
        return view('admin.inventory.customers.customer.show',
            compact('customer', 'ClientTypes', 'InterestedsOn'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $customer          =   InventoryCustomer::findOrFail($id);
            $ClientTypes       =   ClientType::where('status', 1)->get();
            $InterestedsOn     =   InterestedOn::where('status', 1)->get();
            return view('admin.inventory.customers.customer.edit',
                compact('ClientTypes', 'InterestedsOn', 'customer'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validation Start
        $request->validate([
            'name'              =>      'required',
            'email'             =>      'required',
            'customer_type'     =>      'required',
            'interested_on'     =>      'required',
        ]);
        // Validation End

        try {
            $data                               =           InventoryCustomer::findOrFail($id);
            $data->name                         =           $request->name;
            $data->email                        =           $request->email;
            $data->phone                        =           $request->phone;
            $data->customer_type                =           $request->customer_type;
            $data->customer_type_priority       =           $request->customer_type_priority;
            $data->interested_on                =           $request->interested_on;
            $data->address                      =           $request->address;
            $data->description                  =           $request->description;
            $data->updated_by                   =           Auth::user()->id;

            $data->update();

            return redirect()->route('admin.inventory.customers.customer.index')->with('message', 'Customer Updated successfully.');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                InventoryCustomer::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Customer Deleted Successfully.',
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

    public function statusUpdate(Request $request, $id)
    {
        try {
            $customer = InventoryCustomer::findOrFail($id);
            // Check Item Current Status
            if ($customer->status == 1) {
                $customer->status = 0;
            } else {
                $customer->status = 1;
            }

            $customer->save();
            return response()->json([
                'success' => true,
                'message' => 'Customer Status Update Successfully.',
            ]);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
}




