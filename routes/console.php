<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; // <-- यह लाइन महत्वपूर्ण है

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| आप अपनी कंसोल-आधारित कमांड्स को यहाँ रजिस्टर कर सकते हैं।
| ये कमांड्स Artisan कमांड लाइन इंटरफ़ेस के माध्यम से उपलब्ध होते हैं।
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// --- आपकी कमांड को शेड्यूल करने का तरीका ---
// Schedule::command() का उपयोग करें और उसे हर मिनट चलाने के लिए configure करें।
Schedule::command('process:lead-actions')->everyMinute();

// आप इसे हर 5 मिनट, हर घंटे, या किसी भी अंतराल पर चला सकते हैं:
// Schedule::command('process:lead-actions')->everyFiveMinutes();
// Schedule::command('process:lead-actions')->hourly();
