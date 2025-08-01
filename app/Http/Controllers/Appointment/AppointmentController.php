<?php

namespace App\Http\Controllers\Appointment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GoogleCalendarService;
use App\Models\Appointment;
use App\Models\Lead;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'lead' => 'required|exists:leads,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $appointment = Appointment::create([
            'lead_id' => $request->lead,
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'date' => $request->date,
            'user_id' => Auth::id(),
        ]);

        // Optional: Get lead info for response
        $lead = Lead::findOrFail($request->lead);

        // ✅ Sync to Google Calendar
        try {
            GoogleCalendarService::createEvent($request->all(), $lead->id, Auth::id());
        } catch (\Exception $e) {
            \Log::error('Google Calendar Sync Failed: ' . $e->getMessage());
            // You may return a partial success message or ignore based on UX preference
        }

        return response()->json([
            'appointment' => [
                'id' => $appointment->id,
                'title' => $appointment->title,
                'date' => $appointment->date,
                'lead_id' => $lead->id,
                'lead_name' => $lead->first_name . ' ' . $lead->last_name
            ]
        ]);
    }
    public function index()
    {
        $appointments = Appointment::with('lead')->where('user_id', Auth::id())->get();

        $formatted = $appointments->map(function ($appt) {
            return [
                'id' => $appt->id,
                'title' => $appt->title,
                'type' => 'Appointment',
                'date' => $appt->date,
                'lead_id' => $appt->lead_id,
                'lead_name' => $appt->lead->first_name . ' ' . $appt->lead->last_name,
            ];
        });

        return response()->json($formatted);
    }

}

