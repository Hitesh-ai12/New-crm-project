<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FacebookIntegrationController extends Controller
{
    public function saveAccessToken(Request $request)
    {
        $accessToken = $request->input('access_token');

        // Save the access token to the database or session (as per your needs)
        // Example: Store it in session
        session(['facebook_access_token' => $accessToken]);

        // You can also save it in a database if needed

        return response()->json(['message' => 'Access token saved successfully']);
    }

    public function fetchLeads()
    {
        $accessToken = session('facebook_access_token');

        if (!$accessToken) {
            return response()->json(['error' => 'Access token not found'], 401);
        }

        // Make API request to Facebook Graph API to fetch leads
        $response = Http::get("https://graph.facebook.com/v18.0/your-page-id/leads", [
            'access_token' => $accessToken,
        ]);

        if ($response->successful()) {
            $leads = $response->json();
            return response()->json($leads);
        }

        return response()->json(['error' => 'Failed to fetch leads'], 500);
    }
}
