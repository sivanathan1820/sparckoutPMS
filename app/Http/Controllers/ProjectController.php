<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Project;
use App\Models\User;
use Exception;
use Yajra\DataTables\DataTables;

class ProjectController extends Controller
{
    public function index()
    {
        return view('projects.index');
    }

    public function datatable(Request $request)
    {
        if ($request->ajax()) {
            $data = Project::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($project) {
                    $editUrl = route('projects.edit', $project->id);
                    $deleteUrl = route('projects.destroy', $project->id);
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
        $teamMembers = User::getTeamMembers();
        return view('projects.create',compact('teamMembers'));
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name'          => 'required|unique:projects,name,NULL,id,deleted_at,NULL|max:255',
                'description'   => 'required',
                'start_date'    => 'required|date',
                'end_date'      => 'required|date|after_or_equal:start_date',
                'team_member'   => 'required|array|min:1',
            ]);
            $validatedData['team_member'] = implode(',', $validatedData['team_member']);
            Project::create($validatedData);
            return response()->json(['success' => 'Project created successfully.'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function edit(Project $project)
    {
        $teamMembers = User::getTeamMembers();
        return view('projects.edit', compact('project','teamMembers'));
    }

    public function update(Request $request, Project $project)
    {
        try {
            $validatedData = $request->validate([
                'name'          => ['required','max:255',Rule::unique('projects')->ignore($project->id)->whereNull('deleted_at')],
                'description'   => 'required',
                'start_date'    => 'required|date',
                'end_date'      => 'required|date|after_or_equal:start_date',
                'team_member'   => 'required|array|min:1',
            ]);
            $validatedData['team_member'] = implode(',', $validatedData['team_member']);
            $project->update($validatedData);
            return response()->json(['success' => 'Project updated successfully.'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Project $project)
    {
        try {
            $project->delete();
            return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
