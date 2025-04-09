<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use Illuminate\Support\Facades\Session;
use App\Models\UserActivityLog;


class ClientUserController extends Controller
{
    public function index($id)
    {
        $de_id = decode_id($id);
        $clients = User::with('roledata')->where('client_id', $de_id)->get(); 
        return view('client_users.index', compact('clients', 'id'));
    }

    public function create($id)
    {
        $client_id = decode_id($id);
        return view('client_users.create', compact('client_id'));
    }

    public function store(Request $request, $id)
    {
        $clientdata = [
            'client_Fname' => 'required|string|max:255',
            'client_Lname' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ];
        
      

        $validator = Validator::make($request->all(), $clientdata);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            if (User::where('email', $request->email)->exists()) {
                return redirect()->back()->withInput()->withErrors(['email' => __('messages.The email is already registered.')]);
            }
            $role = Role::where('role_slug', config('constants.roles.CLIENT_SERVICE_EXECUTIVE'))->first();
        
            $createClient = User::create([
                'fname' => $request->client_Fname,
                'lname' => $request->client_Lname,
                'email' => $request->email,
                'created_by' => auth()->id(),
                'password' => Hash::make($request->password),
                'role' => $role->id,
                'client_id' => $id
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

            $mainClientEmail = User::where('id', $id)->value('email');
            $mainClientBusiness = User::where('id', $id)->value('business_name');
            
           // Send email to both the new client user and the main client
            queueEmailJob(
                recipients: [$createClient->email, $mainClientEmail],
                subject: 'New Client User Created',
                template: 'emails.client_user_created',
                payload: [
                    'fname' => $createClient->fname,
                    'email' => $createClient->email,
                    'business_name' => $mainClientBusiness,
                    'password' => $request->password, // original password
                ],
                emailType: 'client_user_created'
            );
            return redirect()->route('client_users.index', encode_id($id))
            ->with('message',  __('messages.Client created successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
            ->withInput()
            ->withErrors(['message' => __('messages.Client creation failed! Please try again later.')]);
                }
        return redirect()->route('client_users.index', encode_id($id))->with('success', 'Client created successfully!');
    }

    public function destroy($clientId, $master_client)
    {
        
        $de_clientId = decode_id($clientId);
        $client_data = User::find($de_clientId);
        if (!$client_data) {
            return redirect()->route('client.index')->with('error', 'Client not found.');
        }

        $deletedEmail = $client_data->email;
        $deletedName = $client_data->fname . ' ' . $client_data->lname;

          // Get master client email and business name
        $masterClientData = User::find(decode_id($master_client));
        $masterEmail = $masterClientData?->email;
        $businessName = $masterClientData?->business_name;

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

            // Send email
            queueEmailJob(
                recipients: [$deletedEmail, $masterEmail],
                subject: 'Client User Deleted - ' . config('app.name'),
                template: 'emails.client_user_deleted',
                payload: [
                    'deleted_name' => $deletedName,
                    'deleted_email' => $deletedEmail,
                    'business_name' => $businessName,
                ],
                emailType: 'client_user_deleted'
            );


        Session::flash('message', __('messages.Client deleted successfully.'));
        return redirect()->route('client_users.index',$master_client )->with('success', 'Client deleted successfully.');
    }

    public function edit($id, $master_client)
    {
        $en = $id;
        $de_id = decode_id($id);
        $en = $master_client;
        $master_client = decode_id($master_client);
        $client = User::findOrFail($de_id);
        return view('client_users.edit', compact('client', 'master_client')); 
    }

     // Update user information
     public function update(Request $request, $clientId, $master_id)
     {

        $de_clientId = decode_id($clientId);
         $user = User::findOrFail($de_clientId);
     
        
         $validator = Validator::make($request->all(), [
            'client_Fname' => 'required|string|max:255',
            'client_Lname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
         ]);
       
         // Check if validation fails
         if ($validator->fails()) {
             return redirect()->back()
                 ->withErrors($validator)
                 ->withInput();
         }
     
 
         $user->fname = $request->client_Fname;
         $user->lname = $request->client_Lname;
         $user->email = $request->email;
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
         return redirect()->route('client_users.index',  $master_id)
             ->with('message', __('messages.Client updated successfully.'));
     }

}
