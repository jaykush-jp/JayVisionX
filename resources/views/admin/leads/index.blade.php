@extends('admin.layouts.app')
@section('title', $isCustomers ? 'Customers' : 'Leads')
@section('content')

<div class="page-header-row">
    <h2>{{ $isCustomers ? 'Customers' : 'Leads' }} <span class="count-badge">{{ $leads->total() }}</span></h2>
    <div class="header-actions">
        <a href="{{ route('admin.contacts.export', request()->query()) }}" class="btn btn-secondary"><i class="fas fa-file-csv"></i> Export CSV</a>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('addLeadModal').classList.add('open')"><i class="fas fa-plus"></i> Add Lead</button>
    </div>
</div>

@unless($isCustomers)
<!-- Pipeline stat strip -->
<div class="crm-stat-strip">
    <a href="{{ route('admin.contacts.index') }}" class="crm-stat {{ !$status ? 'active' : '' }}">
        <span class="crm-stat-num">{{ $stats['total'] }}</span><span class="crm-stat-label">All</span>
    </a>
    @foreach(\App\Models\Contact::STATUSES as $key => $label)
    <a href="{{ route('admin.contacts.index', ['status' => $key]) }}" class="crm-stat {{ $status === $key ? 'active' : '' }}">
        <span class="crm-stat-num">{{ $stats['by_status'][$key] ?? 0 }}</span>
        <span class="crm-stat-label"><span class="lead-badge lead-badge-{{ $key }}">{{ $label }}</span></span>
    </a>
    @endforeach
    <span class="crm-stat crm-stat-alert">
        <span class="crm-stat-num">{{ $stats['due'] }}</span><span class="crm-stat-label">Follow-ups due</span>
    </span>
</div>
@endunless

<!-- Filters -->
<form method="GET" class="crm-filter-bar">
    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search name, email, phone...">
    @unless($isCustomers)
    <select name="status" class="form-control">
        <option value="">All statuses</option>
        @foreach(\App\Models\Contact::STATUSES as $key => $label)
        <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    @endunless
    <select name="source" class="form-control">
        <option value="">All sources</option>
        @foreach(['contact_form' => 'Contact Form', 'landing' => 'Landing Page', 'chatbot' => 'Chatbot', 'meta' => 'Meta (FB/IG) Ads', 'google' => 'Google Ads', 'manual' => 'Manual'] as $key => $label)
        <option value="{{ $key }}" {{ request('source') === $key ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    <input type="date" name="from" value="{{ request('from') }}" class="form-control" title="From date">
    <input type="date" name="to" value="{{ request('to') }}" class="form-control" title="To date">
    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
    @if(request()->hasAny(['q','status','source','from','to']))
    <a href="{{ $isCustomers ? route('admin.customers') : route('admin.contacts.index') }}" class="btn btn-secondary">Reset</a>
    @endif
</form>

<div class="admin-table-wrapper">
    <table class="admin-table">
        <thead><tr><th>Name</th><th>Contact</th><th>Service</th><th>Status</th><th>Source</th><th>Follow-up</th><th>Date</th><th>Actions</th></tr></thead>
        <tbody>
            @forelse($leads as $c)
            <tr class="{{ !$c->is_read ? 'row-unread' : '' }}">
                <td><strong>{{ $c->name }}</strong>@if($c->value)<br><small class="text-muted">₹{{ number_format($c->value) }}</small>@endif</td>
                <td>
                    <a href="mailto:{{ $c->email }}">{{ $c->email }}</a>
                    @if($c->phone)<br><small class="text-muted">{{ $c->phone }}</small>@endif
                </td>
                <td>{{ $c->service_interested ?? '-' }}</td>
                <td><span class="lead-badge lead-badge-{{ $c->status }}">{{ $c->statusLabel() }}</span></td>
                <td><small>{{ ucfirst(str_replace('_',' ',$c->source ?? '-')) }}</small></td>
                <td>
                    @if($c->follow_up_at)
                        <span class="{{ $c->follow_up_at->isPast() ? 'text-danger' : '' }}">{{ $c->follow_up_at->format('d M, H:i') }}</span>
                    @else — @endif
                </td>
                <td>{{ $c->created_at->format('d M Y') }}</td>
                <td class="actions-cell">
                    <a href="{{ route('admin.contacts.show', $c->id) }}" class="btn-sm btn-view" title="Open"><i class="fas fa-eye"></i></a>
                    <form action="{{ route('admin.contacts.destroy', $c->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this lead?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-sm btn-delete" title="Delete"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center">No {{ $isCustomers ? 'customers' : 'leads' }} found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="pagination-wrapper">{{ $leads->links() }}</div>

<!-- Add Lead modal -->
<div class="crm-modal" id="addLeadModal">
    <div class="crm-modal-box">
        <div class="crm-modal-head">
            <h3>Add Lead</h3>
            <button type="button" class="crm-modal-close" onclick="document.getElementById('addLeadModal').classList.remove('open')">&times;</button>
        </div>
        <form action="{{ route('admin.contacts.store') }}" method="POST">
            @csrf
            <div class="form-row-2">
                <div class="form-group"><label>Name *</label><input type="text" name="name" class="form-control" required></div>
                <div class="form-group"><label>Email *</label><input type="email" name="email" class="form-control" required></div>
            </div>
            <div class="form-row-2">
                <div class="form-group"><label>Phone</label><input type="text" name="phone" class="form-control"></div>
                <div class="form-group"><label>Service Interested</label><input type="text" name="service_interested" class="form-control"></div>
            </div>
            <div class="form-row-2">
                <div class="form-group"><label>Budget</label><input type="text" name="budget" class="form-control"></div>
                <div class="form-group"><label>Deal Value (₹)</label><input type="number" step="0.01" name="value" class="form-control"></div>
            </div>
            <div class="form-group"><label>Message / Notes</label><textarea name="message" class="form-control" rows="3"></textarea></div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Lead</button>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('addLeadModal').classList.remove('open')">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection
