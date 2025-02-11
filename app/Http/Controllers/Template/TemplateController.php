<?php

namespace App\Http\Controllers\Template;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    // Fetch all email templates
    public function index(Request $request)
    {
        $type = $request->get('type', 'email');
        
        $query = Template::where('type', $type);
    
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
    
        $templates = $query->paginate(10);
    
        return response()->json($templates); 
    }
    
    // Store a new email template
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $data = $request->only('type', 'title', 'subject', 'content');
        $data['created_by'] = auth()->id(); 

        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $request->file('attachment')->store('attachments', 'public');
        }

        $template = Template::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Template created successfully',
            'template' => $template,
        ]);
    }

    // Update an existing email template
    public function update(Request $request, $id)
    {
        $template = Template::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $data = $request->only('title', 'subject', 'content');

        if ($request->hasFile('attachment')) {
           
            if ($template->attachment_path) {
                \Storage::disk('public')->delete($template->attachment_path);
            }
            $data['attachment_path'] = $request->file('attachment')->store('attachments', 'public');
        }

        $template->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Template updated successfully',
            'template' => $template,
        ]);
    }

    // Delete an email template
    public function destroy($id)
    {
        $template = Template::findOrFail($id);

        if ($template->attachment_path) {
            \Storage::disk('public')->delete($template->attachment_path);
        }

        $template->delete();

        return response()->json([
            'success' => true,
            'message' => 'Template deleted successfully',
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:templates,id', 
        ]);

        Template::whereIn('id', $request->ids)->delete();

        return response()->json(['success' => true, 'message' => 'Templates deleted successfully.']);
    }
    
}
