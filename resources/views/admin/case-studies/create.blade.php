@extends('admin.layouts.app')
@section('title', isset($caseStudy) ? 'Edit Work' : 'Add Work')
@section('content')
<div class="page-header-row">
    <h2>{{ isset($caseStudy) ? 'Edit Work' : 'Add New Work' }}</h2>
    <a href="{{ route('admin.case-studies.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>
<div class="admin-form-card">
    @if($errors->any())
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <ul style="margin:0;padding-left:18px">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif
    <form action="{{ isset($caseStudy) ? route('admin.case-studies.update',$caseStudy->id) : route('admin.case-studies.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($caseStudy)) @method('PUT') @endif

        <div class="form-row-2">
            <div class="form-group">
                <label>Title *</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $caseStudy->title ?? '') }}" required>
            </div>
            <div class="form-group">
                <label>Client</label>
                <input type="text" name="client" class="form-control" value="{{ old('client', $caseStudy->client ?? '') }}" placeholder="Client / company name">
            </div>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label>Category / Industry</label>
                <input type="text" name="industry" class="form-control" value="{{ old('industry', $caseStudy->industry ?? '') }}" placeholder="e.g. Website, Real Estate, Healthcare">
            </div>
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $caseStudy->sort_order ?? 0) }}">
            </div>
        </div>

        <div class="form-group">
            <label>Challenge <small class="text-muted">— client ki problem</small></label>
            <textarea name="challenge" class="form-control" rows="3" placeholder="Client kis problem ka saamna kar raha tha?">{{ old('challenge', $caseStudy->challenge ?? '') }}</textarea>
        </div>

        <div class="form-group">
            <label>Solution <small class="text-muted">— aapne kya kiya</small></label>
            <textarea name="solution" class="form-control" rows="3" placeholder="Aapne kaunsi strategy / solution di?">{{ old('solution', $caseStudy->solution ?? '') }}</textarea>
        </div>

        <div class="form-group">
            <label>Result <small class="text-muted">— short summary (Our Work card par dikhta hai)</small></label>
            <textarea name="result" class="form-control" rows="2" placeholder="Ek-do line ka measurable result.">{{ old('result', $caseStudy->result ?? '') }}</textarea>
        </div>

        <div class="form-group">
            <label>Key Metrics <small class="text-muted">— bade numbers jo result dikhate hain (e.g. 3x &rarr; More leads)</small></label>
            @php
                $existingStats = $caseStudy->stats ?? [];
                if (is_string($existingStats)) { $existingStats = json_decode($existingStats, true); }
                if (! is_array($existingStats)) { $existingStats = []; }
                $existingStats = old('stats', $existingStats);
            @endphp
            <table class="admin-table" id="statsTable">
                <thead><tr><th style="width:35%">Value</th><th>Label</th><th style="width:50px"></th></tr></thead>
                <tbody id="statsRows">
                    @foreach($existingStats as $i => $st)
                    <tr class="stat-row">
                        <td><input type="text" name="stats[{{ $i }}][value]" class="form-control" value="{{ is_array($st) ? ($st['value'] ?? '') : '' }}" placeholder="3x"></td>
                        <td><input type="text" name="stats[{{ $i }}][label]" class="form-control" value="{{ is_array($st) ? ($st['label'] ?? '') : '' }}" placeholder="More qualified leads"></td>
                        <td><button type="button" class="btn-sm btn-delete js-remove-stat"><i class="fas fa-trash"></i></button></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="button" class="btn btn-secondary" id="addStat" style="margin-top:8px"><i class="fas fa-plus"></i> Add Metric</button>
            <small class="text-muted" style="display:block;margin-top:6px">3–4 metrics best lagte hain. Khaali rows apne-aap ignore ho jaati hain.</small>
        </div>

        <div class="form-group">
            <label>Full Details <small class="text-muted">(optional, HTML allowed)</small></label>
            <textarea name="content" class="form-control" rows="6" placeholder="Is kaam ke baare me detail (optional).">{{ old('content', $caseStudy->content ?? '') }}</textarea>
        </div>

        <div class="form-group">
            <label>Thumbnail Image</label>
            <input type="file" name="thumbnail" class="form-control @error('thumbnail') is-invalid @enderror" accept="image/*">
            <small class="text-muted">Max 3 MB. Formats: jpg, png, webp, gif.</small>
            @error('thumbnail')<span class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
            @if(isset($caseStudy) && $caseStudy->thumbnail)
            <img src="{{ $caseStudy->thumbnail_url }}" class="preview-img" alt="thumb">
            @endif
        </div>

        <div class="form-check-group">
            <label class="form-check-label">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $caseStudy->is_active ?? true) ? 'checked' : '' }}> Active (show on website)
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> {{ isset($caseStudy) ? 'Update' : 'Save' }} Work</button>
            <a href="{{ route('admin.case-studies.index') }}" class="btn btn-secondary btn-lg">Cancel</a>
        </div>
    </form>
</div>

<template id="statRowTpl">
    <tr class="stat-row">
        <td><input type="text" name="stats[__I__][value]" class="form-control" placeholder="3x"></td>
        <td><input type="text" name="stats[__I__][label]" class="form-control" placeholder="More qualified leads"></td>
        <td><button type="button" class="btn-sm btn-delete js-remove-stat"><i class="fas fa-trash"></i></button></td>
    </tr>
</template>

<script>
(function () {
    var rows = document.getElementById('statsRows');
    var tpl  = document.getElementById('statRowTpl');
    var next = {{ count($existingStats) }};

    document.getElementById('addStat').addEventListener('click', function () {
        var html = tpl.innerHTML.replace(/__I__/g, next++);
        var tmp = document.createElement('tbody');
        tmp.innerHTML = html.trim();
        rows.appendChild(tmp.firstChild);
    });

    rows.addEventListener('click', function (e) {
        var btn = e.target.closest('.js-remove-stat');
        if (btn) { btn.closest('tr').remove(); }
    });
})();
</script>
@endsection
