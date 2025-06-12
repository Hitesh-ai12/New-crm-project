<?php

namespace App\Http\Controllers\Signature;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SignatureTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\SignatureFolder;


class SignatureTemplateController extends Controller
{
public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string',
        'subject' => 'nullable|string',
        'content' => 'required|string',
        'folder_id' => 'required|exists:signature_folders,id',
        'type' => 'required|in:email,sms,whatsapp', // <-- यहाँ validation जोड़ा
        'attachment' => 'nullable|file',
    ]);

    $folder = SignatureFolder::findOrFail($request->folder_id);

    $template = new SignatureTemplate();
    $template->title = $request->title;
    $template->subject = $request->subject;
    $template->content = $request->content;
    $template->folder_id = $folder->id;
    $template->user_id = auth()->id();

    // यहाँ folder->type की बजाय request से type लें:
    $template->type = $request->type;

    // Handle attachment upload if needed...
    if ($request->hasFile('attachment')) {
        $path = $request->file('attachment')->store('attachments');
        $template->attachment_path = $path;
    }

    $template->save();

    return response()->json(['message' => 'Template created successfully', 'data' => $template], 201);
}

public function update(Request $request, $id)
{
    $request->validate([
        'title' => 'required|string',
        'subject' => 'nullable|string',
        'content' => 'required|string',
        'folder_id' => 'required|exists:signature_folders,id',
        'attachment' => 'nullable|file',
        'type' => 'required|in:email,sms,whatsapp'
    ]);

    $template = SignatureTemplate::findOrFail($id);
    $template->title = $request->title;
    $template->subject = $request->subject;
    $template->content = $request->content;
    $template->folder_id = $request->folder_id;
    $template->type = $request->type;

    if ($request->hasFile('attachment')) {
        $path = $request->file('attachment')->store('attachments');
        $template->attachment_path = $path;
    }

    $template->save();

    return response()->json(['message' => 'Template updated successfully', 'data' => $template], 200);
}

public function destroy($id)
{
    $template = SignatureTemplate::findOrFail($id);

    if ($template->attachment_path && Storage::exists($template->attachment_path)) {
        Storage::delete($template->attachment_path);
    }

    $template->delete();

    return response()->json(['message' => 'Template deleted successfully'], 200);
}

public function getEmailTemplates()
{
    $templates = SignatureTemplate::where('type', 'email')
                  ->where('user_id', auth()->id())
                  ->with('folder') 
                  ->get();

    return response()->json($templates);
}

public function getSmsTemplates()
{
    $templates = SignatureTemplate::where('type', 'sms')
                  ->where('user_id', auth()->id())
                  ->with('folder')
                  ->get();

    return response()->json($templates);
}

public function getWhatsappTemplates()
{
    $templates = SignatureTemplate::where('type', 'whatsapp')
                  ->where('user_id', auth()->id())
                  ->with('folder')
                  ->get();

    return response()->json($templates);
}
}
