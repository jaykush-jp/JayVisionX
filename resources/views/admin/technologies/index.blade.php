@extends('admin.layouts.app')
@section('title','Technologies')
@section('content')
<div class="page-header-row">
    <h2>Technologies We Use</h2>
    <a href="{{ route('home') }}#technologies" target="_blank" class="btn btn-secondary"><i class="fas fa-external-link-alt"></i> View on site</a>
</div>

<div class="admin-form-card">
    @if(session('success'))
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <ul style="margin:0;padding-left:18px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <p style="color:#6B7FA3;margin-bottom:18px">
        Yeh list home page ke "Technologies We Use" section me dikhti hai. Naya item add karne ke liye
        <strong>Add Technology</strong> dabayein. <br>
        Icon do tarah se de sakte hain: <strong>(1)</strong> <a href="https://fontawesome.com/search?o=r&m=free" target="_blank">Font Awesome</a>
        class (jaise <code>fab fa-react</code>), ya <strong>(2)</strong> apni <strong>image upload</strong> karein
        (Next.js jaise logos ke liye — image ho to wahi dikhegi). <strong>Active</strong> toggle se decide karein kaun si tech site par dikhe.
    </p>

    <form action="{{ route('admin.technologies.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <table class="admin-table" id="techTable">
            <thead>
                <tr>
                    <th style="width:60px">Preview</th>
                    <th style="width:70px">Active</th>
                    <th>Name</th>
                    <th>Icon class</th>
                    <th style="width:130px">Color</th>
                    <th style="width:200px">Image (optional)</th>
                    <th style="width:50px"></th>
                </tr>
            </thead>
            <tbody id="techRows">
                @foreach($technologies as $i => $tech)
                <tr class="tech-row">
                    <td class="tech-preview">
                        @if(!empty($tech['image']))
                        <img src="{{ asset('storage/'.$tech['image']) }}" alt="" style="width:26px;height:26px;object-fit:contain">
                        @else
                        <i class="{{ $tech['icon'] ?: 'fas fa-code' }}" style="font-size:22px;color:{{ $tech['color'] ?: '#1E5EFF' }}"></i>
                        @endif
                    </td>
                    <td style="text-align:center">
                        <input type="checkbox" name="active[{{ $i }}]" value="1" {{ ($tech['active'] ?? true) ? 'checked' : '' }}>
                    </td>
                    <td><input type="text" name="name[{{ $i }}]" class="form-control js-name" value="{{ $tech['name'] ?? '' }}" placeholder="e.g. Next.js"></td>
                    <td><input type="text" name="icon[{{ $i }}]" class="form-control js-icon" value="{{ $tech['icon'] ?? '' }}" placeholder="fab fa-react"></td>
                    <td><input type="text" name="color[{{ $i }}]" class="form-control js-color" value="{{ $tech['color'] ?? '' }}" placeholder="#61DAFB"></td>
                    <td>
                        <input type="hidden" name="existing_image[{{ $i }}]" value="{{ $tech['image'] ?? '' }}">
                        <input type="file" name="image[{{ $i }}]" class="form-control" accept="image/*" style="padding:4px">
                        @if(!empty($tech['image']))<small class="text-muted">Uploaded ✓ (naya upload purani ko replace karega)</small>@endif
                    </td>
                    <td><button type="button" class="btn-sm btn-delete js-remove"><i class="fas fa-trash"></i></button></td>
                </tr>
                @endforeach
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
        <td style="text-align:center"><input type="checkbox" name="active[__I__]" value="1" checked></td>
        <td><input type="text" name="name[__I__]" class="form-control js-name" placeholder="e.g. Next.js"></td>
        <td><input type="text" name="icon[__I__]" class="form-control js-icon" placeholder="fab fa-react"></td>
        <td><input type="text" name="color[__I__]" class="form-control js-color" placeholder="#61DAFB"></td>
        <td>
            <input type="hidden" name="existing_image[__I__]" value="">
            <input type="file" name="image[__I__]" class="form-control" accept="image/*" style="padding:4px">
        </td>
        <td><button type="button" class="btn-sm btn-delete js-remove"><i class="fas fa-trash"></i></button></td>
    </tr>
</template>

<script>
(function () {
    var rows = document.getElementById('techRows');
    var tpl  = document.getElementById('techRowTpl');
    var next = {{ count($technologies) }}; // next free row index

    document.getElementById('addTech').addEventListener('click', function () {
        var html = tpl.innerHTML.replace(/__I__/g, next++);
        var tmp = document.createElement('tbody');
        tmp.innerHTML = html.trim();
        rows.appendChild(tmp.firstChild);
    });

    rows.addEventListener('click', function (e) {
        var btn = e.target.closest('.js-remove');
        if (btn) { btn.closest('tr').remove(); }
    });

    // Live icon/color preview (only when no image is uploaded for the row)
    rows.addEventListener('input', function (e) {
        var row = e.target.closest('tr');
        if (!row) return;
        var prevCell = row.querySelector('.tech-preview');
        if (prevCell && prevCell.querySelector('img')) return; // image present, skip icon preview
        var icon  = row.querySelector('.js-icon');
        var color = row.querySelector('.js-color');
        var prev  = prevCell ? prevCell.querySelector('i') : null;
        if (prev) {
            prev.className = (icon && icon.value.trim()) ? icon.value.trim() : 'fas fa-code';
            prev.style.color = (color && color.value.trim()) ? color.value.trim() : '#1E5EFF';
        }
    });
})();
</script>
@endsection
