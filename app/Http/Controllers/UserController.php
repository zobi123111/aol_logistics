<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCreated;

class UserController extends Controller
{
    public function users()
    {
        $users = User::with('roledata')->where('id', '!=', auth()->id())->get();
        $roles = Role::all();
        return view('User.allusers', compact('users', 'roles'));
    }

    public function save_user(Request $request)
    {
        $validated = $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            'role_name' => 'required',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:6|confirmed'
        ]);

        $store_user = array(
            "fname" => $request->firstname,
            "lname"  => $request->lastname,
            "email"   => $request->email,
            "password"=> Hash::make($request->password),
            'role'    => $request->role_name,
            'created_by' => auth()->id()
         );

       $store =  User::create($store_user);
        if($store){

            // Generate password to send in the email
            $password = $request->password;

            // Send email
            Mail::to($store->email)->send(new UserCreated($store, $password));
        
            Session::flash('message', 'User saved successfully');
            return response()->json(['success' => 'User saved successfully']); 
        }
    }

    public function getUserById(Request $request) 
    {
        $user = User::find($request->id);
        if (!$user) {
            return response()->json(['error' => 'User not found']);
        }
        return response()->json(['user' => $user]);
    }


    public function saveUserById(Request $request)
    {
        $validated = $request->validate([
            'edit_id' => 'required|exists:users,id',
            'fname' => 'required',
            'lname' => 'required',
            'edit_role_name' => 'required'
        ], [
            'fname.required' => 'The first name field is required.',
            'lname.required' => 'The last name field is required.',
            'edit_role_name.required' => 'The role field is required.',
        ]

        );

        // Find the user by ID
        $user = User::find($request->edit_id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Update the user's details
        $user->fname = $request->fname;
        $user->lname = $request->lname;
        $user->role = $request->edit_role_name;
        $user->save();

        // Flash message and response
        Session::flash('message', 'User updated successfully');
        return response()->json(['success' => 'User updated successfully']);
    }

    public function destroy(Request $request)
    { 
        $user = User::find($request->id);
        if ($user) {
            $user->delete();
            return redirect()->route('users.index')->with('message', 'User deleted successfully');
        }
    }

    public function toggleStatus(Request $request)
    {
        $userId = $request->user_id;
        $isActive = $request->is_active;
        $user = User::findOrFail($userId);

        // Update the user's status
        $user->is_active = $isActive;
        $user->save();
        return response()->json([
            'success' => true,
            'message' => 'User status updated successfully',
            'is_active' => $user->is_active
        ]);
    }
}