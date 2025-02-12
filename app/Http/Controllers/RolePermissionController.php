<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Module;
use App\Models\Page;
use App\Models\RolePermission;
use App\Models\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\UserActivityLog;

class RolePermissionController extends Controller
{
   
    public function index()
    {
        $rolePermissions = RolePermission::with(['role.userType', 'module'])->get();
        $roles = Role::with(['userType'])->withCount('users') ->get();
        return view('role_permissions.index', compact('rolePermissions', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        $userType = UserType::all();
        $pages = Page::with(['modules'])->get(); 
        return view('role_permissions.create', compact('roles','userType', 'pages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_type' => 'required|exists:user_types,id',
            'role_name' => 'required|string',
            'module_ids' => 'required|array',  
            'module_ids.*' => 'exists:modules,id',
        ]);
    
        // Create a new RolePermission instance and save it
        try {

            $createRole = Role::create([
                'role_name' => $validated['role_name'],
                'user_type_id' => $validated['user_type']
            ]);

            // Attach the selected modules to the role_permission
            foreach ($validated['module_ids'] as $pageId => $moduleIds) {
                foreach ($moduleIds as $moduleId) {
                    // dd($moduleId);
                    $rolePermission = RolePermission::create([
                        'role_id' => $createRole->id,
                        'module_id' => $moduleId

                    ]);
                    // $rolePermission->modules()->attach($moduleId);
                }
            }

            // add log
            UserActivityLog::create([
                'log_type' => UserActivityLog::LOG_TYPE_CREATE_ROLE,
                'description' => 'A new role "' . $validated['role_name'] . '"  has been created by ' 
                        . auth()->user()->fname . ' ' 
                        . auth()->user()->lname 
                        . ' (' . auth()->user()->email . ')',
                    'user_id' => auth()->id(), 
            ]);

            // Success flash message
            return redirect()->to('/roles')->with('message', 'Role Created successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors('Something went wrong. Please try again.')
                ->withInput();
        }
    }
    
    public function edit($roleId)
    {
        $roleId = decode_id($roleId);
        try {
            $role = Role::with('userType', 'rolePermissions')->findOrFail($roleId);

            $currentModules = $role->rolePermissions->pluck('module_id')->toArray();

            // Get the list of pages and modules
            $pages = Page::with('modules')->get(); 
    
            return view('role_permissions.edit', compact('role', 'pages', 'currentModules'));
        } catch (\Exception $e) {
            // Handle error if role not found
            return redirect()->back()
                             ->withErrors('Role not found.')
                             ->withInput();
        }
    }

    public function update(Request $request, $roleId)
    {
        $en = $roleId;
        $roleId = decode_id($roleId);

        $validated = $request->validate([
            'role_name' => 'required|string',
            'module_ids' => 'required|array', 
            'module_ids.*' => 'exists:modules,id', 
        ]);
        
        // Find the role
        $role = Role::findOrFail($roleId);
    
        $role->update([
            'role_name' => $request->input('role_name'),
        ]);

        // Get the selected modules from the request
        $selectedModules = $request->input('module_ids');
        $role->rolePermissions()->delete();

            // Attach the selected modules to the role_permission
            foreach ($validated['module_ids'] as $pageId => $moduleIds) {
                foreach ($moduleIds as $moduleId) {
                    $rolePermission = RolePermission::create([
                        'role_id' => $role->id,
                        'module_id' => $moduleId

                    ]);
                }
            }
            // add log
            UserActivityLog::create([
                'log_type' => UserActivityLog::LOG_TYPE_EDIT_ROLE,
                'description' => 'A new role "' . $validated['role_name'] . '"  has been updated by ' 
                        . auth()->user()->fname . ' ' 
                        . auth()->user()->lname 
                        . ' (' . auth()->user()->email . ')',
                    'user_id' => auth()->id(), 
            ]);
        Session::flash('message', 'Role Updated successfully');
        return redirect()->route('roles.edit', $en);
    }

    public function destroy($roleId)
    {
        $en = $roleId;
        $roleId = decode_id($roleId);
        // Find the role and delete it along with its associated role permissions
        $role = Role::withCount('users')->findOrFail($roleId);

        if ($role->users_count > 0) {
            return redirect()->route('roles.index')->withErrors('You cannot delete this role because it is assigned to ' . $role->users_count . ' users.');
        }
        // add log
        UserActivityLog::create([
            'log_type' => UserActivityLog::LOG_TYPE_DELETE_ROLE,
            'description' => 'A new role "' . $role->role_name . '"  has been deleted by ' 
                    . auth()->user()->fname . ' ' 
                    . auth()->user()->lname 
                    . ' (' . auth()->user()->email . ')',
                'user_id' => auth()->id(), 
        ]);
        $role->rolePermissions()->delete();
        $role->delete();
        Session::flash('message', 'Role deleted successfully.');
        return redirect()->route('roles.index');
    }
}