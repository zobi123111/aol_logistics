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
use App\Http\Controllers\TrackTrailer;
use App\Http\Controllers\ClientUserController;
use App\Http\Controllers\LoadController;
use App\Http\Controllers\OriginController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\LangController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\QuickBooksController;
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;

use QuickBooksOnline\API\Facades\Invoice;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Customer;
use QuickBooksOnline\API\Core\OAuth\OAuth2AccessToken;
use QuickBooksOnline\API\Core\Http\IntuitLogger;
use QuickBooksOnline\API\Core\OAuth\OAuth2LoginHelper;
use QuickBooksOnline\API\Data\IPPAttachable;
use QuickBooksOnline\API\Data\IPPAttachableRef;
use QuickBooksOnline\API\Data\IPPReferenceType;
use QuickBooksOnline\API\Utility\Upload;
use QuickBooksOnline\API\PlatformService\PlatformService;
use App\Http\Controllers\EmailTypeController;
use App\Http\Controllers\WhatsAppWebhookController;
use App\Livewire\Chat;
use App\Http\Controllers\MasterServiceController;
use App\Http\Controllers\SupplierServiceController;
use App\Http\Controllers\ClientServiceController;
use App\Http\Controllers\TrailerdataController;


use Eludadev\Passage\Passage;
use Eludadev\Passage\Middleware\PassageAuthMiddleware;



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
    Route::get('lang/change', [LangController::class, 'change'])->name('changeLang');
    Route::get('/loads/filtered', [LoadController::class, 'getFilteredLoads'])->name('loads.filtered');

// });

Route::post('/verify-otp', [LoginController::class, 'verifyOtp'])->name('verifyotp');
Route::get('/otp-verify', [LoginController::class, 'verifyotpform'])->name('otp.verify');
Route::post('/resend-otp', [LoginController::class, 'resendOtp'])->name('resendotp');

Route::get('/logout', [LoginController::class, 'logOut']);
Route::get('/forgot-password', [LoginController::class, 'forgotPasswordView'])->name('forgot-password');
Route::post('/forgot-password', [LoginController::class, 'forgotPassword'])->name('forgot.password');
Route::get('/reset/password/{token}', [LoginController::class, 'resetPassword']);
Route::post('/reset/password', [LoginController::class, 'submitResetPasswordForm'])->name('submit.reset.password');

// Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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
    Route::get('/logs/data', [UserActivityLogController::class, 'getLogs'])->name('logs.data');

    Route::delete('/logs/delete', [UserActivityLogController::class, 'deleteLogs'])->name('logs.delete');
    Route::post('/users/bulk-action', [UserController::class, 'bulkAction'])->name('users.bulkAction');
    Route::resource('suppliers', SupplierController::class);
    Route::post('/suppliers/toggle-status', [SupplierController::class, 'toggleStatus'])->name('suppliers.toggleStatus');

    // Routes for client page
    Route::resource('client', ClientController::class);
    Route::get('/lastposition', [TrackTrailer::class, 'lastposition'])->name('lastposition.index');
    Route::resource('loads', LoadController::class);
    Route::get('/loads/{id}/assign', [LoadController::class, 'assignPage'])->name('loads.assign');
    Route::get('/loads/{load_id}/assign/{supplier_id}/{service_id}', [LoadController::class, 'assignSupplier'])
    ->name('loads.assign.supplier');
    Route::resource('origins', OriginController::class);
    Route::resource('destinations', DestinationController::class);
    Route::post('/assign-service', [LoadController::class, 'assign'])->name('assign.service');
    Route::delete('/unassign-service/{id}', [LoadController::class, 'unassignService'])->name('unassign.service');

    Route::put('/loads/{id}/change-status', [LoadController::class, 'changeStatus'])->name('loads.changeStatus');
    Route::get('/loads/{id}/edit-truck-details', [LoadController::class, 'editTruckDetails'])->name('loads.editTruckDetails');
    Route::post('/loads/{id}/update-truck-details', [LoadController::class, 'updateTruckDetails'])->name('loads.updateTruckDetails');
    Route::delete('/loads/document/{id}', [LoadController::class, 'deleteDocument'])->name('loads.deleteDocument');
    Route::get('/client-cost/{clientId}', [ClientController::class, 'clientCost'])->name('client_cost.index');
    Route::post('/client-cost/save', [ClientController::class, 'save'])->name('client_cost.save');
    Route::resource('master-services', MasterServiceController::class)->except(['show']);
    Route::get('master-services/data', [MasterServiceController::class, 'index'])->name('master-services.data');
    


});

Route::middleware(['auth.user', 'otp.verified'])->group(function () {
    Route::get('/email-types', [EmailTypeController::class, 'index'])->name('email-types.index');
    Route::post('/email-types/{id}/toggle', [EmailTypeController::class, 'toggle'])->name('email-types.toggle');
    // Route::get('/quickbooks/invoice', [QuickBooksController::class, 'createInvoice'])->name('quickbooks.invoice');
    // Route::post('/invoice/create-quickbooks/{id}', [QuickBooksController::class, 'createInvoice'])->name('invoice.quickbooks.create');
    // Route::post('/invoice/store', [QuickBooksController::class, 'storeInvoice'])->name('invoice.store');
    // Route::get('/invoice/client/{load_id}', [QuickBooksController::class, 'addClientInvoice'])
    //     ->name('invoice.client');
    // Route::get('/invoice/upload/{id}', [QuickBooksController::class, 'showUploadForm'])->name('invoice.upload');
    Route::get('/loads/{load_id}/quickbooks-invoices', [QuickBooksController::class, 'showQuickBooksInvoice'])->name('loads.quickbooks_invoices');
    Route::post('/update-client-business', [UserController::class, 'updateClientBusiness'])->name('update.client.business');
    Route::get('/upload-bill/{load_id}', [QuickBooksController::class, 'showUploadBillForm'])->name('upload.bill.form');
    Route::post('/upload-bill/supplier/{load_id}', [QuickBooksController::class, 'createSupplierBill'])
        ->name('upload.bill');
    Route::get('/invoice/supplier/{load_id}', [QuickBooksController::class, 'showQuickBooksBillByLoadId'])->name('invoice.supplier');
    Route::prefix('clients/{userId}/services')->group(function () {
        Route::get('/', [ClientServiceController::class, 'index'])->name('client_services.index');
        Route::get('/create', [ClientServiceController::class, 'create'])->name('client_services.create');
        Route::post('/', [ClientServiceController::class, 'store'])->name('client_services.store');
        Route::get('/{serviceId}/edit', [ClientServiceController::class, 'edit'])->name('client_services.edit');
        Route::put('/{serviceId}', [ClientServiceController::class, 'update'])->name('client_services.update');
        Route::delete('/{serviceId}', [ClientServiceController::class, 'destroy'])->name('client_services.destroy');
        Route::post('/restore/{serviceId}', [ClientServiceController::class, 'restore'])->name('client_services.restore');
    });
    Route::get('/supplier/{id}/trucks', [LoadController::class, 'getTrucksBySupplier']);

});

Route::middleware(['auth.user', 'otp.verified', 'check.suppliers'])->group(function () {
    Route::post('/update-client-business', [UserController::class, 'updateClientBusiness'])->name('update.client.business');
    Route::get('/upload-bill/{load_id}', [QuickBooksController::class, 'showUploadBillForm'])->name('upload.bill.form');
    Route::post('/upload-bill/supplier/{load_id}', [QuickBooksController::class, 'createSupplierBill'])
        ->name('upload.bill');
    // Route::get('/invoice/supplier/{load_id}', [QuickBooksController::class, 'showQuickBooksBillByLoadId'])->name('invoice.supplier');

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

    Route::prefix('suppliers/{supplierId}/services')->group(function () {
        Route::get('/', [SupplierServiceController::class, 'index'])->name('supplier_services.index');
        Route::get('/create', [SupplierServiceController::class, 'create'])->name('supplier_services.create');
        Route::post('/', [SupplierServiceController::class, 'store'])->name('supplier_services.store');
        Route::get('/{serviceId}/edit', [SupplierServiceController::class, 'edit'])->name('supplier_services.edit');
        Route::put('/{serviceId}', [SupplierServiceController::class, 'update'])->name('supplier_services.update');
        Route::delete('/{serviceId}', [SupplierServiceController::class, 'destroy'])->name('supplier_services.destroy');
        Route::post('/restore/{serviceId}', [SupplierServiceController::class, 'restore'])->name('supplier_services.restore');
    });

    Route::prefix('suppliers/{supplierId}/trailers')->name('supplier_trailers.')->group(function () {
        Route::get('/', [TrailerdataController::class, 'index'])->name('index'); 
        Route::get('/create', [TrailerdataController::class, 'create'])->name('create');
        Route::post('/', [TrailerdataController::class, 'store'])->name('store');
        Route::get('/{trailer}/edit', [TrailerdataController::class, 'edit'])->name('edit');
        Route::put('/{trailer}', [TrailerdataController::class, 'update'])->name('update');
        Route::delete('/{trailer}', [TrailerdataController::class, 'destroy'])->name('destroy');
    });
});
Route::middleware(['auth.user', 'otp.verified', 'check.client'])->group(function () {
    Route::prefix('clients/{clientId}/users')->group(function () {
        Route::get('/', [ClientUserController::class, 'index'])->name('client_users.index');
        Route::get('/create', [ClientUserController::class, 'create'])->name('client_users.create');
        Route::post('/', [ClientUserController::class, 'store'])->name('client_users.store');
        Route::get('/{userId}/edit', [ClientUserController::class, 'edit'])->name('client_users.edit');
        Route::put('/{userId}', [ClientUserController::class, 'update'])->name('client_users.update');
        Route::delete('/{userId}', [ClientUserController::class, 'destroy'])->name('client_users.destroy');
    });
});
Route::get('/csrf-token', function () {
    return response()->json(['csrfToken' => csrf_token()]);
});
Route::post('/set-timezone', function (Request $request) {
    Session::put('timezone', $request->timezone);
    return response()->json(['success' => true]);
})->name('set.timezone');


// Supplier Upload Invoice Form (GET)
Route::get('/supplier/invoice/{loadId}', function ($loadId) {
    return view('suppliers.upload_invoice', compact('loadId'));
})->name('supplier.upload.invoice');


////////////////////////// QuickBooks //////////////////////////

Route::get('/quickbooks/connect', [QuickBooksController::class, 'connect'])->name('quickbooks.connect');
Route::get('/quickbooks/callback', [QuickBooksController::class, 'callback'])->name('quickbooks.callback');
Route::get('/quickbooks/refresh-token', [QuickBooksController::class, 'refreshToken'])->name('quickbooks.refresh');

Route::post('/api/weather', [DashboardController::class, 'getWeather']);

Route::get('/chat/{number}/{name}', function ($number, $name) {
    return view('chat', ['number' => $number, 'name' => $name ]);
})->name('chat.here');

// Route::get('/passage/callback', [LoginController::class, 'handle']);

Route::get('/passage-login', [LoginController::class, 'loginhere'])->name('passage.login');

