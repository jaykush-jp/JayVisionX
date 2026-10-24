<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\KnowledgeArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminKnowledgeController extends Controller
{
    /** Categories the chatbot understands. */
    public const CATEGORIES = [
        'company_profile' => 'Company Profile',
        'services'        => 'Services',
        'pricing'         => 'Pricing',
        'faqs'            => 'FAQs',
        'portfolio'       => 'Portfolio',
        'policies'        => 'Policies',
        'process'         => 'Process',
        'contact'         => 'Contact Information',
    ];

    public function index(Request $request)
    {
        $query = KnowledgeArticle::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $query->where(function ($w) use ($request) {
                $w->where('title', 'like', '%' . $request->q . '%')
                  ->orWhere('content', 'like', '%' . $request->q . '%');
            });
        }

        $articles = $query->orderBy('sort_order')->orderByDesc('updated_at')->paginate(20)->withQueryString();

        return view('admin.knowledge.index', [
            'articles'   => $articles,
            'categories' => self::CATEGORIES,
        ]);
    }

    public function create()
    {
        return view('admin.knowledge.create', ['categories' => self::CATEGORIES]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['title']) . '-' . Str::random(5);
        $data['last_updated_by'] = session('admin_email');

        $article = KnowledgeArticle::create($data);
        AuditLog::record('created', 'knowledge_article', $article->id, ['title' => $article->title]);

        return redirect()->route('admin.knowledge.index')->with('success', 'Knowledge entry created.');
    }

    public function edit(KnowledgeArticle $knowledge)
    {
        return view('admin.knowledge.edit', [
            'article'    => $knowledge,
            'categories' => self::CATEGORIES,
        ]);
    }

    public function update(Request $request, KnowledgeArticle $knowledge)
    {
        $data = $this->validated($request);
        $data['last_updated_by'] = session('admin_email');

        $knowledge->update($data);
        AuditLog::record('updated', 'knowledge_article', $knowledge->id, ['title' => $knowledge->title]);

        return redirect()->route('admin.knowledge.index')->with('success', 'Knowledge entry updated.');
    }

    public function destroy(KnowledgeArticle $knowledge)
    {
        AuditLog::record('deleted', 'knowledge_article', $knowledge->id, ['title' => $knowledge->title]);
        $knowledge->delete();

        return redirect()->route('admin.knowledge.index')->with('success', 'Knowledge entry deleted.');
    }

    /** Quick publish / archive / unpublish from the list. */
    public function setStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:draft,published,archived']);
        $article = KnowledgeArticle::findOrFail($id);
        $article->update(['status' => $request->status, 'last_updated_by' => session('admin_email')]);
        AuditLog::record('status:' . $request->status, 'knowledge_article', $article->id);

        return back()->with('success', 'Status updated to ' . $request->status . '.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title'        => 'required|string|max:200',
            'category'     => 'required|in:' . implode(',', array_keys(self::CATEGORIES)),
            'content'      => 'nullable|string',
            'source_url'   => 'nullable|url|max:500',
            'status'       => 'required|in:draft,published,archived',
            'is_searchable' => 'nullable|boolean',
            'sort_order'   => 'nullable|integer',
        ]);

        $data['is_searchable'] = $request->boolean('is_searchable');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}
