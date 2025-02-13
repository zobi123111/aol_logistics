<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    public function index()
    {
        $clients = User::with('roledata')->where('is_client', 1)->get(); 
        return view('client.index', compact('clients'));
    }

    public function create()
    {
        return view('client.create');
    }

    public function store(Request $request)
    {
        $clientdata = [
            'client_Fname' => 'required|string|max:255',
            'client_Lname' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
        
        $profilePhotoPath = null;
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension(); 
            $profilePhotoPath = $file->storeAs('profile_photos', $filename, 'public'); 
        }

        $validator = Validator::make($request->all(), $clientdata);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        DB::beginTransaction();
        try {
            if (User::where('email', $request->email)->exists()) {
                return redirect()->back()->withInput()->withErrors(['email' => 'The email is already registered.']);
            }
            $role = Role::where('role_slug', $request->role)->first();
        
            $createClient = User::create([
                'fname' => $request->client_Fname,
                'lname' => $request->client_Lname,
                'email' => $request->email,
                'created_by' => auth()->id(),
                'password' => Hash::make($request->password),
                'role' => $role->id,
                'profile_photo' => $profilePhotoPath,
                'is_client' => 1
            ]);
        
            DB::commit();
            return redirect()->route('client.index')
            ->with('message', 'Client created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
            ->withInput()
            ->withErrors(['message' => 'Client creation failed! Please try again later.']);
                }
        return redirect()->route('client.index')->with('success', 'Client created successfully!');
    }

    public function destroy($clientId)
    {
        $de_clientId = decode_id($clientId);
        $client_data = User::find($de_clientId);
        if (!$client_data) {
            return redirect()->route('client.index')->with('error', 'Client not found.');
        }
        $client_data->delete();
        Session::flash('message', 'Client deleted successfully.');
        return redirect()->route('client.index')->with('success', 'Client deleted successfully.');
    }
}
