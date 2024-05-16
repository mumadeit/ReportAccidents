<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportsAPIController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompaniesController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// register new user
Route::post('/register', [AuthController::class, 'register']);

// login
Route::post('/login', [AuthController::class, 'login']);

Route::group(['prefix' => 'reports'], function () {
    Route::get('/all', [ReportsAPIController::class, 'index']);
    Route::post('/new', [ReportsAPIController::class, 'store']);
    Route::put('/solved/{uuid}', [ReportsAPIController::class, 'solved']);
    Route::put('/canceled/{uuid}', [ReportsAPIController::class, 'canceled']);
    Route::delete('/delete/{uuid}', [ReportsAPIController::class, 'destroy']);
});

Route::group(['prefix' => 'companies'], function () {
    Route::get('/all', [CompaniesController::class, 'index']);
});

Route::group(['prefix' => 'breakdowns'], function () {
    Route::get('/all', [CompaniesController::class, 'breakdown']);
});
