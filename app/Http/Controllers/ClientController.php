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
use App\Models\ClientCost;


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
            ->addColumn('client_cost', function ($client) {
                return '<a href="'.route('client_services.index', encode_id($client->id)).'" class="btn btn-primary create-button btn_primary_color">
                            <i class="fa-solid fa-user"></i> '. __('messages.Manage').'
                        </a>';
            })
            ->rawColumns(['status', 'actions', 'client_users', 'client_cost']) 
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
            // 'email' => 'required|string|max:255',
            // 'password' => 'required|string|min:6|confirmed',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'mobile_number' => 'required|numeric',  
            'country_code' => 'required|string|max:5', 
            'dba' => 'required|string|max:255',
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
            // if (User::where('email', $request->email)->exists()) {
            //     return redirect()->back()->withInput()->withErrors(['email' => 'The email is already registered.']);
            // }
            // $role = Role::where('role_slug', config('constants.roles.CLIENTMASTERCLIENT'))->first();
        
            $createClient = User::create([
                // 'fname' => $request->client_Fname,
                // 'lname' => $request->client_Lname,
                // 'email' => $request->email,
                'business_name' => $request->business_name,
                'created_by' => auth()->id(),
                // 'password' => Hash::make($request->password),
                // 'role' => $role->id,
                // 'profile_photo' => $profilePhotoPath,
                'is_client' => 1,
                'mobile_number' => $request->mobile_number,
                'country_code' => $request->country_code,
                'dba' => $request->dba,
            ]);
        
            DB::commit();

             // add log
            UserActivityLog::create([
                'log_type' => UserActivityLog::LOG_TYPE_CREATE_CLIENT,
                'description' => 'A new client user'. ' (' .$request->business_name . ') has been created by ' 
                        . auth()->user()->fname . ' ' 
                        . auth()->user()->lname 
                        . ' (' . auth()->user()->business_name . ') ',
                'user_id' => auth()->id(), 
            ]);

            // $password = $request->password;

            // Send welcome email to client
        // if (isEmailTypeActive('client_account_created')) {

        //     queueEmailJob(
        //         recipients: [$createClient->email],
        //         subject: 'Welcome to ' . config('app.name'),
        //         template: 'emails.client_account_created',
        //         payload: [
        //             'business_name' => $createClient->business_name,
        //             'email' => $createClient->email,
        //             'password' => $password,
        //         ],
        //         emailType: 'client_account_created'
        //     );
        // }
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
         // Send email to the client before deletion
        if (isEmailTypeActive('client_deleted')) {

        queueEmailJob(
            recipients: [$client_data->email],
            subject: 'Your Client Account Has Been Deleted',
            template: 'emails.client_deleted',
            payload: [
                'email' => $client_data->email,
                'business_name' => $client_data->business_name,
            ],
            emailType: 'client_deleted'
        );
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
            // 'email' => 'required|email|unique:users,email,' . $user->id,
            'mobile_number' => 'required|numeric', 
            'country_code' => 'required|string|max:5',
            'dba' => 'required|string|max:255',
         ]);
     
         // Check if validation fails
         if ($validator->fails()) {
             return redirect()->back()
                 ->withErrors($validator)
                 ->withInput();
         }
     
        //  $user->fname = $request->client_Fname;
        //  $user->lname = $request->client_Lname; 
        //  $user->email = $request->email;
         $user->business_name = $request->business_name;
         $user->mobile_number = $request->mobile_number;
         $user->country_code = $request->country_code;
         $user->dba = $request->dba;

         $user->save();

          // add log
        UserActivityLog::create([
        'log_type' => UserActivityLog::LOG_TYPE_EDIT_CLIENT,
        'description' => 'A new client user'. ' (' .$request->business_name . ') has been updated by ' 
                . auth()->user()->fname . ' ' 
                . auth()->user()->lname 
                . ' (' . auth()->user()->business_name . ')',
            'user_id' => auth()->id(), 
        ]);
         return redirect()->route('client.index', ['supplierId' => $clientId])
             ->with('message', __('messages.Client updated successfully.'));
     }

     public function clientCost(Request $request, $clientId)
     {
         $de = decode_id($clientId); 
         $client = User::findOrFail($de);
         if ($request->ajax()) {
            $services =\App\Models\Service::with(['clientCosts' => function ($query) use ($de) {
                $query->where('client_id', '=', $de);  
            }, 'supplier'])->get();
            return DataTables::of($services)
                ->addColumn('supplier_name', function ($service) {
                    return $service->supplier ? $service->supplier->company_name : '---';
                })
                ->addColumn('service_name', function ($service) {
                    return $service->service_name ?? $service->service_name ;
                })
                ->addColumn('client_cost', function ($service) {
                    $clientCost = $service->clientCosts->first()?->client_cost ?? '';
                    return '<input type="number" step="0.01"
                                   name="costs[' . $service->id . ']"
                                    value="' . (is_null($clientCost) || $clientCost === "" ? '' : number_format((float) $clientCost, 2)) . '"
                                   class="form-control client-cost-input"
                                   data-service-id="' . $service->id . '">';
                })
                ->rawColumns(['client_cost'])
                ->make(true);
        }
    
        return view('client.client_cost', compact('clientId', 'client'));
     }

     public function save(Request $request)
{
    $clientId = $request->input('client_id');
    $costs = $request->input('costs', []); // [service_id => cost]
    $de = decode_id($clientId);
    foreach ($costs as $serviceId => $cost) {
        if ($cost === null || $cost === ''|| floatval($cost) == 0.00) continue;

        $service = \App\Models\Service::find($serviceId);
        if (!$service) continue;

        \App\Models\ClientCost::updateOrCreate(
            [
                'client_id' => $de,
                'service_id' => $serviceId,
            ],
            [
                'supplier_id' => $service->supplier_id,
                'client_cost' => $cost,
            ]
        );
    }
    return redirect()->back()->with('message', 'Cost updated successfully.');
}

     
}
