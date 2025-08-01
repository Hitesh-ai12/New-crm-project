<?php
namespace App\Services;

use App\Models\Lead;
use App\Models\User;
use App\Services\GmailService;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Carbon\Carbon;

class GoogleCalendarService
{
    public static function createEvent($appointment, $leadId, $userId)
    {
        $lead = Lead::findOrFail($leadId);
        $client = GmailService::getGoogleClient($userId);

        $calendarService = new Google_Service_Calendar($client);

        $event = new Google_Service_Calendar_Event([
            'summary' => $appointment['title'],
            'description' => $appointment['description'] ?? '',
            'location' => $appointment['location'] ?? '',
            'start' => [
                'dateTime' => Carbon::parse($appointment['date'])->toRfc3339String(),
                'timeZone' => 'Asia/Kolkata',
            ],
            'end' => [
                'dateTime' => Carbon::parse($appointment['date'])->addHour()->toRfc3339String(),
                'timeZone' => 'Asia/Kolkata',
            ],
            'attendees' => [
                ['email' => $lead->email]
            ],
        ]);

        return $calendarService->events->insert('primary', $event);
    }
}

?>
