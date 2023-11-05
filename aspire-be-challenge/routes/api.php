<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\LoanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
});

Route::controller(LoanController::class)->group(function () {
    Route::post('create-loan-request', 'createLoanRequest');
    Route::get('get-loans', 'getLoansForUser');
    Route::post('add-loan-repayment', 'addLoanRepayment');
    Route::post('approve-loan', 'approveLoan');
});

