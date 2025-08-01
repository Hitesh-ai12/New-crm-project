<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Integration\FacebookController;
use App\Http\Controllers\Leads\LeadController;
use App\Http\Controllers\Webhook\GmailWebhookController;


// Route::post('/leads', [LeadController::class, 'store']);  
// Route::get('/leads', [LeadController::class, 'index']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/leads/delete', [LeadController::class, 'deleteLeads']);
Route::post('/leads/export', [LeadController::class, 'exportLeads']);

Route::get('/gmail/auth', [GmailWebhookController::class, 'redirectToGoogle']);
Route::get('/gmail/callback', [GmailWebhookController::class, 'handleGoogleCallback']);
Route::get('/gmail/start-watch', [GmailWebhookController::class, 'startWatch']);
// API routes
Route::get('/lead-form', function (Request $request) {
    $userId = $request->query('user_id'); 
    return view('lead-form', compact('userId'));
});
Route::get('{any?}', function() {
    return view('application');
})->where('any', '.*');
