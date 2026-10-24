@extends('admin.layouts.app')
@section('title', 'Quote Request')
@section('content')
<div class="page-header-row">
    <h2>Quote #{{ $quote->id }} <span class="lead-badge lead-badge-qualified">{{ $quote->projectLabel() }}</span></h2>
    <a href="{{ route('admin.quotes.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>

<div class="crm-detail-grid">
    <div class="crm-col">
        <div class="detail-card">
            <div class="detail-grid">
                <div class="detail-item"><strong>Name</strong><span>{{ $quote->name ?: '—' }}</span></div>
                <div class="detail-item"><strong>Email</strong><span>{{ $quote->email ?: '—' }}</span></div>
                <div class="detail-item"><strong>Phone</strong><span>{{ $quote->phone ?: '—' }}</span></div>
                <div class="detail-item"><strong>Project</strong><span>{{ $quote->projectLabel() }}</span></div>
                <div class="detail-item"><strong>Estimate</strong><span><strong>{{ $quote->estimateLabel() }}</strong></span></div>
                <div class="detail-item"><strong>Requested</strong><span>{{ $quote->created_at->format('d M Y, H:i') }}</span></div>
            </div>
            @if(!empty($quote->answers['message']))
            <div class="detail-message"><strong>Message:</strong><p>{{ $quote->answers['message'] }}</p></div>
            @endif
            <div class="detail-actions">
                @if($quote->contact)<a href="{{ route('admin.contacts.show', $quote->contact->id) }}" class="btn btn-secondary"><i class="fas fa-user"></i> View Lead</a>@endif
                @if($quote->chat_session_id)<a href="{{ route('admin.inbox.show', $quote->chat_session_id) }}" class="btn btn-secondary"><i class="fas fa-comments"></i> View Chat</a>@endif
            </div>
        </div>
    </div>

    <div class="crm-col">
        <div class="admin-form-card">
            <h3 class="section-heading">Update</h3>
            <form action="{{ route('admin.quotes.status', $quote->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        @foreach($statuses as $k => $v)
                        <option value="{{ $k }}" {{ $quote->status===$k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Admin notes</label>
                    <textarea name="admin_notes" class="form-control" rows="4">{{ $quote->admin_notes }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
            </form>
        </div>
    </div>
</div>
@endsection
