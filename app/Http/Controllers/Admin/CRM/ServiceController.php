<?php

namespace App\Http\Controllers\Admin\CRM;

use App\Http\Controllers\Controller;
use App\Models\CRM\Service;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ServiceController extends Controller
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
                $service = Service::latest()->get();
                return DataTables::of($service)
                    ->addIndexColumn()
                    ->addColumn('status', function ($service) {
                        if ($service->status == 1) {
                            $status = '<button type="submit" class="btn btn-success mb-2 text-white" onclick="showStatusChangeAlert(' . $service->id . ')">Active</button>';
                        } else {
                            $status = '<button type="submit" class="btn btn-danger mb-2 text-white" onclick="showStatusChangeAlert(' . $service->id . ')">Inactive</button>';
                        }
                        return $status;
                    })
                    ->addColumn('description', function ($service) {
                        return Str::limit($service->description, 20, $end = '.....');
                    })
                    ->addColumn('action', function ($service) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                  <a href="' . route('admin.crm.service.edit', $service->id) . '" class="btn btn-success text-white" style="cursor:pointer" title="Edit"><i class="bx bxs-edit"></i></a>
                                  <a class="btn btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $service->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->rawColumns(['action', 'status', 'description'])
                    ->make(true);
            }
            return view('crm.service.index');
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
            return view('crm.service.create');
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
            'service_name' => 'required|string',
            'description' => 'string|nullable',
            'status' => 'required|numeric'
        ]);
        // Validation End

        // Store Data
        try {
            $data = new Service();
            $data->service_name = $request->service_name;
            $data->description = $request->description;
            $data->status = $request->status;
            $data->created_by = Auth::user()->id;
            $data->save();

            return redirect()->route('admin.crm.service.index')->with('toastr-success', 'Service Created Successfully');
        } catch (\Exception $exception) {
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $service = Service::findOrFail($id)->first();
            return view('crm.service.edit', compact('service'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
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
        // Validation Start
        $request->validate([
            'service_name' => 'required|string',
            'description' => 'string|nullable',
            'status' => 'required|numeric'
        ]);
        // Validation End

        try {
            $data = Service::where('id', $id)->first();
            $data->service_name = $request->service_name;
            $data->description = $request->description;
            $data->status = $request->status;
            $data->updated_by = Auth::user()->id;
            $data->save();

            return redirect()->route('admin.crm.service.index')->with('toastr-success', 'Service Updated Successfully');
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
    public function destroy(Request $request,$id)
    {
        if ($request->ajax()) {
            try {
                Service::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Item Deleted Successfully.',
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
            $service = Service::findOrFail($id);
            // Check Item Current Status
            if ($service->status == 1) {
                $service->status = 0;
            } else {
                $service->status = 1;
            }
            $service->updated_by = Auth::user()->id;
            $service->save();
            return response()->json([
                'success' => true,
                'message' => 'Service Status Update Successfully.',
            ]);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
}
