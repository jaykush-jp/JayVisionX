@extends('admin.layouts.app')
@section('title','Technologies')
@section('content')
<div class="page-header-row">
    <h2>Technologies We Use</h2>
    <a href="{{ route('home') }}#technologies" target="_blank" class="btn btn-secondary"><i class="fas fa-external-link-alt"></i> View on site</a>
</div>

<div class="admin-form-card">
    @if($errors->any())
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <ul style="margin:0;padding-left:18px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <p style="color:#6B7FA3;margin-bottom:18px">
        Yeh list home page ke "Technologies We Use" section me dikhti hai. Naya item add karne ke liye
        <strong>Add Technology</strong> dabayein. <br>
        Icon ke liye <a href="https://fontawesome.com/search?o=r&m=free" target="_blank">Font Awesome</a> ka class use karein
        (jaise <code>fab fa-react</code>, <code>fab fa-python</code>, <code>fas fa-database</code>). Color me hex code (jaise <code>#61DAFB</code>) dein.
    </p>

    <form action="{{ route('admin.technologies.update') }}" method="POST">
        @csrf

        <table class="admin-table" id="techTable">
            <thead>
                <tr>
                    <th style="width:60px">Preview</th>
                    <th>Name</th>
                    <th>Icon class</th>
                    <th style="width:150px">Color</th>
                    <th style="width:60px"></th>
                </tr>
            </thead>
            <tbody id="techRows">
                @forelse($technologies as $tech)
                <tr class="tech-row">
                    <td class="tech-preview"><i class="{{ $tech['icon'] ?? 'fas fa-code' }}" style="font-size:22px;color:{{ $tech['color'] ?? '#1E5EFF' }}"></i></td>
                    <td><input type="text" name="name[]" class="form-control js-name" value="{{ $tech['name'] ?? '' }}" placeholder="e.g. React.js"></td>
                    <td><input type="text" name="icon[]" class="form-control js-icon" value="{{ $tech['icon'] ?? '' }}" placeholder="fab fa-react"></td>
                    <td><input type="text" name="color[]" class="form-control js-color" value="{{ $tech['color'] ?? '' }}" placeholder="#61DAFB"></td>
                    <td><button type="button" class="btn-sm btn-delete js-remove"><i class="fas fa-trash"></i></button></td>
                </tr>
                @empty
                @endforelse
            </tbody>
        </table>

        <div style="margin:16px 0">
            <button type="button" class="btn btn-secondary" id="addTech"><i class="fas fa-plus"></i> Add Technology</button>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Save Technologies</button>
        </div>
    </form>
</div>

<template id="techRowTpl">
    <tr class="tech-row">
        <td class="tech-preview"><i class="fas fa-code" style="font-size:22px;color:#1E5EFF"></i></td>
        <td><input type="text" name="name[]" class="form-control js-name" placeholder="e.g. React.js"></td>
        <td><input type="text" name="icon[]" class="form-control js-icon" placeholder="fab fa-react"></td>
        <td><input type="text" name="color[]" class="form-control js-color" placeholder="#61DAFB"></td>
        <td><button type="button" class="btn-sm btn-delete js-remove"><i class="fas fa-trash"></i></button></td>
    </tr>
</template>

<script>
(function () {
    var rows = document.getElementById('techRows');
    var tpl  = document.getElementById('techRowTpl');

    document.getElementById('addTech').addEventListener('click', function () {
        rows.appendChild(tpl.content.cloneNode(true));
    });

    rows.addEventListener('click', function (e) {
        var btn = e.target.closest('.js-remove');
        if (btn) { btn.closest('tr').remove(); }
    });

    // Live icon/color preview
    rows.addEventListener('input', function (e) {
        var row = e.target.closest('tr');
        if (!row) return;
        var icon  = row.querySelector('.js-icon');
        var color = row.querySelector('.js-color');
        var prev  = row.querySelector('.tech-preview i');
        if (prev) {
            prev.className = (icon && icon.value.trim()) ? icon.value.trim() : 'fas fa-code';
            prev.style.color = (color && color.value.trim()) ? color.value.trim() : '#1E5EFF';
        }
    });
})();
</script>
@endsection
