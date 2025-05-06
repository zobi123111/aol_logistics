<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect('/dashboard');
        }
        return view('Login/index');
    }

    public function login(Request $request)
    {
        if (Auth::check()) {
            return redirect('/dashboard');
        }
        if ($request->isMethod('get')) {
            $user = Auth::user();
            if ($user && $user->otp_verified) {               
                return redirect()->route('dashboard');
            } 
            return view('Login.index');
        }
    
        if ($request->isMethod('post')) {

            // Validate email and password
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);
    
            // Check if 'remember me' is selected
            $remember = $request->has('remember'); 
            // Attempt login with credentials
            if (Auth::attempt($credentials, $remember)) {
                $user = Auth::user();
                // Check if session has expired
                // if ($user->last_activity && $user->last_activity < now()->subMinutes(30)) {
                //     $user->update(['session_id' => null]);
                // }

                $currentSession = session()->getId();
                // Check if the user is already logged in from another device
                // if ($user->session_id && $user->session_id !== $currentSession) {
                //     Auth::logout();
                //     return response()->json(['credentials_error' => 'You are already logged in from another device.']);
                // }
                // Check if user is active
                if (!$user->is_active) {
                    Auth::logout();
                    return response()->json(['credentials_error' => 'Your account is inactive']);
                }

                // Generate OTP
                $otp = rand(100000, 999999); 
                $otpExpiresAt = now()->addMinutes(5);
    
                // Store OTP and expiration in the user's record
                $otp = 123456;

                $user->otp = $otp;
                $user->otp_expires_at = $otpExpiresAt;
                $user->otp_verified = false; 
                $user->save();
                
                session(['otp_email' => $user->email]);
                session(['last_otp_sent_time' => time()]); // Store current time
    
                // Send OTP to user's email (make sure the mail system is configured)
                // Mail::send('emails.otp', ['otp' => $otp, 'user' => $user], function ($message) use ($user) {
                //     $message->to($user->email)
                //         ->subject('Your OTP for 2FA');
                // });
                
    
                return response()->json(['otp_required' => true]);
            } else {
                // Invalid credentials
                return response()->json(['credentials_error' => 'The provided credentials do not match our records.']);
            }
        }
    }

    public function verifyOtp(Request $request)
    {
        // Validate OTP
        $request->validate([
            'otp' => 'required|numeric|digits:6', 
        ]);

        // Retrieve the logged-in user
        $user = User::where('email', session('otp_email'))->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Retrieve OTP and expiration time from the database
        $storedOtp = $user->otp;
        $otpExpiresAt = $user->otp_expires_at;

        // Check if OTP has expired
        if ($otpExpiresAt && now()->greaterThan($otpExpiresAt)) {
            return response()->json(['error' => 'OTP has expired. Please request a new one.'], 422);
        }

        // Check if OTP matches the stored OTP
        if ($request->otp == $storedOtp) {

            $currentSession = session()->getId();
            // // Check if the user is already logged in from another device
            // if ($user->session_id && $user->session_id !== $currentSession) {
            //     Auth::logout();
            //     return response()->json(['error' => 'You are already logged in from another device.']);
            // }

            // Clear OTP and expiration from the database
            $user->otp_verified = true;
            $user->otp = null;
            $user->otp_expires_at = null;
            $user->session_id = $currentSession; 
            $user->last_login_at = Carbon::now(); 

            // $user->last_activity = now(); 
            $user->save();

            Auth::login($user);

            session()->forget('otp_email');

            // Return success response
            return response()->json([
                'success' => 'OTP verified successfully!',
                'redirect' => route('dashboard')  
            ]);
        } else {
            // Invalid OTP
            return response()->json(['error' => 'Invalid OTP'], 422);
        }
    }

    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);
    
        $user = User::where('email', $request->email)->first();
    
        if (!$user) {
            return response()->json(['error' => 'User not found!'], 404);
        }
    
        $otp = rand(100000, 999999); // Generate OTP
        $user->update(['otp' => $otp]); // Save OTP
    
        // Store the last OTP sent time in session
        session(['last_otp_sent_time' => time()]);
    
        return response()->json(['success' => 'OTP has been resent.', 'remaining_time' => 120]);
    }

    public function logOut()
    {
        $user = Auth::user(); // Get the current authenticated user

        if ($user) {
            $user->otp_verified = false;
            $user->session_id = null;
            // $user->last_activity = null;
            $user->save();
        }

        Session::flush();
        Auth::logout(); 
        session()->invalidate(); 
        session()->regenerateToken();
        
        // Pass 'logout' session variable
        return redirect('/')->with('logout', true);
    }


    public function forgotPasswordView()
    {

        return view('auth.forgot-password');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Retrieve the email and check if the user exists
        $email = $request->email;
        $recipient = User::where('email', $email)->first();

        if ($recipient) {
            // Generate a random token and store it in the database
            $token = Str::random(64);
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                ['token' => $token, 'created_at' => Carbon::now()]
            );

            // Send the reset password email
            $resetLink = url('/reset/password/' . $token . '?email=' . urlencode($email));

            Mail::send('emails.password_reset', ['resetLink' => $resetLink, 'user' => $recipient], function ($message) use ($email) {
                $message->to($email)
                    ->subject('Reset Your Password');
            });

            return redirect()->back()->with('message', 'Password reset link has been sent to your email.');
        } else {
            return response()->json([
                'message' => 'Email address not found.'
            ], 404);
        }
    }
    
    public function resetPassword($token)
    {

        $email = request()->query('email'); 
        return view('auth.reset-password', ['token' => $token, 'email' => $email]);
    }

    public function submitResetPasswordForm(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $updatePassword = DB::table('password_reset_tokens')
        ->where([
          'email' => $request->email,
          'token' => $request->token
        ])
        ->first();

        if(!$updatePassword){
            return back()->withInput()->with('error', 'Invalid token!');
        }
        $user = User::where('email', $request->email)
        ->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')->where(['email'=> $request->email])->delete();

        return redirect('/')->with('message', 'Your password has been changed!');

    }
   
    public function verifyotpform()
    {
        return view('Login/otpform');
    }
}