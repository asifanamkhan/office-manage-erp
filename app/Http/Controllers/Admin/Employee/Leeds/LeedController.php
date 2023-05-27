<?php

namespace App\Http\Controllers\Admin\Employee\Leeds;

use App\Http\Controllers\Controller;
use App\Models\CRM\Client\Client;
use App\Models\Employee\Department;
use App\Models\Employee\Employee;
use App\Repositories\UserRepository;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   public function index(Request $request)
   {

   }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
           return view('admin.employee.Leeds.leed-show');
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
            'name' => 'required',
            'status' => 'required',

        ]);
        // Validation End

        // Store Data
        try {
            $data = new Department();
            $data->name = $request->name;
            $data->status = $request->status;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $data->description = $request->description;
            $data->created_by = Auth::id();

            $data->save();
            return redirect()->route('admin.department.index')->with('message', 'Create successfully.');
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
    public function show(Request $request ,$id)
    {
        try {
            $employee = Employee::findOrFail($id);
            if ($request->ajax()) {
                    // $Clients = Client::where('created_by',1)->with('createdBy','interestedOn')->get();
                    if(Auth::id() != 1){
                        $Clients = Client::whereJsonContains('assign_to',Auth::id())
                            ->with('createdBy','interestedOn')
                            ->orwhere('is_assign',false)
                            ->latest()->get();
                    }else{
                        $Clients = Client::latest()
                            ->with('createdBy','interestedOn')
                            ->get();
                    }
                return DataTables::of($Clients)
                    ->addIndexColumn()
                    ->addColumn('addedBy', function ($Clients) {
                        if($Clients->createdBy){
                           return $Clients->createdBy->name;
                    }
                    else{
                        return ' -- ';
                    }
                    })
                    ->addColumn('interestedOn', function ($Clients) {
                        if($Clients->interestedOn){
                           return $Clients->interestedOn->name;
                    }
                    else{
                        return ' -- ';
                    }
                    })
                    ->addColumn('action', function ($Clients) {
                            return '<div class="btn-group" role="group"aria-label="Basic mixed styles example"><a class="btn btn-sm btn-info text-white " title="Comment" style="cursor:pointer"href="' . route('admin.crm.client.comment', $Clients->id) . '"><i class="bx bx-comment-check"></i></a><a class="btn btn-sm btn-success text-white " title="Reminder" style="cursor:pointer"href="' . route('admin.crm.client.reminder', $Clients->id) . '"><i class="bx bx-stopwatch"></i> </a></div>';

                    })
                    ->rawColumns(['addedBy','interestedOn','action'])
                    ->make(true);
            }
            return view('admin.employee.Leeds.leed-show',compact('employee'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function ownLeeds(Request $request ,$id)
    {
        try {
            $employee = Employee::findOrFail($id);
            if ($request->ajax()) {
                if(Auth::user()->id == 1){
                    $Clients = Client::where('created_by',1)->with('createdBy','interestedOn')->get();
                }
                else{
                 $Clients = Client::where('created_by',Auth::user()->id)->with('createdBy','interestedOn')->get();
                }
                return DataTables::of($Clients)
                    ->addIndexColumn()
                    ->addColumn('addedBy', function ($Clients) {
                        if($Clients->createdBy){
                           return $Clients->createdBy->name;
                    }
                    else{
                        return ' -- ';
                    }
                    })
                    ->addColumn('interestedOn', function ($Clients) {
                        if($Clients->interestedOn){
                           return $Clients->interestedOn->name;
                    }
                    else{
                        return ' -- ';
                    }
                    })
                    ->addColumn('action', function ($Clients) {
                            return '<div class="btn-group" role="group"aria-label="Basic mixed styles example"><a class="btn btn-sm btn-info text-white " title="Comment" style="cursor:pointer"href="' . route('admin.crm.client.comment', $Clients->id) . '"><i class="bx bx-comment-check"></i></a><a class="btn btn-sm btn-success text-white " title="Reminder" style="cursor:pointer"href="' . route('admin.crm.client.reminder', $Clients->id) . '"><i class="bx bx-stopwatch"></i> </a></div>';

                    })
                    ->rawColumns(['addedBy','interestedOn','action'])
                    ->make(true);
            }
            return view('admin.employee.Leeds.leed-show',compact('employee'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }


    }
    public function assignLeeds(Request $request ,$id)
    {
        try {
            $employee = Employee::findOrFail($id);
            if ($request->ajax()) {
                $Clients = Client::whereJsonContains('assign_to',Auth::id())
                            ->with('createdBy','interestedOn')
                            ->get();

                return DataTables::of($Clients)
                    ->addIndexColumn()
                    ->addColumn('addedBy', function ($Clients) {
                        if($Clients->createdBy){
                           return $Clients->createdBy->name;
                    }
                    else{
                        return ' -- ';
                    }
                    })
                    ->addColumn('action', function ($Clients) {
                            return '<div class="btn-group" role="group"aria-label="Basic mixed styles example"><a class="btn btn-sm btn-info text-white " title="Comment" style="cursor:pointer"href="' . route('admin.crm.client.comment', $Clients->id) . '"><i class="bx bx-comment-check"></i></a><a class="btn btn-sm btn-success text-white " title="Reminder" style="cursor:pointer"href="' . route('admin.crm.client.reminder', $Clients->id) . '"><i class="bx bx-stopwatch"></i> </a></div>';
                    })
                    ->rawColumns(['addedBy','action'])
                    ->make(true);
            }
            return view('admin.employee.Leeds.leed-show',compact('employee'));
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
                Client::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Client Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }
}
