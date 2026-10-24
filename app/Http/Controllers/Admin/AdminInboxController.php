<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\Contact;
use Illuminate\Http\Request;

/**
 * Live Inbox — view and work the chatbot conversations persisted by
 * ChatbotController (ChatSession + ChatMessage).
 */
class AdminInboxController extends Controller
{
    public const STATUSES = [
        'open'     => 'Open',
        'pending'  => 'Pending',
        'resolved' => 'Resolved',
        'closed'   => 'Closed',
    ];

    public function index(Request $request)
    {
        $query = ChatSession::query()->withCount('messages');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($search = trim((string) $request->input('q'))) {
            $query->where(function ($w) use ($search) {
                $w->where('visitor_name', 'like', "%{$search}%")
                    ->orWhere('visitor_email', 'like', "%{$search}%")
                    ->orWhere('visitor_phone', 'like', "%{$search}%");
            });
        }
        if ($request->input('filter') === 'human') {
            $query->where('is_ai_active', false);
        }

        $sessions = $query->orderByDesc('last_message_at')->orderByDesc('created_at')
            ->paginate(20)->withQueryString();

        $stats = [
            'total'     => ChatSession::count(),
            'open'      => ChatSession::where('status', 'open')->count(),
            'human'     => ChatSession::where('is_ai_active', false)->count(),
            'withLead'  => ChatSession::whereNotNull('lead_id')->count(),
        ];

        return view('admin.inbox.index', compact('sessions', 'stats'));
    }

    public function show($id)
    {
        $session = ChatSession::with(['messages', 'lead'])->findOrFail($id);
        return view('admin.inbox.show', compact('session'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:' . implode(',', array_keys(self::STATUSES))]);
        ChatSession::findOrFail($id)->update(['status' => $request->status]);
        return back()->with('success', 'Status updated.');
    }

    /** Toggle human takeover (pauses AI auto-replies) or return control to AI. */
    public function toggleHuman($id)
    {
        $session = ChatSession::findOrFail($id);
        $takeOver = $session->is_ai_active; // if AI active, we're taking over
        $session->update([
            'is_ai_active' => ! $session->is_ai_active,
            'status'       => $takeOver ? 'pending' : $session->status,
        ]);

        ChatMessage::create([
            'chat_session_id' => $session->id,
            'role'            => 'system',
            'content'         => $takeOver
                ? 'Human agent took over the conversation.'
                : 'Conversation returned to the AI assistant.',
            'meta'            => ['by' => session('admin_email')],
        ]);

        return back()->with('success', $takeOver ? 'You have taken over this chat.' : 'Returned to AI.');
    }

    public function assign(Request $request, $id)
    {
        $request->validate(['assigned_to' => 'nullable|string|max:191']);
        ChatSession::findOrFail($id)->update(['assigned_to' => $request->assigned_to]);
        return back()->with('success', 'Assignment updated.');
    }

    /** Post an internal note into the transcript (role=system, not shown to visitor). */
    public function note(Request $request, $id)
    {
        $request->validate(['body' => 'required|string']);
        $session = ChatSession::findOrFail($id);
        ChatMessage::create([
            'chat_session_id' => $session->id,
            'role'            => 'system',
            'content'         => $request->body,
            'meta'            => ['internal' => true, 'by' => session('admin_email')],
        ]);
        return back()->with('success', 'Note added.');
    }

    /** Create a CRM lead from the conversation's visitor details and link it. */
    public function convertToLead(Request $request, $id)
    {
        $session = ChatSession::findOrFail($id);

        if ($session->lead_id) {
            return back()->with('error', 'This conversation is already linked to a lead.');
        }

        $data = $request->validate([
            'name'  => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'phone' => 'nullable|string|max:40',
            'message' => 'nullable|string',
        ]);

        $lead = Contact::create([
            'name'    => $data['name'],
            'email'   => $data['email'],
            'phone'   => $data['phone'] ?? $session->visitor_phone,
            'message' => $data['message'] ?? 'Created from chat conversation #' . $session->id,
            'source'  => 'chatbot',
            'status'  => 'new',
            'is_read' => true,
            'ip_address' => $session->ip_address,
        ]);

        $session->update([
            'lead_id'       => $lead->id,
            'visitor_name'  => $data['name'],
            'visitor_email' => $data['email'],
            'visitor_phone' => $lead->phone,
        ]);

        return redirect()->route('admin.contacts.show', $lead->id)
            ->with('success', 'Lead created from conversation.');
    }
}
