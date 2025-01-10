<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use App\Mail\SendPasswordMail;
use App\Mail\SendOtpMail;
use App\Models\User;
use App\Models\ApiKey;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        // Validate login data
        $validatedData = $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
        ]);
    
        // Check user credentials
        $credentials = [
            'email' => $validatedData['email'],
            'password' => $validatedData['password'],
        ];
        
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'token' => $token,
                'message' => 'Login successful',
            ], 200);
        }
    
        return response()->json([
            'message' => 'Invalid credentials',
        ], 401);
    }

    public function getRole()
    {
    $user = Auth::user(); 
    if (!$user) {
        return response()->json([
            'message' => 'Unauthenticated.',
        ], 401);
    }

    return response()->json([
        'role' => $user->role,  
    ], 200); 
    }

    public function resetPassword(Request $request)
    {
        $validatedData = $request->validate([
            'current_password' => 'required|string|min:6',
            'new_password' => 'required|string|min:6|confirmed', 
        ]);
        $user = Auth::user();

        if (!Hash::check($validatedData['current_password'], $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect.',
            ], 400);
        }
    
        // Update the password
        $user->password = Hash::make($validatedData['new_password']);
        $user->save();
    
        return response()->json([
            'message' => 'Password successfully changed.',
        ], 200);
    }

    public function generateApiKey(Request $request)
    {
        try {
            $user = auth()->user();
    
            // Validate permissions
            $permissions = $request->permissions;
            if (empty($permissions)) {
                return response()->json(['error' => 'No permissions selected.'], 400);
            }
    
            // Generate a unique API key (you can use any method to generate the key)
            $apiKey = Str::random(32);
    
            // Construct the endpoint based on selected permissions
            $endpoints = [];
            foreach ($permissions as $permission) {
                switch ($permission) {
                    case 'create':
                        $endpoints[] = url('/api/create');
                        break;
                    case 'read':
                        $endpoints[] = url('/api/read');
                        break;
                    // Add more cases for 'update', 'delete', etc.
                }
            }
    
            // Check if an API key already exists for the user
            $apiKeyRecord = ApiKey::where('user_id', $user->id)->first();
    
            if ($apiKeyRecord) {
                $apiKeyRecord->key = $apiKey;
                $apiKeyRecord->permissions = json_encode($permissions);
                $apiKeyRecord->endpoint = implode(',', $endpoints);
                $apiKeyRecord->save(); 
            } else {
                // If no record exists, create a new one
                $apiKeyRecord = new ApiKey();
                $apiKeyRecord->user_id = $user->id;
                $apiKeyRecord->key = $apiKey;
                $apiKeyRecord->permissions = json_encode($permissions);
                $apiKeyRecord->endpoint = implode(',', $endpoints);
                $apiKeyRecord->save(); 
            }
    
            return response()->json([
                'api_key' => $apiKey,
                'endpoint' => $apiKeyRecord->endpoint
            ]);

        } catch (\Exception $e) {

            Log::error('Failed to generate API key: ' . $e->getMessage());
    
            return response()->json(['error' => 'An error occurred while generating the API key.'], 500);
        }
    }      
 }
