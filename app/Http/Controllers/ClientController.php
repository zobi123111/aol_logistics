<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use Illuminate\Support\Facades\Session;
use App\Models\UserActivityLog;
use Yajra\DataTables\DataTables;


class ClientController extends Controller
{
    public function index(Request $request )
    {
        if ($request->ajax()) {
            $clients = User::with('roledata')->where('is_client', 1); 
    
            return DataTables::of($clients)
            ->addColumn('role_name', function ($client) {
                return $client->roledata ? $client->roledata->role_name : '---'; // Concatenating first and last name
            })
            ->addColumn('status', function ($client) {
                return view('client.partials.toggle_status', compact('client'))->render();
            })
            ->addColumn('actions', function ($client) {
                return view('client.partials.actions', compact('client'))->render();
            })
            ->addColumn('client_users', function ($client) {
                return '<a href="'.route('client_users.index', encode_id($client->id)).'" class="btn btn-primary create-button btn_primary_color">
                            <i class="fa-solid fa-user"></i> '. __('messages.Manage').'
                        </a>';
            })
            ->rawColumns(['status', 'actions', 'client_users']) 
            ->make(true);
        }
    
        return view('client.index');
    }

    public function create()
    {
        return view('client.create');
    }

    public function store(Request $request)
    {
        $clientdata = [
            // 'client_Fname' => 'required|string|max:255',
            // 'client_Lname' => 'required|string|max:255',
            'business_name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
        
      

        $validator = Validator::make($request->all(), $clientdata);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // $profilePhotoPath = null;
        // if ($request->hasFile('profile_photo')) {
        //     $file = $request->file('profile_photo');
        //     $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension(); 
        //     $profilePhotoPath = $file->storeAs('profile_photos', $filename, 'public'); 
        // }

        DB::beginTransaction();
        try {
            if (User::where('email', $request->email)->exists()) {
                return redirect()->back()->withInput()->withErrors(['email' => 'The email is already registered.']);
            }
            $role = Role::where('role_slug', config('constants.roles.CLIENTMASTERCLIENT'))->first();
        
            $createClient = User::create([
                // 'fname' => $request->client_Fname,
                // 'lname' => $request->client_Lname,
                'email' => $request->email,
                'business_name' => $request->business_name,
                'created_by' => auth()->id(),
                'password' => Hash::make($request->password),
                'role' => $role->id,
                // 'profile_photo' => $profilePhotoPath,
                'is_client' => 1
            ]);
        
            DB::commit();

             // add log
            UserActivityLog::create([
                'log_type' => UserActivityLog::LOG_TYPE_CREATE_CLIENT,
                'description' => 'A new client user'. ' (' .$request->email . ') has been created by ' 
                        . auth()->user()->fname . ' ' 
                        . auth()->user()->lname 
                        . ' (' . auth()->user()->email . ') with role '.$role->role_name,
                'user_id' => auth()->id(), 
            ]);
            return redirect()->route('client.index')
            ->with('message', __('messages.Client created successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
            ->withInput()
            ->withErrors(['message' => 'Client creation failed! Please try again later.']);
        }
    }

    public function destroy($clientId)
    {
        $de_clientId = decode_id($clientId);
        $client_data = User::find($de_clientId);
        if (!$client_data) {
            return redirect()->route('client.index')->with('error', 'Client not found.');
        }
        $client_data->delete();

         // add log
         UserActivityLog::create([
            'log_type' => UserActivityLog::LOG_TYPE_DELETE_CLIENT,
            'description' => 'A new client user'. ' (' .$client_data->email . ') has been deleted by ' 
                    . auth()->user()->fname . ' ' 
                    . auth()->user()->lname 
                    . ' (' . auth()->user()->email . ')',
                'user_id' => auth()->id(), 
            ]);
        Session::flash('message', __('messages.Client deleted successfully.'));
        return redirect()->route('client.index')->with('success', 'Client deleted successfully.');
    }

    public function edit($id)
    {
        $en = $id;
        $de_id = decode_id($id);
        $client = User::findOrFail($de_id);
        // dd($supplier, $supplier->supplierdocuments);
        return view('client.edit', compact('client')); 
    }

     // Update user information
     public function update(Request $request, $clientId)
     {

        $de_clientId = decode_id($clientId);
         $user = User::findOrFail($de_clientId);

         $validator = Validator::make($request->all(), [
            // 'client_Fname' => 'required|string|max:255',
            // 'client_Lname' => 'required|string|max:255',
            'business_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
         ]);
     
         // Check if validation fails
         if ($validator->fails()) {
             return redirect()->back()
                 ->withErrors($validator)
                 ->withInput();
         }
     
        //  $user->fname = $request->client_Fname;
        //  $user->lname = $request->client_Lname; 
         $user->email = $request->email;
         $user->business_name = $request->business_name;

         $user->save();

          // add log
        UserActivityLog::create([
        'log_type' => UserActivityLog::LOG_TYPE_EDIT_CLIENT,
        'description' => 'A new client user'. ' (' .$request->email . ') has been updated by ' 
                . auth()->user()->fname . ' ' 
                . auth()->user()->lname 
                . ' (' . auth()->user()->email . ')',
            'user_id' => auth()->id(), 
        ]);
         return redirect()->route('client.index', ['supplierId' => $clientId])
             ->with('message', __('messages.Client updated successfully.'));
     }


     
}
