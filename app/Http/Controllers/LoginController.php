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
        return view('Login/index');
    }

    public function login(Request $request)
    {
        if ($request->isMethod('get')) {
            $userId = session('pending_user_id') ?? Auth::id();

            if ($userId) {
                // Redirect based on whether OTP is verified
                $user = User::find($userId);
                if ($user && $user->otp_verified) {
                    return redirect()->route('dashboard');
                } elseif (session('pending_user_id')) {
                    return redirect()->route('otp_screen'); // Redirect to OTP verification screen
                }
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
                $user = User::where('email', $credentials['email'])->first();
                // Generate OTP
                $otp = rand(100000, 999999); 
                $otpExpiresAt = now()->addMinutes(5);
    
                // Store OTP and expiration in the user's record
                $user->otp = $otp;
                $user->otp_expires_at = $otpExpiresAt;
                $user->otp_verified = false; // Reset OTP verification status
                $user->save();
    
                // Store user ID in session temporarily
                session(['pending_user_id' => $user->id]);
                
                // Send OTP to user's email (make sure the mail system is configured)
                Mail::send('emails.otp', ['otp' => $otp, 'user' => $user], function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Your OTP for 2FA');
                });
    
                return response()->json(['otp_required' => true]);
            } else {
                // Invalid credentials
                return response()->json(['credentials_error' => 'The provided credentials do not match our records.']);
            }
        }
    }

    public function verifyOtp(Request $request)
    {
        // Get the user from the session
        $userId = session('pending_user_id');
        if (!$userId) {
            return redirect()->route('login')->withErrors(['error' => 'Session expired. Please log in again.']);
        }

        // Validate OTP
        $request->validate([
            'otp' => 'required|numeric|digits:6', 
        ]);

        // Retrieve the logged-in user
        $user = Auth::user();

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
            Auth::login($user);

            // Clear OTP and expiration from the database
            $user->otp_verified = true;
            $user->otp = null;
            $user->otp_expires_at = null;
            $user->save();

            // Clear session
            session()->forget('pending_user_id');


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

    public function logOut()
    {
         Session::flush();
         Auth::logout();
         return Redirect('/');
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

            return response()->json([
                'message' => 'Password reset link sent to your email.'
            ], 200);
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