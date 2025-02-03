<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolePermissionController;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Login Route 
// Route::middleware(['guest'])->group(function () {
    Route::match(['get', 'post'], '/', [LoginController::class, 'index']);
    Route::match(['get', 'post'], '/login', [LoginController::class, 'login'])->name('login');
// });

Route::post('/verify-otp', [LoginController::class, 'verifyOtp'])->name('verifyotp');
Route::get('/otp-verify', [LoginController::class, 'verifyotpform'])->name('otp.verify');
Route::post('/resend-otp', [LoginController::class, 'resendOtp'])->name('resendotp');

Route::get('/logout', [LoginController::class, 'logOut']);
Route::get('/forgot-password', [LoginController::class, 'forgotPasswordView'])->name('forgot-password');
Route::post('/forgot-password', [LoginController::class, 'forgotPassword'])->name('forgot.password');
Route::get('/reset/password/{token}', [LoginController::class, 'resetPassword']);
Route::post('/reset/password', [LoginController::class, 'submitResetPasswordForm'])->name('submit.reset.password');


Route::middleware(['auth.user', 'otp.verified', 'role.permission', 'auto.logout'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [UserController::class, 'users'])->name('users.index');
    Route::post('/save_user', [UserController::class, 'save_user'])->name('save_user.index');
    Route::post('/users/edit', [UserController::class, 'getUserById'])->name('user.get');
    Route::post('/users/edit/save', [UserController::class, 'saveUserById'])->name('user.update');;
    Route::post('/users/delete', [UserController::class, 'destroy'])->name('user.destroy');
    Route::post('/users/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');

    //roles 
    Route::resource('roles', RolePermissionController::class);
});