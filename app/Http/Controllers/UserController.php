<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Role;
use App\Models\User;
use Exception;
use Yajra\DataTables\DataTables;


class UserController extends Controller
{
    public function index()
    {
        return view('users.index');
    }

    public function datatable(Request $request)
    {
        if ($request->ajax()) {
            $data = User::select('users.*', 'roles.name as role_name')
                        ->leftJoin('roles', 'users.role', '=', 'roles.id')
                        ->latest()
                        ->get();
                        
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($user) {
                        $editUrl = route('users.edit', $user->id);
                        $deleteUrl = route('users.destroy', $user->id);
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
        $roles = Role::all();
        return view('users.create',compact('roles'));
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name'          => 'required|max:255',
                'email'         => 'required|unique:users,email,NULL,id,deleted_at,NULL|email',
                'password'      => 'required|min:8|regex:/^(?=.*[A-Z])(?=.*[!@#$%^&*])(?=.*[0-9])(?=.*[a-z]).{8,}$/',
                'role'          => 'required',
            ]);
            
            $RoleData = Role::where('name',$validatedData['role'])->first();
            $validatedData['role'] = $RoleData['id'];
            $user = User::create($validatedData);
            $user->assignRole($RoleData['name']);

            return response()->json(['success' => 'User created successfully.', 'user' => $user], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user','roles'));
    }

    public function update(Request $request, User $user)
    {
        try {
            $validatedData = $request->validate([
                'name'          => 'required|max:255',
                'email'         => ['required','max:255',Rule::unique('users')->ignore($user->id)->whereNull('deleted_at')],
                'password'      => 'required|min:8|regex:/^(?=.*[A-Z])(?=.*[!@#$%^&*])(?=.*[0-9])(?=.*[a-z]).{8,}$/',
                'role'          => 'required',
            ]);

            $RoleData = Role::where('name',$validatedData['role'])->first();
            $validatedData['role'] = $RoleData['id'];
            $user->update($validatedData);
            $user->assignRole($RoleData['name']);

            return response()->json(['success' => 'User updated successfully.', 'user' => $user], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();
            return redirect()->route('users.index')->with('success', 'User deleted successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
