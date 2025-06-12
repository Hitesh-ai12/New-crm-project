<?php

namespace App\Http\Controllers\Signature;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SignatureFolder;
use Illuminate\Support\Facades\Auth;
use App\Models\SignatureTemplate;


class SignatureFolderController extends Controller
{
      public function index()
    {
        return SignatureFolder::withCount('templates')
            ->with('user:id,name')
            ->where('user_id', Auth::id())
            ->get()
            ->map(function ($folder) {
                return [
                    'id' => $folder->id,
                    'name' => $folder->name,
                    'template_count' => $folder->templates_count,
                    'created_by_name' => $folder->user->name ?? 'Unknown'
                ];
            });
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:signature_folders,name',
        ]);

        $folder = SignatureFolder::create([
            'name' => $request->name,
            'type' => 'signature',
            'user_id' => Auth::id()
        ]);

        return response()->json($folder, 201);
    }

    public function templates($id)
    {
        $folder = SignatureFolder::with('templates')->findOrFail($id);
        return response()->json($folder->templates);
    }
}
