<?php

//use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\FundRequestController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserAccountController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WithdrawRequestController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//Auth::routes();

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/runartisancommands', function () {
    // Run the necessary artisan commands
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('view:cache');
    Artisan::call('storage:link');
    // Return a success message and the output of the commands
    return response()->json([
        'message' => 'Artisan commands executed successfully!',
        'output' => Artisan::output(),
    ]);
});
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class);
    Route::get('getusers/{username}', [UserController::class, 'getUserData'])->name('username');
    Route::resource('orders', OrderController::class);
    Route::resource('user_accounts', UserAccountController::class);
});
Route::middleware(middleware: ['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Route::get('/fundrequests', [FundRequestController::class, 'index'])->name('fundrequests.index');
    Route::get('/fundrequests', [FundRequestController::class, 'create'])->name('fundrequests.create');
    Route::get('/fund_transfer_history', [FundRequestController::class, 'history'])->name('fundrequests.history');
    Route::post('/fundrequests/create', [FundRequestController::class, 'store'])->name('fundrequests.create');
    Route::post('/fundrequests/fund_request_approve', [FundRequestController::class, 'fund_request_approve'])->name('fundrequests.approve');
    Route::get('/fundrequests/{status?}', [FundRequestController::class, 'index'])->name('fundrequests.index');
    Route::get('/fundrequestsshow/{id}', [FundRequestController::class, 'fundrequestsshow'])->name('fundrequests.show');
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/data', [ReportController::class, 'getReport'])->name('reports.getReport');

    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('setting_update', [SettingController::class, 'setting_update'])->name('settings.update');

    Route::get('withdrawal_settings', [SettingController::class, 'withdrawal_index'])->name('withdrawal_settings.index');
    Route::post('withdrawal_settings_update', [SettingController::class, 'withdrawal_setting_update'])->name('withdrawal_settings.update');

    Route::post('/withdrawrequests/withdraw_request_approve', [WithdrawRequestController::class, 'withdraw_request_approve'])->name('withdrawrequest.approve');
    Route::get('/withdrawrequest/{status?}', [WithdrawRequestController::class, 'index'])->name('withdrawrequest.index');
    Route::get('/withdrawrequestsshow/{id}', [WithdrawRequestController::class, 'withdrawrequestsshow'])->name('withdrawrequest.show');
});

Route::middleware(middleware: ['auth:user'])->prefix('user')->name('user.')->group(function () {
    
});