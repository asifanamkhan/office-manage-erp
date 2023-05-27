<?php

namespace App\Http\Controllers\Admin\Project;

use App\Http\Controllers\Controller;
use App\Models\CRM\Client\Client;
use App\Models\Employee\Employee;
use App\Models\Project\ProjectCategory;
use App\Models\Project\ProjectDuration;
use App\Models\Project\Projects;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
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
                $projects = Projects::with('projectCategory')
                            ->where('type',1)
                            ->latest()
                            ->get();
                return DataTables::of($projects)
                    ->addIndexColumn()
                    ->addColumn('project_category', function ($projects) {
                        if ($projects->projectCategory) {
                            return $projects->projectCategory->name;
                        } else {
                            return '';
                        }

                    })
                    ->addColumn('project_title', function ($projects) {
                        return '<a class="text-primary" style="cursor:pointer;text-decoration: none;"
                                 href="' . route('admin.projects.show', $projects->id) . '"> ' .$projects->project_title. ' </a>';
                    })
                    ->addColumn('status', function ($projects) {
                        if ($projects->status == 1) {
                            $status = '<span class="badge bg-info">Up Coming</span>';
                        } else if ($projects->status == 2) {
                            $status = '<span class="badge bg-primary">On Going</span>';

                        } else if ($projects->status == 3) {
                            $status = '<span class="badge bg-success" >Complete</span>';

                        } else if ($projects->status == 4) {
                            $status = '<span class="badge bg-danger">Cancel</span>';
                        }
                        else if ($projects->status == 5) {
                            $status = '<span class="badge bg-warning">On Hold</span>';
                        }
                        return $status;
                    })
                    ->addColumn('action', function ($projects) {
                        if($projects->status  == 5){
                            return '<div class="btn-group" role="group"    aria-label="Basic mixed styles example">
                                  <a href="' . route('admin.projects.show', $projects->id) . '" class="btn btn-sm btn-primary text-white" style="cursor:pointer" title="Show"><i class="bx bx-show"></i></a>
                                  <a type="button" href="'.route("admin.project.unhold",$projects->id).'" class="btn btn-sm btn-success text-white " style="cursor:pointer"  title="Un-Hold"><i class="bx bxs-hourglass-top text-primary"></i></a>
                                  <a  onclick="getSelectedLeaveData(' . $projects->id . ')" data-coreui-toggle="modal" data-coreui-target="#exampleModal"  class="btn btn-sm btn-success text-white mdlBtn" style="cursor:pointer"  title="Hold-List"><i class="bx bx-list-ul text-primary"></i></a>
                                  <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $projects->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                                </div>';
                        }
                        else {
                            return '<div class="btn-group" role="group"    aria-label="Basic mixed styles example">
                                    <a href="' . route('admin.projects.show', $projects->id) . '" class="btn btn-sm btn-primary text-white" style="cursor:pointer" title="Show"><i class="bx bx-show"></i></a>
                                    <a type="button" href="'.route("admin.project.hold",$projects->id).'" class="btn btn-sm btn-warning text-white " style="cursor:pointer"  title="Hold"><i class="bx bxs-hourglass-bottom text-primary"></i></a>
                                    <a  onclick="getSelectedLeaveData(' . $projects->id . ')" data-coreui-toggle="modal" data-coreui-target="#exampleModal"  class="btn btn-sm btn-success text-white mdlBtn" style="cursor:pointer"  title="Hold-List"><i class="bx bx-list-ul text-primary"></i></a>
                                    <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $projects->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                                    </div>';
                            }
                    })
                    ->rawColumns(['project_title','project_category','status','action'])
                    ->make(true);
            }
            return view('admin.project.project.index');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function gridView()
    {
        try {
            return view('admin.project.project.grid-view');
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
            $projects = Projects::count()+1;
            return view('admin.project.project.create',compact('projects'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
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
        $request->validate([
            'project_code' =>'required|string',
            'project_title' =>'required|string',
            'project_category' =>'required',
            'project_type' =>'required',
            'project_priority' =>'required',
            // 'reporting_person' =>'required',
            'status' =>'required',
        ]);

        try {
            $data = new Projects();
            $data->project_code = $request->project_code;
            $data->project_title = $request->project_title;
            $data->client_id = json_encode($request->client);
            $data->project_category = $request->project_category;
            $data->project_type = $request->project_type;
            $data->project_priority = $request->project_priority;
            $data->type = 1;  // project type == 1
            $data->description = $request->description;
            $data->status = $request->status;
            $data->created_by = Auth::user()->id;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $data->save();

            return redirect()->route('admin.projects.index')
                    ->with('message', 'Project Created Successfully');
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
        try{
            $projectCategory = ProjectCategory::get();
            $employees = Employee::get();
            $clients= Client::get();
            $project = Projects::findOrFail($id);
            $client_ids= json_decode($project->client_id);
            return view('admin.project.project.show.project-show',compact('project','projectCategory','clients','client_ids','employees'));
        }
        catch(\Exception $exception){
            return redirect()->back()->with('error', $exception->getMessage());
        }
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
            $projects = Projects::count()+1;
            return view('admin.project.project.edit',compact('projects'));
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
        $request->validate([
            'project_code' =>'required|string',
            'project_title' =>'required|string',
            'project_category' =>'required',
            'project_type' =>'required',
            'project_priority' =>'required',
            'status' =>'required',
        ]);

        try {
            $data = Projects::findOrFail($id);
            $data->project_code = $request->project_code;
            $data->project_title = $request->project_title;
            $data->project_type = $request->project_type;
            $data->client_id = json_encode($request->client);
            $data->project_category = $request->project_category;
            $data->project_priority = $request->project_priority;
            $data->type = 1;  // project type == 1
            $data->description = $request->description;
            $data->status = $request->status;
            $data->created_by = Auth::user()->id;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $data->update();
            return redirect()->route('admin.projects.show', $id)
                    ->with('message', 'Project Update Successfully');
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
                ProjectCategory::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Product Category Deleted Successfully.',
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
            $Category = ProjectCategory::findOrFail($id);
            // Check Item Current Status
            if ($Category->status == 1) {
                $Category->status = 0;
            } else {
                $Category->status = 1;
            }

            $Category->save();
            return response()->json([
                'success' => true,
                'message' => 'Product Category Status Update Successfully.',
            ]);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function projectCategorySearch(Request $request)
    {
        $result = ProjectCategory::query()
            ->where('name', 'LIKE', "%{$request->search}%")
            ->get(['name', 'id']);
        return $result;
    }
    public function employeeSearch(Request $request)
    {
        $result = Employee::query()
            ->whereIn('department', "$request->department_id")
            ->where('name', 'LIKE', "%{$request->search}%")
            ->get(['name', 'id']);
        return $result;
    }
    public function projectHold(Request $request, $id)
    {
        try {
            $projectDurations = ProjectDuration::where('duration_type_id',$id)
                               ->where('duration_type',1)
                                ->orWhere('project_id',$id)
                                ->get();

            foreach($projectDurations as $durations){
                $duration = ProjectDuration::findOrFail($durations->id);
                $duration->on_hold = 1;
                $duration->status= 5;
                $duration->project_id = $id;
                $duration->update();
            }

            $project= Projects::findOrFail($id);
            $project->status= 5;
            $project->update();

            $modules = Projects::where('parent_id',$id)->get();
            foreach($modules as $module){
                $duration = Projects::findOrFail($module->id);
                $module->status= 5;
                $module->update();
            }

            $projectDuration = $projectDurations->first();

            $data = new ProjectDuration();
            $data->duration_type_id =$id;
            $data->project_id = $id;
            $data->duration_type  = 1; // Project Type = 1
            $data->module_duration_id  = $projectDuration->id; // Duration Id
            $data->start_date = Carbon::now();
            $data->estimate_day = $projectDuration->estimate_day;
            $data->estimate_hour_per_day = $projectDuration->estimate_hour_per_day;
            $data->estimate_hour = $projectDuration->estimate_hour;
            $data->final_hour = $projectDuration->final_hour;
            $data->vacation_day = $projectDuration->vacation_day ;
            $data->final_day = $projectDuration->final_day;
            $data->adjustment_type = $projectDuration->adjustment_type;
            $data->adjustment_hour = $projectDuration->adjustment_hour;
            $data->status = 5;
            $data->on_hold = 1;
            $data->created_by = Auth::user()->id;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $data->save();
            return redirect()->route('admin.projects.index',)
                ->with('message', 'Project Hold Successfully');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function projectUnHold(Request $request, $id)
    {
        try {
            $projectDurations = ProjectDuration::where('duration_type_id',$id)
                                ->where('duration_type',1)
                                ->orWhere('project_id',$id)
                                ->get();

            foreach($projectDurations as $durations){
                $duration = ProjectDuration::findOrFail($durations->id);
                $duration->on_hold = 0;
                $duration->status= 2;
                if($duration->module_duration_id != null){
                    $duration->end_date = Carbon::now();
                    $duration->update();
                    // $duration->delete();
                }
                else{
                    $duration->update();
                }
            }

            $project= Projects::findOrFail($id);

            $project->status= 2;
            $project->update();

            $modules = Projects::where('parent_id',$id)->get();
            foreach($modules as $module){
                $duration = Projects::findOrFail($module->id);
                $module->status= 2;
                $module->update();
            }

            return redirect()->route('admin.projects.index')
                ->with('message', 'Project Status Change Successfully');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function holdList(Request $request,$id)
    {
        try {
                $data = ProjectDuration::where('duration_type_id',$id)
                        ->where('duration_type',1)
                        ->where('module_duration_id', $id)
                        ->get();
                $module = '';
                    if (count($data)> 0) {
                        $module = ProjectDuration::findOrFail($data->first()->duration_type_id);
                    }
                $html = view('admin.project.project.show.duration.module.hold-history', compact('data','module'))->render();

            return response()->json([
                'type' => $request->type,
                'data' => $html,
            ]);

        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
}
