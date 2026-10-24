<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\LeadReplyMail;
use App\Models\Contact;
use App\Models\EmailTemplate;
use App\Models\LeadEmail;
use App\Models\LeadNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Leads / Customer CRM. Backed by the `contacts` table; route names stay
 * `admin.contacts.*` for compatibility, but the UI is a full pipeline CRM.
 */
class AdminContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::query();

        if ($search = trim((string) $request->input('q'))) {
            $query->where(function ($w) use ($search) {
                $w->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // `customers` view locks the filter to won deals.
        $status = $request->routeIs('admin.customers')
            ? 'won'
            : $request->input('status');

        if ($status && array_key_exists($status, Contact::STATUSES)) {
            $query->where('status', $status);
        }

        if ($source = $request->input('source')) {
            $query->where('source', $source);
        }

        if ($from = $request->input('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $leads = $query->latest()->paginate(20)->withQueryString();

        $counts = Contact::selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status');
        $stats = [
            'total'       => Contact::count(),
            'by_status'   => $counts,
            'due'         => Contact::dueFollowUps()->count(),
            'this_week'   => Contact::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        $isCustomers = $request->routeIs('admin.customers');

        return view('admin.leads.index', compact('leads', 'stats', 'status', 'isCustomers'));
    }

    public function show($id)
    {
        $lead = Contact::with(['notes', 'emails'])->findOrFail($id);
        $lead->update(['is_read' => true]);
        $templates = EmailTemplate::orderBy('name')->get();

        return view('admin.leads.show', compact('lead', 'templates'));
    }

    /** Manually add a lead from the admin. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:191',
            'email'              => 'required|email|max:191',
            'phone'              => 'nullable|string|max:40',
            'service_interested' => 'nullable|string|max:191',
            'budget'             => 'nullable|string|max:191',
            'value'              => 'nullable|numeric',
            'message'            => 'nullable|string',
        ]);
        $data['source'] = 'manual';
        $data['status'] = 'new';
        $data['is_read'] = true;

        $lead = Contact::create($data);
        $this->log($lead, 'note', 'Lead created manually.');

        return redirect()->route('admin.contacts.show', $lead->id)->with('success', 'Lead added.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:' . implode(',', array_keys(Contact::STATUSES))]);
        $lead = Contact::findOrFail($id);
        $old = $lead->statusLabel();
        $lead->update(['status' => $request->status]);

        $this->log($lead, 'status_change', "Status changed: {$old} → {$lead->statusLabel()}");

        return back()->with('success', 'Status updated.');
    }

    public function storeNote(Request $request, $id)
    {
        $request->validate([
            'body' => 'required|string',
            'type' => 'nullable|in:note,call',
        ]);
        $lead = Contact::findOrFail($id);

        if ($request->type === 'call') {
            $lead->update(['last_contacted_at' => now()]);
        }

        $this->log($lead, $request->type ?: 'note', $request->body);

        return back()->with('success', 'Note added.');
    }

    public function setFollowUp(Request $request, $id)
    {
        $request->validate(['follow_up_at' => 'nullable|date']);
        $lead = Contact::findOrFail($id);
        $lead->update(['follow_up_at' => $request->follow_up_at]);

        $when = $request->follow_up_at ? \Illuminate\Support\Carbon::parse($request->follow_up_at)->format('d M Y, h:i A') : null;
        $this->log($lead, 'note', $when ? "Follow-up set for {$when}." : 'Follow-up cleared.');

        return back()->with('success', 'Follow-up updated.');
    }

    public function reply(Request $request, $id)
    {
        $data = $request->validate([
            'subject' => 'required|string|max:191',
            'body'    => 'required|string',
        ]);
        $lead = Contact::findOrFail($id);

        $status = 'sent';
        try {
            Mail::to($lead->email)->send(new LeadReplyMail($data['subject'], $data['body'], $lead->name));
        } catch (\Throwable $e) {
            $status = 'failed';
            Log::warning('Lead reply email failed: ' . $e->getMessage());
        }

        LeadEmail::create([
            'contact_id' => $lead->id,
            'subject'    => $data['subject'],
            'body'       => $data['body'],
            'sent_by'    => session('admin_email'),
            'status'     => $status,
        ]);

        $lead->update(['last_contacted_at' => now()]);
        if ($lead->status === 'new') {
            $lead->update(['status' => 'contacted']);
        }
        $this->log($lead, 'email', "Email sent: {$data['subject']}");

        return back()->with(
            $status === 'sent' ? 'success' : 'error',
            $status === 'sent' ? 'Reply sent to ' . $lead->email : 'Reply could not be sent (check mail settings). It has been logged.'
        );
    }

    public function destroy($id)
    {
        Contact::findOrFail($id)->delete();
        return redirect()->route('admin.contacts.index')->with('success', 'Lead deleted.');
    }

    public function markRead($id)
    {
        Contact::findOrFail($id)->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    /** Stream all (filtered) leads as CSV without a package. */
    public function export(Request $request): StreamedResponse
    {
        $filename = 'leads-' . now()->format('Y-m-d') . '.csv';
        $query = Contact::query();
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Name', 'Email', 'Phone', 'Service', 'Budget', 'Value', 'Status', 'Source', 'Follow-up', 'Created']);
            $query->latest()->chunk(200, function ($rows) use ($out) {
                foreach ($rows as $r) {
                    fputcsv($out, [
                        $r->name, $r->email, $r->phone, $r->service_interested, $r->budget,
                        $r->value, $r->statusLabel(), $r->source,
                        optional($r->follow_up_at)->format('Y-m-d H:i'),
                        $r->created_at->format('Y-m-d H:i'),
                    ]);
                }
            });
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function log(Contact $lead, string $type, string $body): void
    {
        LeadNote::create([
            'contact_id' => $lead->id,
            'type'       => $type,
            'body'       => $body,
            'created_by' => session('admin_email', 'Administrator'),
        ]);
    }
}
