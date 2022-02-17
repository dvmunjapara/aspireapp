<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LoanController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [AuthController::class, 'doLogin']);

Route::prefix('loan')->middleware('auth:sanctum')->group(function () {

    Route::get('{loan}',[LoanController::class, 'show'])->can('view','loan');
    Route::post('apply', [LoanController::class, 'apply']);
    Route::post('process/{loan}/{status}', [LoanController::class, 'process'])->can('process','loan');
    Route::post('repay/{loan}', [LoanController::class, 'repayment']);
});
