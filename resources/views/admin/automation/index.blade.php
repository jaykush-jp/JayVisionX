@extends('admin.layouts.app')
@section('title', 'Automation Rules')
@section('content')
<div class="page-header-row">
    <h2>Automation Rules <span class="count-badge">{{ $rules->count() }}</span></h2>
    <a href="{{ route('admin.automation.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> New Rule</a>
</div>
<p class="muted-intro">Rules run automatically on chat events — notify your team, post an auto message, or flag a conversation.</p>

<div class="admin-table-wrapper">
    <table class="admin-table">
        <thead><tr><th>Name</th><th>When</th><th>Does</th><th>Active</th><th>Actions</th></tr></thead>
        <tbody>
            @forelse($rules as $r)
            <tr>
                <td><strong>{{ $r->name }}</strong></td>
                <td><span class="lead-badge lead-badge-qualified">{{ $r->triggerLabel() }}</span></td>
                <td>{{ $r->actionLabel() }}@if($r->message)<br><small class="text-muted">{{ Str::limit($r->message, 50) }}</small>@endif</td>
                <td>
                    <form action="{{ route('admin.automation.toggle', $r->id) }}" method="POST" style="display:inline">
                        @csrf
                        <button type="submit" class="lead-badge lead-badge-{{ $r->is_active ? 'won' : 'lost' }}" style="border:none;cursor:pointer">{{ $r->is_active ? 'On' : 'Off' }}</button>
                    </form>
                </td>
                <td class="actions-cell">
                    <a href="{{ route('admin.automation.edit', $r->id) }}" class="btn-sm btn-edit"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.automation.destroy', $r->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete rule?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-sm btn-delete"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center">No rules yet. Create one to automate chat follow-ups.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="dashboard-section" style="margin-top:24px">
    <h3 class="section-heading">Recent automation activity</h3>
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead><tr><th>When</th><th>Trigger</th><th>Rule</th><th>Summary</th></tr></thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('d M, H:i') }}</td>
                    <td>{{ $triggers[$log->trigger] ?? $log->trigger }}</td>
                    <td>{{ $log->rule->name ?? '—' }}</td>
                    <td><small class="text-muted">{{ Str::limit($log->summary, 80) }}</small></td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center">No activity yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
