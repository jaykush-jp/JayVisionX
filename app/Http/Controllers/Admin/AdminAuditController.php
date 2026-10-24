<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AdminAuditController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::query();

        if ($entity = $request->input('entity_type')) {
            $query->where('entity_type', $entity);
        }
        if ($search = trim((string) $request->input('q'))) {
            $query->where(function ($w) use ($search) {
                $w->where('action', 'like', "%{$search}%")
                    ->orWhere('performed_by', 'like', "%{$search}%");
            });
        }

        $logs = $query->latest()->paginate(30)->withQueryString();

        $entityTypes = AuditLog::query()->distinct()->orderBy('entity_type')->pluck('entity_type');

        return view('admin.audit.index', compact('logs', 'entityTypes'));
    }
}
