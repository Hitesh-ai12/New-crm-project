<?php

namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;

class FacebookController extends Controller
{
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback(Request $request)
    {
        // Get the access token from the query parameters
        $accessToken = $request->input('access_token');

        if ($accessToken) {
            // Pass the token back to the frontend via a view or JSON response
            return view('facebook-auth', ['accessToken' => $accessToken]);
        }

        return redirect('/')->with('error', 'Failed to authenticate with Facebook.');
    }

    public function fetchLeads()
    {
        // Ideally, retrieve the access token from a secure storage or environment variable
        $accessToken = 'your_access_token'; // Replace with secure method to obtain the token

        $response = Http::get('https://graph.facebook.com/v18.0/me/leads', [
            'access_token' => $accessToken
        ]);

        if ($response->successful()) {
            return response()->json($response->json()['data']);
        } else {
            return response()->json(['error' => 'Failed to fetch leads.'], 500);
        }
    }
}
