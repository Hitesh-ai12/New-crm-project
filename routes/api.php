<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\FormController;
use App\Http\Controllers\Template\TemplateController;
use App\Http\Controllers\AuthController;
use App\Models\ApiKey;
use App\Http\Controllers\Email\EmailController;
use App\Http\Controllers\SignatureController;
use App\Http\Controllers\Sms\SmsController;
use App\Http\Controllers\ColumnSettingController;
use App\Http\Controllers\Leads\LeadController;
use App\Http\Controllers\Settings\FileUploadController;
use App\Http\Controllers\Email\TemplateFolderController;
use App\Http\Controllers\Email\EmailTemplateController;
use App\Http\Controllers\Email\EditorController;
use App\Http\Controllers\Signature\SignatureFolderController;
use App\Http\Controllers\Settings\ItemController;
use App\Http\Controllers\Signature\SignatureTemplateController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Routes for login, role fetching, and password reset
Route::middleware('auth:sanctum')->get('/get-role', [AuthController::class, 'getRole']);
Route::post('form-submit', [FormController::class, 'store']);
Route::post('reset-password', [AuthController::class, 'resetPassword'])->middleware('auth:sanctum');
Route::post('/login', [AuthController::class, 'login']);

    Route::get('/items', [ItemController::class, 'index']);

    
Route::post('/send-email', [EmailController::class, 'sendEmail'])->name('send.email');
Route::post('/send-sms', [SmsController::class, 'sendSms'])->name('send.sms');
Route::get('/sms-templates', [EmailTemplateController::class, 'getSmsTemplates']);
Route::post('/upload', [FileUploadController::class, 'upload']);
Route::post('/incoming-sms', [SmsController::class, 'incomingSms']);
 Route::get('/columns-settings', [ColumnSettingController::class, 'getColumnSettings']);
// Route::middleware('auth:sanctum')->post('/generate-api-key', [AuthController::class, 'generateApiKey']);
// 
Route::post('/upload-image', [EditorController::class, 'uploadImage']);

Route::middleware('auth:sanctum')->group(function () {
    //Route::get('/templates', [TemplateController::class, 'index']);
    Route::get('/templates', [TemplateController::class, 'index']);

    Route::post('/leads', [LeadController::class, 'store']);
    Route::put('/leads/{id}', [LeadController::class, 'update']);
    Route::get('/leads/{id}', [LeadController::class, 'show']);

    Route::post('/templates', [TemplateController::class, 'store']);
    Route::put('/templates/{id}', [TemplateController::class, 'update']);
    Route::delete('/templates/{id}', [TemplateController::class, 'destroy']);
    Route::post('/templates/bulk-delete', [TemplateController::class, 'bulkDelete']);
   
    Route::post('/columns-settings', [ColumnSettingController::class, 'saveColumnSettings']);
    Route::post('/columns-order', [ColumnSettingController::class, 'saveColumnOrderSettings']);

 
    // Fetch all signatures
    Route::get('/signatures', [SignatureController::class, 'index']);

    // Store a new signature
    Route::post('/signatures', [SignatureController::class, 'store']);

    // Set the default signature
    Route::post('/signatures/set-default', [SignatureController::class, 'setDefault']);

    // Delete a signature
    Route::delete('/signatures/{id}', [SignatureController::class, 'destroy']);
    Route::put('/signatures/{id}', [SignatureController::class, 'update']);

    Route::get('/folders', [TemplateFolderController::class, 'index']);
    Route::post('/folders', [TemplateFolderController::class, 'store']);
    Route::put('/folders/{id}', [TemplateFolderController::class, 'update']);

    
    Route::delete('/folders/{id}', [TemplateFolderController::class, 'destroy']);

    Route::get('/folders/{id}/templates', [EmailTemplateController::class, 'getByFolder']);
    Route::post('/templates', [EmailTemplateController::class, 'store']);
    Route::put('/email-templates/{id}', [EmailTemplateController::class, 'update']);
    Route::delete('/email-templates/{id}', [EmailTemplateController::class, 'destroy']);
    
    Route::get('/sms-folders', [TemplateFolderController::class, 'index']);
    Route::post('/sms-folders', [TemplateFolderController::class, 'store']); // <-- Add this
    Route::delete('/sms-folders/{id}', [TemplateFolderController::class, 'destroy']);
    Route::get('/sms-templates/{id}', [TemplateController::class, 'showSmsTemplate']);
  
    Route::get('/signature-folders', [SignatureFolderController::class, 'index']);
    Route::post('/signature-folders', [SignatureFolderController::class, 'store']);
    Route::get('/signature-folders/{id}/templates', [SignatureFolderController::class, 'templates']);

    Route::post('/signature-templates', [SignatureTemplateController::class, 'store']); 
    Route::put('/signature-templates/{id}', [SignatureTemplateController::class, 'update']); 
    Route::delete('/signature-templates/{id}', [SignatureTemplateController::class, 'destroy']); 
    Route::get('/signature-templates/{id}', [SignatureTemplateController::class, 'show']);   

    // Featch signature by type
    Route::get('/signature-templates-email/email', [SignatureTemplateController::class, 'getEmailTemplates']);
    Route::get('/signature-templates/whatsapp', [SignatureTemplateController::class, 'getWhatsappTemplates']);
    Route::get('/signature-templates-sms/sms', [SignatureTemplateController::class, 'getSmsTemplates']);

    Route::get('/emails/replies/{leadEmail}', [EmailController::class, 'getReplies']);
    Route::get('/leads/{email}/emails', [EmailController::class, 'getEmailTimeline']);
    Route::get('/leads/{lead}/email-logs', [LeadController::class, 'getEmailLogs']);
    Route::get('/sent-emails', [EmailController::class, 'getSentEmails']);

    Route::get('/received-emails', [EmailController::class, 'getReceivedEmails']);
    Route::get('/incoming-sms', [SmsController::class, 'getIncomingSms']);
    Route::get('/sent-sms', [SmsController::class, 'getSentSms']);


    Route::post('/items', [ItemController::class, 'store']);
    Route::put('/items/{id}', [ItemController::class, 'update']);

    // Delete an item by ID
    Route::delete('/items/{id}', [ItemController::class, 'destroy']);
});


