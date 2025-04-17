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
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rule;
use App\Models\EmailJob;


class UserController extends Controller
{
    public function users()
    {
        // $users = User::with('roledata')->where('id', '!=', auth()->id())->get();
        // $users = User::with('roledata')->whereNotNull('created_by')->get();
        $users = User::with('roledata')
        ->whereHas('roledata', function ($query) {
            $query->where('user_type_id', 1); 
        })
        ->get();
        $roles = Role::with(['userType'])->where('user_type_id', 1)->get();
        $userType = UserType::all();
        $allclient = User::where('is_client', 1)->get();

        return view('User.allusers', compact('users', 'roles', 'userType', 'allclient'));
    }

    public function save_user(Request $request)
    {

        App::setLocale(session('locale', 'en'));

        $validated = $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            'role_name' => 'required',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->whereNull('deleted_at'), 
            ],
            'password' => 'required|min:6|confirmed',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ],[
            'email.unique' => __('validation.unique.string'),
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
            // Mail::to($store->email)->send(new UserCreated($store, $password));
        
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

            // Queue welcome email
            queueEmailJob(
                recipients: [$validated['email']],
                subject: 'Welcome to ' . config('app.name'),
                template: 'emails.user_created',
                payload: [
                    'firstname' => $validated['firstname'],
                    'lastname' => $validated['lastname'],
                    'email' => $validated['email'],
                    'password' => $validated['password']
                ],
                emailType: 'user_created'
            );
            Session::flash('message', __('messages.user saved successfully'));
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
        ]);

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
        Session::flash('message', __('messages.User updated successfully'));
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

            // Queue user deletion email
            queueEmailJob(
                recipients: [$user->email],
                subject: 'Your Account Has Been Deleted',
                template: 'emails.user_deleted', 
                payload: [
                    'name' => $user->fname . ' ' . $user->lname,
                    'email' => $user->email,
                    'deleted_by' => auth()->user()->fname . ' ' . auth()->user()->lname
                ],
                emailType: 'user_deleted'
            );
            $user->delete();
            
            return redirect()->route('users.index')->with('message', __('messages.User deleted successfully'));
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

        // Get recipient emails
        $recipients = [$user->email];

        // If user has master client, add their email
        if ($user->client_id) {
            $masterClientEmail = User::where('id', $user->client_id)->value('email');
            if ($masterClientEmail) {
                $recipients[] = $masterClientEmail;
            }
        }
         // Insert email job into `email_jobs` table
            queueEmailJob(
                $recipients,
                'Your Account Status Changed',
                'emails.user-status-changed', 
                [
                    'name' => $user->fname . ' ' . $user->lname,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                ],
                'status_update'
            );
        return response()->json([
            'success' => true,
            'message' => __('messages.User status updated successfully'),
            'is_active' => $user->is_active
        ]);
    }

    public function bulkAction(Request $request)
    {

        $action = $request->input('bulk_action');
        $selectedUsers = $request->input('selected_users'); 
        $status = $request->input('status');
       

        // Ensure at least one user is selected
        if (empty($selectedUsers)) {
            return back()->with('error', 'Please select at least one user.');
        }

        // Perform the action based on selected option
        if ($action == 'change_status') {
            if (empty($status)) {
                return back()->with('error', 'Please select a status to change.');
            }

            // Update the status of selected users
            if ($status == 'active') {
                User::whereIn('id', $selectedUsers)->update(['is_active' => 1]);
            } elseif ($status == 'deactivated') {
   
                User::whereIn('id', $selectedUsers)->update(['is_active' => 0]);

            } else {
    
                return back()->with('error', 'Invalid status selected.');
            }

            return back()->with('success', 'Status updated successfully!');
        } elseif ($action == 'delete') {
            // Delete selected users
            User::whereIn('id', $selectedUsers)->delete();

            return back()->with('success', 'Users deleted successfully!');
        } else {

            return back()->with('error', 'Invalid action selected.');
        }
    }

    public function updateClientBusiness(Request $request)
    {
        // dd($request->user_id);

        // Validate the request
        $request->validate([
            'user_id' => 'required',
            'client_id' => 'required',
        ]);

        // try {
            // dd(decode_id($request->user_id));
            
            $userId = decode_id($request->user_id);
            // Get the user and update their client_id and role
            $user = User::findOrFail($userId);
            $role = Role::where('role_slug', config('constants.roles.CLIENT_SERVICE_EXECUTIVE'))->first();
            $user->client_id = $request->client_id;
            $user->role = $role->id;  
            $user->save();

            return redirect()->back()->with('message', __('messages.user_updated_successfully'));
        // } catch (\Exception $e) {
        //     return redirect()->back()->with('error', 'Failed to update user: ' . $e->getMessage());
        // }
    }
}