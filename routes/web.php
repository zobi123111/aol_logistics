<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserActivityLogController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SupplierUserController;
use App\Http\Controllers\SupplierUnitController;
use App\Http\Controllers\ServiceController;


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


Route::middleware(['auth.user', 'otp.verified', 'role.permission'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [UserController::class, 'users'])->name('users.index');
    Route::post('/save_user', [UserController::class, 'save_user'])->name('save_user.index');
    Route::post('/users/edit', [UserController::class, 'getUserById'])->name('user.get');
    Route::post('/users/edit/save', [UserController::class, 'saveUserById'])->name('user.update');;
    Route::post('/users/delete', [UserController::class, 'destroy'])->name('user.destroy');
    Route::post('/users/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');

    Route::resource('roles', RolePermissionController::class);
    Route::get('/activity-logs', [UserActivityLogController::class, 'showAll'])->name('activityLogs.all');
    Route::delete('/logs/delete', [UserActivityLogController::class, 'deleteLogs'])->name('logs.delete');
    Route::post('/users/bulk-action', [UserController::class, 'bulkAction'])->name('users.bulkAction');
    Route::resource('suppliers', SupplierController::class);
    Route::post('/suppliers/toggle-status', [SupplierController::class, 'toggleStatus'])->name('suppliers.toggleStatus');
    // Route::prefix('suppliers/{supplierId}/users')->group(function () {
    //     Route::get('/', [SupplierUserController::class, 'index'])->name('supplier_users.index');
    //     Route::get('/create', [SupplierUserController::class, 'create'])->name('supplier_users.create');
    //     Route::post('/', [SupplierUserController::class, 'store'])->name('supplier_users.store');
    //     Route::get('/{userId}/edit', [SupplierUserController::class, 'edit'])->name('supplier_users.edit');
    //     Route::put('/{userId}', [SupplierUserController::class, 'update'])->name('supplier_users.update');
    //     Route::delete('/{userId}', [SupplierUserController::class, 'destroy'])->name('supplier_users.destroy');
    // });

    // Routes for client page
    Route::resource('client', ClientController::class);
});

Route::middleware(['auth.user', 'otp.verified', 'check.supplier'])->group(function () {
    Route::prefix('suppliers/{supplierId}/users')->group(function () {
        Route::get('/', [SupplierUserController::class, 'index'])->name('supplier_users.index');
        Route::get('/create', [SupplierUserController::class, 'create'])->name('supplier_users.create');
        Route::post('/', [SupplierUserController::class, 'store'])->name('supplier_users.store');
        Route::get('/{userId}/edit', [SupplierUserController::class, 'edit'])->name('supplier_users.edit');
        Route::put('/{userId}', [SupplierUserController::class, 'update'])->name('supplier_users.update');
        Route::delete('/{userId}', [SupplierUserController::class, 'destroy'])->name('supplier_users.destroy');
    });

    Route::prefix('suppliers/{supplierId}/units')->group(function () {
        Route::get('/', [SupplierUnitController::class, 'index'])->name('supplier_units.index');
        Route::get('/create', [SupplierUnitController::class, 'create'])->name('supplier_units.create');
        Route::post('/', [SupplierUnitController::class, 'store'])->name('supplier_units.store');
        Route::get('/{unitId}/edit', [SupplierUnitController::class, 'edit'])->name('supplier_units.edit');
        Route::put('/{unitId}', [SupplierUnitController::class, 'update'])->name('supplier_units.update');
        Route::delete('/{unitId}', [SupplierUnitController::class, 'destroy'])->name('supplier_units.destroy');
        Route::post('/restore/{unitId}', [SupplierUnitController::class, 'restore'])->name('supplier_units.restore');
    });

    Route::prefix('suppliers/{supplierId}/services')->group(function () {
        Route::get('/', [ServiceController::class, 'index'])->name('services.index');
        Route::get('/create', [ServiceController::class, 'create'])->name('services.create');
        Route::post('/', [ServiceController::class, 'store'])->name('services.store');
        Route::get('/{serviceId}/edit', [ServiceController::class, 'edit'])->name('services.edit');
        Route::put('/{serviceId}', [ServiceController::class, 'update'])->name('services.update');
        Route::delete('/{serviceId}', [ServiceController::class, 'destroy'])->name('services.destroy');
        Route::post('/restore/{serviceId}', [ServiceController::class, 'restore'])->name('services.restore');
    });
});
