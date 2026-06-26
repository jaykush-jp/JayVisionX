@extends('admin.layouts.app')
@section('title','Manage Testimonials')
@section('content')
<div class="page-header-row">
    <h2>Testimonials <span class="count-badge">{{ $testimonials->count() }}</span></h2>
    <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Testimonial</a>
</div>
<div class="admin-table-wrapper">
    <table class="admin-table">
        <thead><tr><th>#</th><th>Photo</th><th>Client</th><th>Company</th><th>Rating</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
            @forelse($testimonials as $t)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    @if($t->photo)
                    <img src="{{ asset('storage/'.$t->photo) }}" style="width:40px;height:40px;border-radius:50%;object-fit:cover">
                    @else
                    <div style="width:40px;height:40px;border-radius:50%;background:var(--primary);color:white;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1rem">{{ strtoupper(substr($t->client_name,0,1)) }}</div>
                    @endif
                </td>
                <td><strong>{{ $t->client_name }}</strong><br><small class="text-muted">{{ $t->designation }}</small></td>
                <td>{{ $t->company ?? '-' }}</td>
                <td>
                    @for($i=1;$i<=5;$i++)<i class="fas fa-star" style="color:{{ $i<=$t->rating ? '#FFB800':'#DDE4F0' }};font-size:0.85rem"></i>@endfor
                </td>
                <td><span class="badge {{ $t->is_active ? 'badge-yes':'badge-no' }}">{{ $t->is_active ? 'Active':'Inactive' }}</span></td>
                <td class="actions-cell">
                    <a href="{{ route('admin.testimonials.edit',$t->id) }}" class="btn-sm btn-edit"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.testimonials.destroy',$t->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button type="submit" class="btn-sm btn-delete"><i class="fas fa-trash"></i></button></form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center">No testimonials yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
