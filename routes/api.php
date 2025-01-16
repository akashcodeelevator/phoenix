<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::prefix('v1')->group(function () {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/roi', [PlanController::class, 'roi']);
    Route::post('/email-send', [UserController::class, 'email_send']);
    Route::post('/otp-verify', [UserController::class, 'verify_otp']);
    Route::get('verify_wallet', [LoginController::class, 'validateWallet']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [UserController::class, 'getUserDetails']);
        Route::post('/kyc-verification', [UserController::class, 'saveKyc']);
        Route::get('/get_kyc', [UserController::class, 'getKycDetails']);
        Route::post('/change-password', [UserController::class, 'changePassword']);
        Route::post('/logout', [UserController::class, 'logout']);
        Route::post('/profile-update', [UserController::class, 'updateUserProfile'])->name('profile-update');
        //Packages
        Route::get('/checksponsor', [PlanController::class, 'checksponsor']);
        Route::get('/pin-details', [PlanController::class, 'getActivePinDetails']);
        Route::post('/investment', [PlanController::class, 'investment']);
        //Fund
        Route::get('/fund-details', [PlanController::class, 'getFundDetails']);
        Route::post('/fund-request', [PlanController::class, 'addFundRequest']);
        Route::post('/fund-transfer', [PlanController::class, 'addFundTransfer']);
        Route::get('/fund-request-history', [PlanController::class, 'getFundRequestHistory']);
        //Report
        Route::get('/report', [PlanController::class, 'getReport']);
        Route::post('/verify-transaction', [PlanController::class, 'verifyTransaction']);
        //Team
        Route::get('/teamdirect', [PlanController::class, 'getTeam']);
        //withdrawal
        Route::post('/updateWalletAddress', [PlanController::class, 'updateWalletAddress']);
        Route::post('/fundWithdraw', [PlanController::class, 'fundWithdraw']);
        Route::get('/withdraw-request-history', [PlanController::class, 'getWithdrawRequestHistory']);
    });
});