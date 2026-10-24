@extends('admin.layouts.app')
@section('title', 'Conversation')
@section('content')
<div class="page-header-row">
    <h2>Conversation #{{ $session->id }}
        @if($session->is_ai_active)<span class="lead-badge lead-badge-qualified">AI active</span>@else<span class="lead-badge lead-badge-proposal">Human</span>@endif
    </h2>
    <a href="{{ route('admin.inbox.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Inbox</a>
</div>

<div class="crm-detail-grid">
    <!-- Transcript -->
    <div class="crm-col">
        <div class="admin-form-card">
            <h3 class="section-heading">Transcript</h3>
            <div class="transcript">
                @forelse($session->messages as $m)
                    @if($m->role === 'system')
                        <div class="transcript-system"><i class="fas fa-info-circle"></i> {{ $m->content }} <small>· {{ $m->created_at->format('d M, H:i') }}</small></div>
                    @else
                    <div class="transcript-msg {{ $m->role }}">
                        <div class="transcript-bubble">{{ $m->content }}</div>
                        <small class="transcript-time">{{ ucfirst($m->role) }} · {{ $m->created_at->format('d M, H:i') }}</small>
                    </div>
                    @endif
                @empty
                <p class="text-muted">No messages.</p>
                @endforelse
            </div>

            <form action="{{ route('admin.inbox.note', $session->id) }}" method="POST" style="margin-top:14px">
                @csrf
                <div class="form-group">
                    <label>Internal note <small class="text-muted">(not shown to the visitor)</small></label>
                    <textarea name="body" class="form-control" rows="2" required></textarea>
                </div>
                <button type="submit" class="btn btn-xs btn-secondary"><i class="fas fa-plus"></i> Add note</button>
            </form>
        </div>
    </div>

    <!-- Controls -->
    <div class="crm-col">
        <div class="admin-form-card">
            <h3 class="section-heading">Visitor</h3>
            <div class="detail-grid">
                <div class="detail-item"><strong>Name</strong><span>{{ $session->visitor_name ?: '—' }}</span></div>
                <div class="detail-item"><strong>Email</strong><span>{{ $session->visitor_email ?: '—' }}</span></div>
                <div class="detail-item"><strong>Phone</strong><span>{{ $session->visitor_phone ?: '—' }}</span></div>
                <div class="detail-item"><strong>Consent</strong><span>{{ $session->consent ? 'Yes' : '—' }}</span></div>
                <div class="detail-item"><strong>Source page</strong><span>{{ $session->source_page ? Str::limit($session->source_page, 40) : '—' }}</span></div>
                <div class="detail-item"><strong>Started</strong><span>{{ $session->created_at->format('d M Y, H:i') }}</span></div>
            </div>
        </div>

        <div class="admin-form-card">
            <h3 class="section-heading">Controls</h3>
            <form action="{{ route('admin.inbox.toggle-human', $session->id) }}" method="POST" style="margin-bottom:12px">
                @csrf
                @if($session->is_ai_active)
                <button type="submit" class="btn btn-primary btn-full"><i class="fas fa-headset"></i> Take over (pause AI)</button>
                @else
                <button type="submit" class="btn btn-secondary btn-full"><i class="fas fa-robot"></i> Return to AI</button>
                @endif
            </form>

            <form action="{{ route('admin.inbox.status', $session->id) }}" method="POST" class="inline-form">
                @csrf
                <div class="form-group"><label>Status</label>
                    <div class="inline-controls">
                        <select name="status" class="form-control">
                            @foreach(\App\Http\Controllers\Admin\AdminInboxController::STATUSES as $k => $v)
                            <option value="{{ $k }}" {{ $session->status===$k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-secondary">Set</button>
                    </div>
                </div>
            </form>

            <form action="{{ route('admin.inbox.assign', $session->id) }}" method="POST" class="inline-form">
                @csrf
                <div class="form-group"><label>Assigned to</label>
                    <div class="inline-controls">
                        <input type="text" name="assigned_to" class="form-control" value="{{ $session->assigned_to }}" placeholder="agent email/name">
                        <button type="submit" class="btn btn-secondary">Save</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="admin-form-card">
            <h3 class="section-heading">Lead</h3>
            @if($session->lead)
                <p>Linked to lead <a href="{{ route('admin.contacts.show', $session->lead->id) }}">#{{ $session->lead->id }} — {{ $session->lead->name }}</a>.</p>
            @else
                <form action="{{ route('admin.inbox.convert', $session->id) }}" method="POST">
                    @csrf
                    <div class="form-group"><label>Name *</label><input type="text" name="name" class="form-control" value="{{ $session->visitor_name }}" required></div>
                    <div class="form-group"><label>Email *</label><input type="email" name="email" class="form-control" value="{{ $session->visitor_email }}" required></div>
                    <div class="form-group"><label>Phone</label><input type="text" name="phone" class="form-control" value="{{ $session->visitor_phone }}"></div>
                    <button type="submit" class="btn btn-xs btn-primary"><i class="fas fa-user-plus"></i> Convert to lead</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
