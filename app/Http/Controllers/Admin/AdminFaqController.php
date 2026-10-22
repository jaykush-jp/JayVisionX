<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class AdminFaqController extends Controller
{
    public function index() { return view('admin.faqs.index', ['faqs' => Faq::orderBy('sort_order')->paginate(20)]); }
    public function create() { return view('admin.faqs.create'); }
    public function store(Request $request)
    {
        $request->validate(['question' => 'required', 'answer' => 'required']);
        Faq::create($request->except('_token'));
        return redirect()->route('admin.faqs.index')->with('success', 'FAQ added.');
    }
    public function edit(Faq $faq) { return view('admin.faqs.edit', compact('faq')); }
    public function update(Request $request, Faq $faq)
    {
        $faq->update($request->except(['_token', '_method']));
        return redirect()->route('admin.faqs.index')->with('success', 'FAQ updated.');
    }
    public function destroy(Faq $faq)
    {
        $faq->delete();
        return redirect()->route('admin.faqs.index')->with('success', 'FAQ deleted.');
    }
    public function show(Faq $faq) { return view('admin.faqs.show', compact('faq')); }
}
