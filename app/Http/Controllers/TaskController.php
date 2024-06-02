<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Project;
use App\Models\User;
use App\Models\Task;
use Exception;
use Yajra\DataTables\DataTables;
use Auth;
use Spatie\Permission\Models\Permission;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        return view('tasks.index', compact('tasks'));
    }

    public function datatable(Request $request)
    {
        if ($request->ajax()) 
        {
            $data = Task::select('tasks.task','tasks.id', 'projects.name as project_name', 'users.name as assigned_to')
            ->leftJoin('projects', 'tasks.project_id', '=', 'projects.id')
            ->leftJoin('users', 'tasks.assigned_to', '=', 'users.id')
            ->get();
            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function($task) {
                $editUrl = route('tasks.edit', $task->id);
                $deleteUrl = route('tasks.destroy', $task->id);
                $btn = '<a href="' . $editUrl . '" class="btn btn-primary">Edit</a>';
                $btn .= '<form action="' . $deleteUrl . '" method="POST" style="display:inline;">
                ' . csrf_field() . '
                ' . method_field('DELETE') . '
                <button type="submit" class="btn btn-danger">Delete</button>
                </form>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }
    }

    public function create()
    {
        $projects       = Project::all();
        $task_status    = config('constants.task_status');
        return view('tasks.create',compact('projects','task_status'));
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'project_id'    => 'required|integer',
                'task'          => 'required',
                'assigned_to'   => 'required|integer',
                'status'        => 'required|in:1,2,3,4'
            ]);
            Task::create($validatedData);
            return response()->json(['success' => 'Task created successfully.'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function edit(Task $task)
    {
        $projects           = Project::all();
        $assigned_members   = Project::getAssignedTeamMembers($task->project_id);
        $task_status        = config('constants.task_status');
        return view('tasks.edit', compact('task','projects','assigned_members','task_status'));
    }

    public function update(Request $request, Task $task)
    {
        try {
            $validatedData = $request->validate([
                'project_id'    => 'required|integer',
                'task'          => 'required',
                'assigned_to'   => 'required|integer',
                'status'        => 'required|in:1,2,3,4'
            ]);
            $task->update($validatedData);
            return response()->json(['success' => 'Task updated successfully.'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Task $task)
    {
        try {
            $task->delete();
            return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function getProjectTeamMembers(Request $request)
    {
        try {
            $data = Project::getAssignedTeamMembers($request->project_id);
            return response()->json(['success' => 'Data found.','data' => $data], 200);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function assignedTask(Request $request)
    {
        $task_status  = config('constants.task_status');
        return view('tasks.assignedtask',compact('task_status'));
    }

    public function assignedTaskList(Request $request)
    {
        if ($request->ajax()) 
        {
            $user = Auth::user()->id;
            $data = Task::select('tasks.task','tasks.id','tasks.status', 'projects.name as project_name', 'users.name as assigned_to')
            ->leftJoin('projects', 'tasks.project_id', '=', 'projects.id')
            ->leftJoin('users', 'tasks.assigned_to', '=', 'users.id')
            ->where('assigned_to',$user)
            ->get();
            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('status', function($task) 
            {
                $task_status = config('constants.task_status');
                $filterstatus = "";
                foreach ($task_status as $status) {
                    if ($status['key'] === $task->status) {
                        $filterstatus = $status['value'];
                        break;
                    }
                }
                return $filterstatus;
            })
            ->addColumn('action', function($task) {
                $btn        = '<a href="javascript:void(0)" onclick="update_status('.$task->id.')" class="btn btn-primary">Update Status</a>';
                return $btn;
            })
            ->rawColumns(['status','action'])
            ->make(true);
        }
    }

    public function updateStatus(Request $request)
    {
        try 
        {
            $validatedData = $request->validate([
                'id'          => 'required|integer',
                'status'      => 'required|in:1,2,3,4'
            ]);
            Task::where('id',$request->id)->update(array('status' => $request->status));
            return response()->json(['success' => 'Status updated successfully.'], 200);
        } 
        catch (Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
