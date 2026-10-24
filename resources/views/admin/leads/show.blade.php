@extends('admin.layouts.app')
@section('title', 'Lead: ' . $lead->name)
@section('content')

<div class="page-header-row">
    <h2>{{ $lead->name }} <span class="lead-badge lead-badge-{{ $lead->status }}">{{ $lead->statusLabel() }}</span></h2>
    <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Leads</a>
</div>

<div class="crm-detail-grid">
    <!-- Left: details + actions -->
    <div class="crm-col">
        <div class="detail-card">
            <div class="detail-grid">
                <div class="detail-item"><strong>Email</strong><span><a href="mailto:{{ $lead->email }}">{{ $lead->email }}</a></span></div>
                <div class="detail-item"><strong>Phone</strong><span>{{ $lead->phone ?? 'Not provided' }}</span></div>
                <div class="detail-item"><strong>Service</strong><span>{{ $lead->service_interested ?? '-' }}</span></div>
                <div class="detail-item"><strong>Budget</strong><span>{{ $lead->budget ?? '-' }}</span></div>
                <div class="detail-item"><strong>Deal Value</strong><span>{{ $lead->value ? '₹' . number_format($lead->value) : '-' }}</span></div>
                <div class="detail-item"><strong>Source</strong><span>{{ ucfirst(str_replace('_',' ', $lead->source ?? '-')) }}</span></div>
                <div class="detail-item"><strong>Received</strong><span>{{ $lead->created_at->format('d M Y, h:i A') }}</span></div>
                <div class="detail-item"><strong>Last Contacted</strong><span>{{ $lead->last_contacted_at ? $lead->last_contacted_at->format('d M Y, h:i A') : 'Never' }}</span></div>
            </div>
            @if($lead->message)
            <div class="detail-message"><strong>Message:</strong><p>{{ $lead->message }}</p></div>
            @endif
            <div class="detail-actions">
                @if($lead->phone)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $lead->phone) }}" target="_blank" class="btn btn-whatsapp-lg"><i class="fab fa-whatsapp"></i> WhatsApp</a>
                <a href="tel:{{ $lead->phone }}" class="btn btn-secondary"><i class="fas fa-phone"></i> Call</a>
                @endif
            </div>
        </div>

        <!-- Status + follow-up -->
        <div class="admin-form-card">
            <form action="{{ route('admin.contacts.status', $lead->id) }}" method="POST" class="inline-form">
                @csrf
                <div class="form-group">
                    <label>Pipeline Status</label>
                    <div class="inline-controls">
                        <select name="status" class="form-control">
                            @foreach(\App\Models\Contact::STATUSES as $key => $label)
                            <option value="{{ $key }}" {{ $lead->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </form>
            <form action="{{ route('admin.contacts.follow-up', $lead->id) }}" method="POST" class="inline-form">
                @csrf
                <div class="form-group">
                    <label>Follow-up Reminder</label>
                    <div class="inline-controls">
                        <input type="datetime-local" name="follow_up_at" class="form-control" value="{{ optional($lead->follow_up_at)->format('Y-m-d\TH:i') }}">
                        <button type="submit" class="btn btn-secondary">Set</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Reply by email -->
        <div class="admin-form-card">
            <h3 class="section-heading">Send Email Reply</h3>
            <form action="{{ route('admin.contacts.reply', $lead->id) }}" method="POST">
                @csrf
                @if($templates->count())
                <div class="form-group">
                    <label>Use a template</label>
                    <select class="form-control" id="tplPicker">
                        <option value="">— Choose template —</option>
                        @foreach($templates as $t)
                        <option value="{{ $t->id }}" data-subject="{{ $t->subject }}" data-body="{{ $t->body }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="form-group"><label>Subject *</label><input type="text" name="subject" id="replySubject" class="form-control" value="Re: Your enquiry with {{ \App\Models\Setting::get('site_name', 'JayVisionX') }}" required></div>
                <div class="form-group"><label>Message *</label><textarea name="body" id="replyBody" class="form-control" rows="6" required></textarea></div>
                <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-paper-plane"></i> Send Reply</button>
            </form>
        </div>
    </div>

    <!-- Right: timeline -->
    <div class="crm-col">
        <div class="admin-form-card">
            <h3 class="section-heading">Log a Note / Call</h3>
            <form action="{{ route('admin.contacts.note', $lead->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <select name="type" class="form-control">
                        <option value="note">Note</option>
                        <option value="call">Call (sets last contacted)</option>
                    </select>
                </div>
                <div class="form-group"><textarea name="body" class="form-control" rows="3" placeholder="What happened?" required></textarea></div>
                <button type="submit" class="btn btn-secondary"><i class="fas fa-plus"></i> Add to timeline</button>
            </form>
        </div>

        <div class="admin-form-card">
            <h3 class="section-heading">Activity Timeline</h3>
            <ul class="crm-timeline">
                @forelse($lead->notes as $n)
                <li class="crm-timeline-item type-{{ $n->type }}">
                    <span class="crm-timeline-icon">
                        <i class="fas {{ ['note' => 'fa-sticky-note', 'call' => 'fa-phone', 'email' => 'fa-envelope', 'status_change' => 'fa-exchange-alt'][$n->type] ?? 'fa-circle' }}"></i>
                    </span>
                    <div>
                        <p>{{ $n->body }}</p>
                        <small class="text-muted">{{ $n->created_at->format('d M Y, h:i A') }} · {{ $n->created_by }}</small>
                    </div>
                </li>
                @empty
                <li class="text-muted">No activity yet.</li>
                @endforelse
            </ul>
        </div>

        @if($lead->emails->count())
        <div class="admin-form-card">
            <h3 class="section-heading">Email History</h3>
            <ul class="crm-email-log">
                @foreach($lead->emails as $m)
                <li>
                    <strong>{{ $m->subject }}</strong>
                    <span class="lead-badge lead-badge-{{ $m->status === 'sent' ? 'won' : 'lost' }}">{{ ucfirst($m->status) }}</span>
                    <small class="text-muted d-block">{{ $m->created_at->format('d M Y, h:i A') }}</small>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>

@endsection
@section('scripts')
<script>
    (function () {
        var picker = document.getElementById('tplPicker');
        if (!picker) return;
        picker.addEventListener('change', function () {
            var opt = picker.options[picker.selectedIndex];
            if (!opt.value) return;
            document.getElementById('replySubject').value = opt.getAttribute('data-subject') || '';
            document.getElementById('replyBody').value = opt.getAttribute('data-body') || '';
        });
    })();
</script>
@endsection
