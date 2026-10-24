<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\AutomationLog;
use App\Models\AutomationRule;
use Illuminate\Http\Request;

class AdminAutomationController extends Controller
{
    public function index()
    {
        return view('admin.automation.index', [
            'rules'    => AutomationRule::orderBy('sort_order')->orderBy('id')->get(),
            'logs'     => AutomationLog::with('rule')->latest()->take(30)->get(),
            'triggers' => AutomationRule::TRIGGERS,
            'actions'  => AutomationRule::ACTIONS,
        ]);
    }

    public function create()
    {
        return view('admin.automation.create', [
            'triggers' => AutomationRule::TRIGGERS,
            'actions'  => AutomationRule::ACTIONS,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $rule = AutomationRule::create($data);
        AuditLog::record('created', 'automation_rule', $rule->id, ['name' => $rule->name]);

        return redirect()->route('admin.automation.index')->with('success', 'Automation rule created.');
    }

    public function edit(AutomationRule $automation)
    {
        return view('admin.automation.create', [
            'rule'     => $automation,
            'triggers' => AutomationRule::TRIGGERS,
            'actions'  => AutomationRule::ACTIONS,
        ]);
    }

    public function update(Request $request, AutomationRule $automation)
    {
        $automation->update($this->validated($request));
        AuditLog::record('updated', 'automation_rule', $automation->id, ['name' => $automation->name]);

        return redirect()->route('admin.automation.index')->with('success', 'Automation rule updated.');
    }

    public function destroy(AutomationRule $automation)
    {
        AuditLog::record('deleted', 'automation_rule', $automation->id, ['name' => $automation->name]);
        $automation->delete();

        return redirect()->route('admin.automation.index')->with('success', 'Automation rule deleted.');
    }

    public function toggle(AutomationRule $automation)
    {
        $automation->update(['is_active' => ! $automation->is_active]);
        return back()->with('success', 'Rule ' . ($automation->is_active ? 'enabled' : 'disabled') . '.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name'         => 'required|string|max:150',
            'trigger'      => 'required|in:' . implode(',', array_keys(AutomationRule::TRIGGERS)),
            'action'       => 'required|in:' . implode(',', array_keys(AutomationRule::ACTIONS)),
            'message'      => 'nullable|string|max:1000',
            'notify_email' => 'nullable|email|max:191',
            'sort_order'   => 'nullable|integer',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}
