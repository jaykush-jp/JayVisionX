@extends('admin.layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="dashboard-grid">
    <!-- Stats -->
    <div class="stats-row">
        <div class="stat-card blue">
            <div class="stat-icon"><i class="fas fa-cogs"></i></div>
            <div class="stat-info"><span class="stat-num">{{ $stats['services'] }}</span><span class="stat-label">Services</span></div>
            <a href="{{ route('admin.services.index') }}" class="stat-link">Manage <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="stat-card orange">
            <div class="stat-icon"><i class="fas fa-briefcase"></i></div>
            <div class="stat-info"><span class="stat-num">{{ $stats['projects'] }}</span><span class="stat-label">Projects</span></div>
            <a href="{{ route('admin.projects.index') }}" class="stat-link">Manage <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="stat-card green">
            <div class="stat-icon"><i class="fas fa-blog"></i></div>
            <div class="stat-info"><span class="stat-num">{{ $stats['blogs'] }}</span><span class="stat-label">Blog Posts</span></div>
            <a href="{{ route('admin.blogs.index') }}" class="stat-link">Manage <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="stat-card red">
            <div class="stat-icon"><i class="fas fa-envelope"></i></div>
            <div class="stat-info">
                <span class="stat-num">{{ $stats['contacts'] }}</span>
                <span class="stat-label">Enquiries
                    @if($stats['unread'] > 0)<span class="badge-unread">{{ $stats['unread'] }} new</span>@endif
                </span>
            </div>
            <a href="{{ route('admin.contacts.index') }}" class="stat-link">View <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="stat-card purple">
            <div class="stat-icon"><i class="fas fa-star"></i></div>
            <div class="stat-info"><span class="stat-num">{{ $stats['testimonials'] }}</span><span class="stat-label">Testimonials</span></div>
            <a href="{{ route('admin.testimonials.index') }}" class="stat-link">Manage <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="stat-card teal">
            <div class="stat-icon"><i class="fas fa-paper-plane"></i></div>
            <div class="stat-info"><span class="stat-num">{{ $stats['newsletters'] }}</span><span class="stat-label">Subscribers</span></div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="dashboard-section">
        <h3 class="section-heading">Quick Actions</h3>
        <div class="quick-actions">
            <a href="{{ route('admin.services.create') }}" class="quick-action-btn"><i class="fas fa-plus"></i> Add Service</a>
            <a href="{{ route('admin.projects.create') }}" class="quick-action-btn"><i class="fas fa-plus"></i> Add Project</a>
            <a href="{{ route('admin.blogs.create') }}" class="quick-action-btn"><i class="fas fa-plus"></i> Add Blog Post</a>
            <a href="{{ route('admin.testimonials.create') }}" class="quick-action-btn"><i class="fas fa-plus"></i> Add Testimonial</a>
            <a href="{{ route('admin.faqs.create') }}" class="quick-action-btn"><i class="fas fa-plus"></i> Add FAQ</a>
            <a href="{{ route('admin.settings.index') }}" class="quick-action-btn secondary"><i class="fas fa-cog"></i> Website Settings</a>
        </div>
    </div>

    <!-- Recent Enquiries -->
    <div class="dashboard-section">
        <div class="section-heading-row">
            <h3 class="section-heading">Recent Enquiries</h3>
            <a href="{{ route('admin.contacts.index') }}" class="view-all-link">View All</a>
        </div>
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentContacts as $contact)
                    <tr class="{{ !$contact->is_read ? 'row-unread' : '' }}">
                        <td>{{ $contact->name }}</td>
                        <td>{{ $contact->email }}</td>
                        <td>{{ $contact->phone ?? '-' }}</td>
                        <td>{{ $contact->service_interested ?? '-' }}</td>
                        <td>{{ $contact->created_at->format('M d, Y') }}</td>
                        <td>
                            @if(!$contact->is_read)
                            <span class="badge badge-new">New</span>
                            @else
                            <span class="badge badge-read">Read</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.contacts.show', $contact->id) }}" class="btn-sm btn-view">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center">No enquiries yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
