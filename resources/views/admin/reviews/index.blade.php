@extends('admin.layouts.app')
@section('title', 'Google Reviews')
@section('content')
<div class="page-header-row">
    <h2>Google Reviews</h2>
    <a href="{{ route('admin.integrations.index') }}" class="btn btn-secondary"><i class="fas fa-plug"></i> Integrations</a>
</div>

@if(!$connected)
<div class="empty-state">
    <i class="fas fa-star-half-alt empty-icon"></i>
    <h3>Connect Google Business Profile</h3>
    <p>Once connected, your Google reviews appear here and you can reply to each one without leaving the dashboard.</p>
    <a href="{{ route('admin.integrations.index') }}" class="btn btn-primary"><i class="fas fa-link"></i> Go to Integrations</a>
</div>
@elseif(empty($reviews))
<div class="empty-state">
    <i class="fas fa-inbox empty-icon"></i>
    <h3>No reviews yet</h3>
    <p>When customers leave Google reviews, they will show up here.</p>
</div>
@else
<div class="review-list">
    @foreach($reviews as $r)
    <div class="review-card">
        <div class="review-head">
            <strong>{{ $r['author'] }}</strong>
            <span class="review-stars">
                @for($i = 0; $i < 5; $i++)<i class="fas fa-star {{ $i < $r['rating'] ? 'filled' : '' }}"></i>@endfor
            </span>
        </div>
        <p class="review-comment">{{ $r['comment'] ?: '(no comment)' }}</p>
        @if($r['reply'])
        <div class="review-reply"><strong>Your reply:</strong> {{ $r['reply'] }}</div>
        @else
        <form action="{{ route('admin.reviews.reply', $r['id']) }}" method="POST" class="review-reply-form">
            @csrf
            <textarea name="comment" class="form-control" rows="2" placeholder="Write a public reply..." required></textarea>
            <button type="submit" class="btn btn-xs btn-primary"><i class="fas fa-reply"></i> Reply</button>
        </form>
        @endif
    </div>
    @endforeach
</div>
@endif
@endsection
