<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EditorController extends Controller
{
    public function uploadImage(Request $request)
    {
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('uploads', 'public');
            return response()->json(['url' => asset('storage/' . $path)]);
        }
    
        return response()->json(['error' => 'No file uploaded'], 400);
    }
    
}
