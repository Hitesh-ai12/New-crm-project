<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Appointment;
use Carbon\Carbon;
use App\Models\Email;
use App\Models\SmsMessage;
use Illuminate\Support\Facades\Auth;

class EventCountController extends Controller
{
    public function getTodayCounts()
    {
        $userId = Auth::id();
        $today = Carbon::today()->toDateString();

        $taskCount = Task::whereDate('due_date', $today)
                         ->where('user_id', $userId)
                         ->count();

        $appointmentCount = Appointment::whereDate('date', $today)
                                       ->where('user_id', $userId)
                                       ->count();

        return response()->json([
            'tasks' => $taskCount,
            'appointments' => $appointmentCount
        ]);
    }

    public function index()
    {
        $userId = Auth::id();

        $smsCount = SmsMessage::where('user_id', $userId)->count();
        $emailCount = Email::where('user_id', $userId)->count();

        return response()->json([
            'sms' => $smsCount,
            'email' => $emailCount,
        ]);
    }

}
