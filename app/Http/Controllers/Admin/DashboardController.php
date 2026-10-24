<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Project;
use App\Models\Blog;
use App\Models\Contact;
use App\Models\Testimonial;
use App\Models\Newsletter;
use App\Models\ChatSession;
use App\Models\ChatMessage;

class DashboardController extends Controller
{
    public function index()
    {
        $byStatus = Contact::selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status');

        $stats = [
            'services'      => Service::count(),
            'projects'      => Project::count(),
            'blogs'         => Blog::count(),
            'contacts'      => Contact::count(),
            'unread'        => Contact::where('is_read', false)->count(),
            'testimonials'  => Testimonial::count(),
            'newsletters'   => Newsletter::count(),

            // CRM
            'leads_week'    => Contact::where('created_at', '>=', now()->subDays(7))->count(),
            'customers'     => $byStatus['won'] ?? 0,
            'due'           => Contact::dueFollowUps()->count(),
            'pipeline'      => collect(Contact::STATUSES)->map(fn ($label, $key) => [
                'label' => $label,
                'key'   => $key,
                'count' => $byStatus[$key] ?? 0,
            ])->values(),
        ];

        // AI support snapshot
        $stats['chats']          = ChatSession::count();
        $stats['chats_open']     = ChatSession::where('status', 'open')->count();
        $stats['chats_human']    = ChatSession::where('is_ai_active', false)->count();
        $stats['chats_leads']    = ChatSession::whereNotNull('lead_id')->count();
        $stats['chat_msgs_week'] = ChatMessage::where('created_at', '>=', now()->subDays(7))->count();

        $recentContacts = Contact::latest()->take(5)->get();
        $dueFollowUps   = Contact::dueFollowUps()->orderBy('follow_up_at')->take(5)->get();

        return view('admin.dashboard.index', compact('stats', 'recentContacts', 'dueFollowUps'));
    }
}
