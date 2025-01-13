<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class FormController extends Controller
{

    public function store(Request $request)
    {
        // Validate incoming data
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'email1' => 'required|email|max:255',
            'email2' => 'nullable|email|max:255',
            'email3' => 'nullable|email|max:255',
            'phone1' => 'required|string|max:20',
            'phone2' => 'nullable|string|max:20',
            'phone3' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'role' => 'required|string|max:50',
        ]);
    
        try {
            // Store user data in the users table
            $user = User::create([
                'name' => $validatedData['first_name'],
                'email' => $validatedData['email1'],
                'password' => bcrypt($validatedData['password']),
                'role' => $validatedData['role'],
            ]);
    
            // Store nullable fields in the user_details table
            $user->details()->create([
                'email2' => $validatedData['email2'],
                'email3' => $validatedData['email3'],
                'phone1' => $validatedData['phone1'],
                'phone2' => $validatedData['phone2'],
                'phone3' => $validatedData['phone3'],
            ]);
    
            return response()->json([
                'message' => 'Form submitted successfully!',
                'data' => $user->load('details'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while saving the data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
