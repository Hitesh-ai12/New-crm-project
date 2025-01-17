<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\FormController;
use App\Http\Controllers\Template\TemplateController;
use App\Http\Controllers\AuthController;
use App\Models\ApiKey;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Routes for login, role fetching, and password reset
Route::middleware('auth:sanctum')->get('/get-role', [AuthController::class, 'getRole']);
Route::post('form-submit', [FormController::class, 'store']);
Route::post('reset-password', [AuthController::class, 'resetPassword'])->middleware('auth:sanctum');
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->post('/generate-api-key', [AuthController::class, 'generateApiKey']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/templates', [TemplateController::class, 'index']);
    Route::post('/templates', [TemplateController::class, 'store']);
    Route::put('/templates/{id}', [TemplateController::class, 'update']);
    Route::delete('/templates/{id}', [TemplateController::class, 'destroy']);
    Route::post('/templates/bulk-delete', [TemplateController::class, 'bulkDelete']);
});
