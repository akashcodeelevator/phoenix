<?php

use App\Http\Controllers\Admin\{FundRequestController, OrderController, ReportController, SettingController, UserAccountController, UserController, WithdrawRequestController};
use App\Http\Controllers\User\Auth\{ForgotPasswordController, ResetPasswordController, UserLoginController};
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\FundController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Register web routes for your application.
| These routes are loaded by the RouteServiceProvider within the "web" middleware group.
|
*/

Route::get('/', fn() => view('welcome'));

// Artisan commands route
Route::get('/runartisancommands', function () {
    $commands = [
        'config:clear',
        'config:cache',
        'route:clear',
        'view:clear',
        'view:cache',
        'storage:link'
    ];

    foreach ($commands as $command) {
        Artisan::call($command);
    }

    return response()->json([
        'message' => 'Artisan commands executed successfully!',
        'output' => Artisan::output(),
    ]);
});

// Admin routes with "auth:admin" middleware
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {

    // User Management
    Route::resource('users', UserController::class);
    Route::get('getusers/{username}', [UserController::class, 'getUserData'])->name('users.getUserData');

    // Order Management
    Route::resource('orders', OrderController::class);

    // User Account Management
    Route::resource('user_accounts', UserAccountController::class);

    // Fund Requests
    Route::prefix('fundrequests')->name('fundrequests.')->controller(FundRequestController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/create', 'store')->name('store');
        Route::post('/fund_request_approve', 'fund_request_approve')->name('approve');
        Route::get('/history', 'history')->name('history');
        Route::get('/{status?}', 'index')->name('status');
        Route::get('/show/{id}', 'fundrequestsshow')->name('show');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->controller(ReportController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/data', 'getReport')->name('getReport');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->controller(SettingController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/update', 'setting_update')->name('update');
        Route::get('/withdrawal', 'withdrawal_index')->name('withdrawal.index');
        Route::post('/withdrawal/update', 'withdrawal_setting_update')->name('withdrawal.update');
    });

    // Withdraw Requests
    Route::prefix('withdrawrequests')->name('withdrawrequest.')->controller(WithdrawRequestController::class)->group(function () {
        Route::get('/{status?}', 'index')->name('index');
        Route::post('/approve', 'withdraw_request_approve')->name('approve');
        Route::get('/show/{id}', 'withdrawrequestsshow')->name('show');
    });
});

// User Authentication Routes
Route::prefix('user')->group(function () {

    // Login and Password Reset Routes
    Route::controller(UserLoginController::class)->group(function () {
        Route::get('login', 'showLoginForm')->name('login');
        Route::post('login', [UserLoginController::class, 'login']);
        Route::post('logout', 'logout')->name('user.logout');
    });

    Route::controller(ForgotPasswordController::class)->group(function () {
        Route::get('password/reset', 'showLinkRequestForm')->name('password.reset');
        Route::post('password/reset', 'sendResetCodeEmail');
        Route::get('password/code-verify', 'codeVerify')->name('password.code.verify');
        Route::post('password/verify-code', 'verifyCode')->name('password.verify.code');
    });

    Route::controller(ResetPasswordController::class)->group(function () {
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset.form');
        Route::post('password/reset/change', 'reset')->name('password.change');
    });

    // Authenticated User Routes
    Route::middleware('auth:web')->name('user.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        //Profile 
        Route::get('/myprofile', [DashboardController::class, 'myprofile'])->name('myprofile');
        Route::get('/edit-profile', [DashboardController::class, 'edit'])->name('editprofile');
        Route::post('/profile/update', [DashboardController::class, 'update'])->name('profileupdate');

        //Fund Transfer submission
        Route::get('/fund-transfer', [FundController::class, 'fundTransferform'])->name('fundtransfer');
        Route::post('/fund-transfer', [FundController::class, 'fundTransfer'])->name('fundtransfer.submit');

        Route::get('/fund-requests', [FundController::class, 'fundRequest'])->name('fund.requests');
        Route::get('/get-fund-requests', [FundController::class, 'getFundRequests'])->name('getfundrequests');

        //Fund Convert submission
        Route::get('/fund-convert', [FundController::class, 'fundConvertform'])->name('fundconvert');
        Route::post('/fund-convert', [FundController::class, 'fundConvert'])->name('fundconvert.submit');

        Route::get('/fundconverthistory', [FundController::class, 'fundhistory'])->name('fundhistory');
        Route::get('/get-fundconvert-history', [FundController::class, 'getFundhistory'])->name('getfundhistory');

        //TOPUP 
        Route::get('/topup', [FundController::class, 'topupform'])->name(name: 'topup');
        Route::post('/topup-submit', [FundController::class, 'topup'])->name('topupsubmit');

        Route::get('/viptopup', [FundController::class, 'viptopupform'])->name(name: 'viptopup');
        Route::post('/viptopup-submit', [FundController::class, 'viptopup'])->name('viptopupsubmit');

        Route::get('/upgrade', [FundController::class, 'upgradeform'])->name(name: 'upgrade');
        Route::post('/upgrade-submit', [FundController::class, 'upgrade'])->name('upgradesubmit');

        //Withdraw
        Route::get('/withdraw', [FundController::class, 'withdrawform'])->name(name: 'withdraw');
        Route::post('/withdraw-submit', [FundController::class, 'withdraw'])->name('withdrawsubmit');

        Route::get('/withdrawhistory', [FundController::class, 'withdrawhistory'])->name('withdrawhistory');
        Route::get('/get-withdraw-history', [FundController::class, 'getwithdrawhistory'])->name('getwithdrawhistory');
    });
});