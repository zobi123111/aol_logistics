<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Role;
use Yajra\DataTables\DataTables;
use App\Models\UserActivityLog;


class SupplierUserController extends Controller
{
    // Display all users for a specific supplier
    public function index(Request $request, $supplier_id)
    {
        // $de_supplier_id = decode_id($supplier_id);
        // $supplier = Supplier::findOrFail($de_supplier_id);

        // // Retrieve all users belonging to this supplier
        // $users = User::with('roledata')->where('supplier_id', $de_supplier_id)->get();

        // return view('supplier_users.index', compact('supplier', 'users'));

        $de_supplier_id = decode_id($supplier_id);
        $supplier = Supplier::findOrFail($de_supplier_id);
        $users = User::with('roledata')->where('supplier_id', $de_supplier_id)->get();

        if ($request->ajax()) {
            return DataTables::of($users)
                ->addColumn('name', function ($user) use ($users) {
                    return $user->fname. ' '.$user->lname ; 
                })
                ->addColumn('role', function ($user) {
                    return $user->roledata ? $user->roledata->role_name : 'N/A';
                })
                ->addColumn('status', function ($user) {
                    return view('supplier_users.partials.toggle_status', compact('user'))->render();
                })
                ->addColumn('actions', function ($user) use ($supplier) {
                    return view('supplier_users.partials.actions', compact('user', 'supplier'))->render();
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }

        return view('supplier_users.index', compact('supplier', 'users'));
    }

    // Show form to create a user
    public function create($supplier_id)
    {
        $en = $supplier_id;
        $de_supplier_id = decode_id($supplier_id);
        $supplier = Supplier::findOrFail($de_supplier_id);
        return view('supplier_users.create', compact('supplier'));
    }

    // Store a new user for a supplier
    public function store(Request $request, $supplierId)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'user_role' => 'required|string',
            'password' => 'required|min:6|confirmed',
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

          $role = Role::where('role_slug', $request->user_role)->first();
          if (!$role) {
              return redirect()->route('suppliers.index')->with('success', __('messages.Supplier created successfully!'));
          }
    
        // Create the user and associate with supplier
        $user = new User();
        $user->fname = $request->firstname;
        $user->lname = $request->lastname;
        $user->email = $request->email;
        $user->role = $role->id;
        $user->password = Hash::make($request->password);
        $user->supplier_id = $supplierId;

        // Check if the supplier exists
        if (!$supplierId) {
            return redirect()->route('suppliers.index')->with('error', __('messages.Supplier not found.'));

}
        $supplier = Supplier::findOrFail($supplierId);
        if($request->user_role == 'master_client'){
            $user->is_supplier = 1;
        }
        $user->save();
       
        // add log
        UserActivityLog::create([
        'log_type' => UserActivityLog::LOG_TYPE_CREATE_SUPPLIER,
        'description' => 'A new supplier user'. ' (' .$request->email . ') has been created by ' 
                . auth()->user()->fname . ' ' 
                . auth()->user()->lname 
                . ' (' . auth()->user()->email . ') with role '.$role->role_name,
            'user_id' => auth()->id(), 
        ]);
        if (isEmailTypeActive('supplier_user_created')) {

        queueEmailJob(
            recipients: [$user->email, $supplier->user_email],
            subject: 'New User Created Under Your Company',
            template: 'emails.supplier_user_created',
            payload: [
                'email' => $user->email,
                'password' => $request->password,
                'company_name' => $supplier->company_name,
            ],
            emailType: 'supplier_user_created'
        );
    }
        
        return redirect()->route('supplier_users.index', ['supplierId' => encode_id($supplierId)])
            ->with('message', __('messages.User added successfully.'));
    }
    // Show form to edit a user
    public function edit($supplier_id, $user_id)
    {
        $de_supplier_id = decode_id($supplier_id);
        $de_user_id = decode_id($user_id);
    
        $supplier = Supplier::findOrFail($de_supplier_id);
        $user = User::with('roledata')->where('supplier_id', $de_supplier_id)->findOrFail($de_user_id);
    
        return view('supplier_users.edit', compact('supplier', 'user'));
    }
    

    // Update user information
    public function update(Request $request, $supplier_id, $user_id)
    {
        $de_supplier_id = decode_id($supplier_id);
        $de_user_id = decode_id($user_id);
    
        $user = User::where('supplier_id', $de_supplier_id)->findOrFail($de_user_id);
    
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'user_role' => 'required|string',
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
    
        $role = Role::where('role_slug', $request->user_role)->first();
        if (!$role) {
            return redirect()->route('suppliers.index')->with('success', __('messages.Supplier created successfully!'));
        }

        $user->fname = $request->firstname;
        $user->lname = $request->lastname;
        $user->email = $request->email;
        $user->role = $role->id;

        if($request->user_role == 'master_client'){
            $user->is_supplier = 1;
        }else{
            $user->is_supplier = 0;
        }
        $user->save();

       // add log
        UserActivityLog::create([
        'log_type' => UserActivityLog::LOG_TYPE_EDIT_SUPPLIER,
        'description' => 'A new supplier user'. ' (' .$request->email . ') has been updated by ' 
                . auth()->user()->fname . ' ' 
                . auth()->user()->lname 
                . ' (' . auth()->user()->email . ')',
            'user_id' => auth()->id(), 
        ]);
    
        return redirect()->route('supplier_users.index', ['supplierId' => $supplier_id])
            ->with('message', __('messages.User updated successfully.'));
    }
    
    // Delete a user
    public function destroy($supplier_id, $user_id)
    {
        $de_supplier_id = decode_id($supplier_id);
        $de_user_id = decode_id($user_id);
    
        $user = User::where('supplier_id', $de_supplier_id)->findOrFail($de_user_id);
        $supplier = Supplier::find($de_supplier_id);

        $user->delete(); // Soft delete
    
         // add log
         UserActivityLog::create([
            'log_type' => UserActivityLog::LOG_TYPE_DELETE_SUPPLIER,
            'description' => 'A new supplier user'. ' (' .$user->email . ') has been deleted by ' 
                    . auth()->user()->fname . ' ' 
                    . auth()->user()->lname 
                    . ' (' . auth()->user()->email . ')',
                'user_id' => auth()->id(), 
            ]);
            if (isEmailTypeActive('supplier_user_deleted')) {

        queueEmailJob(
            recipients: [$user->email, $supplier->user_email],
            subject: 'User Account Deleted - ' . config('app.name'),
            template: 'emails.supplier_user_deleted',
            payload: [
                'email' => $user->email,
                'company_name' => $supplier->company_name,
            ],
            emailType: 'supplier_user_deleted'
        );
    }
        return redirect()->route('supplier_users.index', ['supplierId' => $supplier_id])
        ->with('message', __('messages.User deteted successfully.'));
    }
}
