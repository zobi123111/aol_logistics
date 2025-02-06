<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\UserType;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCreated;
use App\Models\UserActivityLog;

class UserController extends Controller
{
    public function users()
    {
        // $users = User::with('roledata')->where('id', '!=', auth()->id())->get();
        $users = User::with('roledata')->get();

        $roles = Role::with(['userType'])->get();
        $userType = UserType::all();
        return view('User.allusers', compact('users', 'roles', 'userType'));
    }

    public function save_user(Request $request)
    {
        $validated = $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            'role_name' => 'required',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Handle Profile Photo Upload
        $profilePhotoPath = null;
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension(); // Unique name
            $profilePhotoPath = $file->storeAs('profile_photos', $filename, 'public'); // Save file
        }

        $store_user = array(
            "fname" => $request->firstname,
            "lname"  => $request->lastname,
            "email"   => $request->email,
            "password"=> Hash::make($request->password),
            'role'    => decode_id($request->role_name),
            'created_by' => auth()->id(),
            'profile_photo' => $profilePhotoPath
         );

      

       $store =  User::create($store_user);
        if($store){

            // Generate password to send in the email
            $password = $request->password;

            // Send email
            Mail::to($store->email)->send(new UserCreated($store, $password));
        
            // add log
            UserActivityLog::create([
                'log_type' => UserActivityLog::LOG_TYPE_CREATE_USER,
                'description' => 'A new user has been created by ' 
                        . auth()->user()->fname . ' ' 
                        . auth()->user()->lname 
                        . ' (' . auth()->user()->email . ') for user: ' 
                        . $request->firstname . ' ' 
                        . $request->lastname 
                        . ' (' . $request->email . ')',
                    'user_id' => auth()->id(), 
            ]);
            Session::flash('message', 'User saved successfully');
            return response()->json(['success' => 'User saved successfully']); 
        }
    }

    public function getUserById(Request $request) 
    {
        $user = User::with('roledata')->find(decode_id($request->id));
        if (!$user) {
            return response()->json(['error' => 'User not found']);
        }
        return response()->json(['user' => $user, 'selected_role' => encode_id($user->role), 'selected_user_type' => encode_id($user->roledata->user_type_id)]);
    }


    public function saveUserById(Request $request)
    {
        $validated = $request->validate([
            'edit_id' => 'required',
            'fname' => 'required',
            'lname' => 'required',
            'edit_role_name' => 'required',
            'edit_profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'edit_password' => 'nullable|min:6|confirmed'
        ], [
            'fname.required' => 'The first name field is required.',
            'lname.required' => 'The last name field is required.',
            'edit_role_name.required' => 'The role field is required.',
            'edit_password.confirmed' => 'The password confirmation does not match.'
        ]

        );

        // Find the user by ID
        $user = User::find(decode_id($request->edit_id));

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
         // Check if profile photo should be removed
        if ($request->remove_profile_photo == "1") {
            if ($user->profile_photo && file_exists(storage_path('app/public/' . $user->profile_photo))) {
                unlink(storage_path('app/public/' . $user->profile_photo));
            }
            $user->profile_photo = null;
        }
    
        // Handle Profile Photo Upload
        if ($request->hasFile('edit_profile_photo')) {
            // Delete the old profile photo if exists
            if ($user->profile_photo && file_exists(storage_path('app/public/' . $user->profile_photo))) {
                unlink(storage_path('app/public/' . $user->profile_photo));
            }

            // Upload new profile photo
            $file = $request->file('edit_profile_photo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension(); 
            $filePath = $file->storeAs('profile_photos', $filename, 'public'); 

            $user->profile_photo = $filePath;
        }

        // Update password if provided and user is Super Admin
        if (isAdminUser() && $request->filled('edit_password')) {
            $user->password = Hash::make($request->edit_password);
        }

        // Update the user's details
        $user->fname = $request->fname;
        $user->lname = $request->lname;
        $user->role = decode_id($request->edit_role_name);
        $user->save();

        // Log the update action
        UserActivityLog::create([
            'log_type' => UserActivityLog::LOG_TYPE_EDIT_USER,
            'description' => 'User ' . $user->fname . ' ' . $user->lname . ' (' . $user->email . ') was updated by ' 
                            . auth()->user()->fname . ' ' 
                            . auth()->user()->lname 
                            . ' (' . auth()->user()->email . ')',
            'user_id' => auth()->id(), 
        ]);

        // Flash message and response
        Session::flash('message', 'User updated successfully');
        return response()->json(['success' => 'User updated successfully']);
    }

    public function destroy(Request $request)
    { 
        $user = User::find(decode_id($request->id));
        if ($user) {

            // Log the delete action before actually deleting the user
            UserActivityLog::create([
                'log_type' => UserActivityLog::LOG_TYPE_DELETE_USER,
                'description' => 'User ' . $user->fname . ' ' . $user->lname . ' (' . $user->email . ') was deleted by ' 
                                . auth()->user()->fname . ' ' 
                                . auth()->user()->lname 
                                . ' (' . auth()->user()->email . ')',
                'user_id' => auth()->id(), 
            ]);
            $user->delete();
            return redirect()->route('users.index')->with('message', 'User deleted successfully');
        }
    }

    public function toggleStatus(Request $request)
    {
        $userId = decode_id($request->user_id);
        $isActive = $request->is_active;
        $user = User::findOrFail($userId);

        // Store the old status before updating
        $oldStatus = $user->is_active ? 'Active' : 'Inactive';
        $newStatus = $isActive ? 'Active' : 'Inactive';

        // Update the user's status
        $user->is_active = $isActive;
        $user->save();

        // Log the status update
        UserActivityLog::create([
            'log_type' => UserActivityLog::LOG_TYPE_UPDATE_STATUS,
            'description' => 'User ' . $user->fname . ' ' . $user->lname . ' (' . $user->email . ') status changed from ' 
                            . $oldStatus . ' to ' . $newStatus . ' by ' 
                            . auth()->user()->fname . ' ' 
                            . auth()->user()->lname 
                            . ' (' . auth()->user()->email . ')',
            'user_id' => auth()->id(),
        ]);
        return response()->json([
            'success' => true,
            'message' => 'User status updated successfully',
            'is_active' => $user->is_active
        ]);
    }
}