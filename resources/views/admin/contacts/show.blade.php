@extends('admin.layouts.app')
@section('title', 'Enquiry Details')
@section('content')
<div class="page-header-row">
    <h2>Enquiry from {{ $contact->name }}</h2>
    <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>
<div class="detail-card">
    <div class="detail-grid">
        <div class="detail-item"><strong>Name</strong><span>{{ $contact->name }}</span></div>
        <div class="detail-item"><strong>Email</strong><span><a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></span></div>
        <div class="detail-item"><strong>Phone</strong><span>{{ $contact->phone ?? 'Not provided' }}</span></div>
        <div class="detail-item"><strong>Service Interested</strong><span>{{ $contact->service_interested ?? 'Not specified' }}</span></div>
        <div class="detail-item"><strong>Budget</strong><span>{{ $contact->budget ?? 'Not specified' }}</span></div>
        <div class="detail-item"><strong>Date</strong><span>{{ $contact->created_at->format('F d, Y H:i A') }}</span></div>
        <div class="detail-item"><strong>IP Address</strong><span>{{ $contact->ip_address ?? '-' }}</span></div>
        <div class="detail-item"><strong>Status</strong><span>{{ $contact->is_read ? 'Read' : 'New' }}</span></div>
    </div>
    <div class="detail-message">
        <strong>Message:</strong>
        <p>{{ $contact->message ?? 'No message provided.' }}</p>
    </div>
    <div class="detail-actions">
        <a href="mailto:{{ $contact->email }}?subject=Re: Your Enquiry at JayVisionX" class="btn btn-primary"><i class="fas fa-reply"></i> Reply via Email</a>
        @if($contact->phone)
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contact->phone) }}" target="_blank" class="btn btn-whatsapp-lg"><i class="fab fa-whatsapp"></i> WhatsApp</a>
        <a href="tel:{{ $contact->phone }}" class="btn btn-secondary"><i class="fas fa-phone"></i> Call</a>
        @endif
    </div>
</div>
@endsection
