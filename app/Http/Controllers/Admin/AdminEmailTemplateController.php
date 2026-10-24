<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class AdminEmailTemplateController extends Controller
{
    public function index()
    {
        return view('admin.email-templates.index', ['templates' => EmailTemplate::orderBy('name')->paginate(20)]);
    }

    public function create()
    {
        return view('admin.email-templates.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        EmailTemplate::create($data);
        return redirect()->route('admin.email-templates.index')->with('success', 'Template saved.');
    }

    public function edit(EmailTemplate $emailTemplate)
    {
        return view('admin.email-templates.create', ['template' => $emailTemplate]);
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $emailTemplate->update($this->validated($request));
        return redirect()->route('admin.email-templates.index')->with('success', 'Template updated.');
    }

    public function destroy(EmailTemplate $emailTemplate)
    {
        $emailTemplate->delete();
        return redirect()->route('admin.email-templates.index')->with('success', 'Template deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'    => 'required|string|max:191',
            'subject' => 'required|string|max:191',
            'body'    => 'required|string',
        ]);
    }
}
