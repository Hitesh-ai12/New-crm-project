<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Integration\FacebookController;
use App\Http\Controllers\Leads\LeadController;


Route::middleware('auth:sanctum')->post('/leads', [LeadController::class, 'store']);  
Route::get('/leads', [LeadController::class, 'index']);

Route::post('/leads/delete', [LeadController::class, 'deleteLeads']);
Route::post('/leads/export', [LeadController::class, 'exportLeads']);

// API routes
Route::get('/lead-form', function (Request $request) {
    $userId = $request->query('user_id'); // URL se user_id fetch karein
    return view('lead-form', compact('userId'));
});
Route::get('{any?}', function() {
    return view('application');
})->where('any', '.*');
