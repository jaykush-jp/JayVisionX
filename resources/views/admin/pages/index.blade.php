@extends('admin.layouts.app')
@section('title','Pages & SEO')
@section('content')
<div class="page-header-row">
    <h2>Pages & SEO Management</h2>
    <a href="{{ route('admin.pages.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Page</a>
</div>
<div class="pages-grid">
    @forelse($pages as $page)
    <div class="page-seo-card">
        <h4>{{ ucfirst(str_replace('-',' ',$page->page_name)) }}</h4>
        <p>{{ Str::limit($page->meta_title, 60) ?: 'No meta title set' }}</p>
        <div class="actions-cell">
            <a href="{{ route('admin.pages.edit',$page->id) }}" class="btn-sm btn-edit"><i class="fas fa-edit"></i> Edit SEO</a>
        </div>
    </div>
    @empty
    <p class="text-muted">No pages configured yet.</p>
    @endforelse
</div>
@endsection
