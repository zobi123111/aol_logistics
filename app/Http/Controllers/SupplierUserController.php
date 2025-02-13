<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Role;


class SupplierUserController extends Controller
{
    // Display all users for a specific supplier
    public function index($supplier_id)
    {
        $de_supplier_id = decode_id($supplier_id);
        $supplier = Supplier::findOrFail($de_supplier_id);

        // Retrieve all users belonging to this supplier
        $users = User::with('roledata')->where('supplier_id', $de_supplier_id)->get();

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
              return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
          }
    
        // Create the user and associate with supplier
        $user = new User();
        $user->fname = $request->firstname;
        $user->lname = $request->lastname;
        $user->email = $request->email;
        $user->role = $role->id;
        $user->password = Hash::make($request->password);
        $user->supplier_id = $supplierId;
        $user->is_supplier = true;
        $user->save();
    
        return redirect()->route('supplier_users.index', ['supplierId' => encode_id($supplierId)])
            ->with('message', 'User added successfully.');
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
            return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
        }

        $user->fname = $request->firstname;
        $user->lname = $request->lastname;
        $user->email = $request->email;
        $user->role = $role->id;
    
        $user->save();
    
    
        return redirect()->route('supplier_users.index', ['supplierId' => $supplier_id])
            ->with('message', 'User updated successfully.');
    }
    
    // Delete a user
    public function destroy($supplier_id, $user_id)
    {
        $de_supplier_id = decode_id($supplier_id);
        $de_user_id = decode_id($user_id);
    
        $user = User::where('supplier_id', $de_supplier_id)->findOrFail($de_user_id);
    
        $user->delete(); // Soft delete
    
        return redirect()->route('supplier_users.index', ['supplierId' => $supplier_id])
        ->with('message', 'User deteted successfully.');
    }
}
