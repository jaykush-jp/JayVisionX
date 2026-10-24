@extends('admin.layouts.app')
@section('title', 'Social Posts')
@section('content')
<div class="page-header-row">
    <h2>Social Posts</h2>
    <a href="{{ route('admin.integrations.index') }}" class="btn btn-secondary"><i class="fas fa-plug"></i> Integrations</a>
</div>

<div class="crm-detail-grid">
    <!-- Composer -->
    <div class="crm-col">
        <div class="admin-form-card">
            <h3 class="section-heading">Compose Post</h3>
            <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Content *</label>
                    <textarea name="body" class="form-control" rows="5" placeholder="What do you want to share?" required></textarea>
                </div>
                <div class="form-group">
                    <label>Image (optional)</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <small class="text-muted">Instagram requires an image.</small>
                </div>
                <div class="form-group">
                    <label>Publish to</label>
                    <div class="platform-picker">
                        @foreach(\App\Models\SocialPost::PLATFORMS as $key => $label)
                        <label class="platform-chip {{ ($connections[$key] ?? false) ? '' : 'disabled' }}">
                            <input type="checkbox" name="platforms[]" value="{{ $key }}" {{ ($connections[$key] ?? false) ? '' : 'disabled' }}>
                            <span>{{ $label }}</span>
                            @unless($connections[$key] ?? false)<small>(not connected)</small>@endunless
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="form-group">
                    <label>Schedule for (optional)</label>
                    <input type="datetime-local" name="scheduled_at" class="form-control">
                </div>
                <div class="form-actions">
                    <button type="submit" name="action" value="publish" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Publish now</button>
                    <button type="submit" name="action" value="schedule" class="btn btn-secondary"><i class="fas fa-clock"></i> Schedule</button>
                    <button type="submit" name="action" value="draft" class="btn btn-secondary">Save draft</button>
                </div>
                @if(!collect($connections)->contains(true))
                <p class="text-muted" style="margin-top:12px"><i class="fas fa-info-circle"></i> No channels connected yet — posts will be saved as drafts. Connect Google / Meta in Integrations to publish.</p>
                @endif
            </form>
        </div>
    </div>

    <!-- History -->
    <div class="crm-col">
        <div class="admin-form-card">
            <h3 class="section-heading">Recent Posts</h3>
            @forelse($posts as $p)
            <div class="social-post-item status-{{ $p->status }}">
                <div class="social-post-top">
                    <span class="lead-badge lead-badge-{{ ['draft'=>'new','scheduled'=>'qualified','published'=>'won','failed'=>'lost'][$p->status] ?? 'new' }}">{{ ucfirst($p->status) }}</span>
                    <span class="social-post-platforms">
                        @foreach($p->platforms ?? [] as $pl)<span class="platform-tag">{{ \App\Models\SocialPost::PLATFORMS[$pl] ?? $pl }}</span>@endforeach
                    </span>
                </div>
                <p>{{ Str::limit($p->body, 160) }}</p>
                @if($p->scheduled_at)<small class="text-muted">Scheduled: {{ $p->scheduled_at->format('d M Y, h:i A') }}</small>@endif
                @if($p->error)<small class="text-danger d-block">{{ $p->error }}</small>@endif
                <div class="social-post-actions">
                    @if(in_array($p->status, ['draft','scheduled','failed']))
                    <form action="{{ route('admin.posts.publish', $p->id) }}" method="POST" style="display:inline">
                        @csrf
                        <button type="submit" class="btn-sm btn-view" title="Publish now"><i class="fas fa-paper-plane"></i></button>
                    </form>
                    @endif
                    <form action="{{ route('admin.posts.destroy', $p->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete post?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-sm btn-delete"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
            @empty
            <p class="text-muted">No posts yet.</p>
            @endforelse
            <div class="pagination-wrapper">{{ $posts->links() }}</div>
        </div>
    </div>
</div>
@endsection
