<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Template;
use App\Models\TemplateFolder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use App\Models\User;



class TemplateFolderController extends Controller
{
    /**
     * Display a listing of folders for the authenticated user.
     */
    public function index(Request $request)
    {
        $userId = auth()->id();
        $type = $request->input('type', 'email');
    
        $folders = TemplateFolder::withCount('templates')
            ->with('user')
            ->where('user_id', $userId) 
            ->where('type', $type)
            ->get()
            ->map(function ($folder) {
                return [
                    'id' => $folder->id,
                    'name' => $folder->name,
                    'type' => $folder->type,
                    'template_count' => $folder->templates_count,
                    'created_by_name' => optional($folder->user)->name ?? 'Unknown',
                ];
            });
    
        return response()->json($folders);
    }
    
    /**
     * Store a newly created folder.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|in:email,sms',
        ]);

        $folder = TemplateFolder::create([
            'name' => $validated['name'],
            'type' => $validated['type'] ?? 'email',
            'user_id' => auth()->id(),  
        ]);

        return response()->json([
            'message' => 'Folder created successfully',
            'folder' => $folder
        ], 201);
    }

    /**
     * Delete the specified folder.
     */
    public function destroy($id)
    {
        $folder = TemplateFolder::where('id', $id)
            ->where('user_id', auth()->id()) 
            ->firstOrFail();

        $folder->templates()->delete();  

        $folder->delete();

        return response()->json(['message' => 'Folder and its templates deleted']);
    }

    /**
     * Rename the specified folder.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $folder = TemplateFolder::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $folder->name = $validated['name'];
        $folder->save();

        return response()->json([
            'message' => 'Folder updated successfully',
            'folder' => $folder,
        ]);
    }

}
