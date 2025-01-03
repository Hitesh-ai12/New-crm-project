<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\FormController;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('form-submit', [FormController::class, 'store']);

// In routes/api.php or routes/web.php
Route::post('reset-password', [AuthController::class, 'resetPassword'])->middleware('auth:sanctum');


Route::post('/login', [AuthController::class, 'login']);
