<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Template;
use App\Models\TemplateFolder;

class EmailTemplateController extends Controller
{
    public function getByFolder($id)
    {
        $type = request('type', 'email'); 

        return Template::where('folder_id', $id)
            ->where('created_by', auth()->id())
            ->where('type', $type)
            ->get();
    }

    public function store(Request $request)
    {
        $type = $request->input('type');

        $rules = [
            'title' => 'required|string',
            'folder_id' => 'required|exists:template_folders,id',
            'content' => 'required|string',
            'type' => 'required|in:email,sms',
        ];

        // Email-specific fields
        if ($type === 'email') {
            $rules['subject'] = 'required|string';
            $rules['attachment'] = 'nullable|file';
        }

        $validated = $request->validate($rules);

        $template = new Template();
        $template->title = $validated['title'];
        $template->folder_id = $validated['folder_id'];
        $template->content = $validated['content'];
        $template->type = $validated['type'];
        $template->created_by = auth()->id();

        if ($type === 'email') {
            $template->subject = $validated['subject'] ?? null;

            if ($request->hasFile('attachment')) {
                $path = $request->file('attachment')->store('attachments', 'public');
                $template->attachment_path = $path;
            }
        }

        $template->save();

        return response()->json($template, 201);
    }

    public function update(Request $request, $id)
    {
        $template = Template::findOrFail($id);
        $type = $request->input('type', $template->type);

        $rules = [
            'title' => 'required|string',
            'folder_id' => 'required|exists:template_folders,id',
            'content' => 'required|string',
            'type' => 'required|in:email,sms',
        ];

        if ($type === 'email') {
            $rules['subject'] = 'required|string';
            $rules['attachment'] = 'nullable|file';
        }

        $validated = $request->validate($rules);

        $template->title = $validated['title'];
        $template->folder_id = $validated['folder_id'];
        $template->content = $validated['content'];
        $template->type = $validated['type'];

        if ($type === 'email') {
            $template->subject = $validated['subject'] ?? null;

            if ($request->hasFile('attachment')) {
                $path = $request->file('attachment')->store('attachments', 'public');
                $template->attachment_path = $path;
            }
        } else {
            $template->subject = null;
            $template->attachment_path = null;
        }

        $template->save();

        return response()->json($template);
    }

    public function destroy($id)
    {
        $template = Template::findOrFail($id);
        $template->delete();

        return response()->json(['message' => 'Template deleted successfully.']);
    }

    public function showSmsTemplate($id)
    {
        $template = Template::where('id', $id)->where('type', 'sms')->firstOrFail();
        return response()->json($template);
    }
    public function getSmsTemplates()
    {
        $templates = Template::where('type', 'sms')
            ->select('id', 'title as name', 'content')
            ->get();

        return response()->json(['templates' => $templates]);
    }

}
