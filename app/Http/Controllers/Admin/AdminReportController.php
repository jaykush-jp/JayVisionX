<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\Contact;
use App\Models\QuoteRequest;
use Illuminate\Support\Carbon;

class AdminReportController extends Controller
{
    public function index()
    {
        // Conversations
        $chatTotal   = ChatSession::count();
        $chatByStatus = ChatSession::selectRaw('status, COUNT(*) c')->groupBy('status')->pluck('c', 'status');
        $chatHuman   = ChatSession::where('is_ai_active', false)->count();
        $chatToLead  = ChatSession::whereNotNull('lead_id')->count();

        // Leads & conversion
        $leadsTotal  = Contact::count();
        $leadsBySource = Contact::selectRaw('source, COUNT(*) c')->groupBy('source')->pluck('c', 'source');
        $won         = Contact::where('status', 'won')->count();
        $conversion  = $leadsTotal > 0 ? round($won / $leadsTotal * 100, 1) : 0;
        $chatLeadConv = $chatTotal > 0 ? round($chatToLead / $chatTotal * 100, 1) : 0;

        // Most-requested services (from lead service_interested)
        $topServices = Contact::whereNotNull('service_interested')
            ->selectRaw('service_interested, COUNT(*) c')->groupBy('service_interested')
            ->orderByDesc('c')->take(8)->pluck('c', 'service_interested');

        // Quote requests by project type
        $quotesByType = QuoteRequest::selectRaw('project_type, COUNT(*) c')->groupBy('project_type')->pluck('c', 'project_type');
        $quotesTotal  = QuoteRequest::count();

        // Messages trend (14 days)
        $trend = [];
        for ($i = 13; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $trend[$day->format('d M')] = ChatMessage::whereDate('created_at', $day)->count();
        }
        $trendMax = max(1, max($trend ?: [0]));

        // AI usage proxy (assistant messages) + unanswered (fallback) proxy
        $aiMessages  = ChatMessage::where('role', 'assistant')->count();
        $unanswered  = ChatMessage::where('role', 'assistant')->whereJsonContains('meta->mode', 'fallback')->count();

        return view('admin.reports.index', compact(
            'chatTotal', 'chatByStatus', 'chatHuman', 'chatToLead',
            'leadsTotal', 'leadsBySource', 'won', 'conversion', 'chatLeadConv',
            'topServices', 'quotesByType', 'quotesTotal',
            'trend', 'trendMax', 'aiMessages', 'unanswered'
        ));
    }
}
