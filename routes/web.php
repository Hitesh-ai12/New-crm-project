<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Integration\FacebookController;
use App\Http\Controllers\Leads\LeadController;
use App\Http\Controllers\Settings\ItemController;


Route::middleware('auth:sanctum')->post('/leads', [LeadController::class, 'store']);  
Route::get('/leads', [LeadController::class, 'index']);

Route::post('/leads/delete', [LeadController::class, 'deleteLeads']);
Route::post('/leads/export', [LeadController::class, 'exportLeads']);

// API routes
Route::get('/items', [ItemController::class, 'index']);

Route::post('/api/items', [ItemController::class, 'store']);
Route::put('/api/items/{id}', [ItemController::class, 'update']);

// Delete an item by ID
Route::delete('/items/{id}', [ItemController::class, 'destroy']);


Route::get('{any?}', function() {
    return view('application');
})->where('any', '.*');
