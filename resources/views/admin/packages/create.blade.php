@extends('admin.layouts.app')
@section('title', isset($package) ? 'Edit Package' : 'Add Package')
@section('content')
<div class="page-header-row">
    <h2>{{ isset($package) ? 'Edit Package' : 'Add Package' }}</h2>
    <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>

<div class="admin-form-card">
    <form action="{{ isset($package) ? route('admin.packages.update', $package->id) : route('admin.packages.store') }}" method="POST">
        @csrf
        @if(isset($package)) @method('PUT') @endif

        <div class="form-row-3">
            <div class="form-group">
                <label>Package Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $package->name ?? '') }}" required placeholder="e.g. Basic Website">
                @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Tier</label>
                <select name="tier" class="form-control">
                    @php $tier = old('tier', $package->tier ?? ''); @endphp
                    <option value="" @selected($tier==='')>—</option>
                    @foreach(['basic'=>'Basic','standard'=>'Standard','premium'=>'Premium','custom'=>'Custom'] as $k=>$v)
                        <option value="{{ $k }}" @selected($tier===$k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Related Service</label>
                <select name="service_id" class="form-control">
                    @php $sid = old('service_id', $package->service_id ?? ''); @endphp
                    <option value="">— General —</option>
                    @foreach($services as $s)
                        <option value="{{ $s->id }}" @selected((string)$sid===(string)$s->id)>{{ $s->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-row-3">
            <div class="form-group">
                <label>Price Type *</label>
                <select name="price_type" class="form-control" required>
                    @php $pt = old('price_type', $package->price_type ?? 'starting_from'); @endphp
                    @foreach($priceTypes as $k=>$v)
                        <option value="{{ $k }}" @selected($pt===$k)>{{ $v }}</option>
                    @endforeach
                </select>
                <small class="form-hint">"Custom Quote" ignores the amount.</small>
            </div>
            <div class="form-group">
                <label>Amount</label>
                <input type="number" step="0.01" min="0" name="amount" class="form-control" value="{{ old('amount', $package->amount ?? '') }}" placeholder="e.g. 25000">
            </div>
            <div class="form-group">
                <label>Currency</label>
                <input type="text" name="currency" class="form-control" value="{{ old('currency', $package->currency ?? 'INR') }}">
            </div>
        </div>

        <div class="form-row-3">
            <div class="form-group">
                <label>Pricing Unit</label>
                <input type="text" name="pricing_unit" class="form-control" value="{{ old('pricing_unit', $package->pricing_unit ?? '') }}" placeholder="per project / per month">
            </div>
            <div class="form-group">
                <label>Min Budget</label>
                <input type="number" step="0.01" min="0" name="min_budget" class="form-control" value="{{ old('min_budget', $package->min_budget ?? '') }}">
            </div>
            <div class="form-group">
                <label>Max Budget</label>
                <input type="number" step="0.01" min="0" name="max_budget" class="form-control" value="{{ old('max_budget', $package->max_budget ?? '') }}">
            </div>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description', $package->description ?? '') }}</textarea>
        </div>

        <div class="form-row-3">
            <div class="form-group">
                <label>Included Features (one per line)</label>
                <textarea name="features_raw" class="form-control" rows="5" placeholder="5 pages&#10;Contact form&#10;Basic SEO">{{ isset($package) && $package->features ? implode("\n", $package->features) : old('features_raw','') }}</textarea>
            </div>
            <div class="form-group">
                <label>Add-ons (one per line)</label>
                <textarea name="add_ons_raw" class="form-control" rows="5" placeholder="Extra page&#10;Logo design">{{ isset($package) && $package->add_ons ? implode("\n", $package->add_ons) : old('add_ons_raw','') }}</textarea>
            </div>
            <div class="form-group">
                <label>Exclusions (one per line)</label>
                <textarea name="exclusions_raw" class="form-control" rows="5" placeholder="Content writing&#10;Hosting">{{ isset($package) && $package->exclusions ? implode("\n", $package->exclusions) : old('exclusions_raw','') }}</textarea>
            </div>
        </div>

        <div class="form-row-3">
            <div class="form-group">
                <label>Estimated Timeline</label>
                <input type="text" name="estimated_timeline" class="form-control" value="{{ old('estimated_timeline', $package->estimated_timeline ?? '') }}" placeholder="e.g. 2–3 weeks">
            </div>
            <div class="form-group">
                <label>Tax Note</label>
                <input type="text" name="tax_note" class="form-control" value="{{ old('tax_note', $package->tax_note ?? '') }}" placeholder="e.g. +18% GST">
            </div>
            <div class="form-group">
                <label>Quote Validity</label>
                <input type="text" name="quote_validity" class="form-control" value="{{ old('quote_validity', $package->quote_validity ?? '') }}" placeholder="e.g. Valid for 15 days">
            </div>
        </div>

        <div class="form-row-3">
            <div class="form-group">
                <label>Effective Date</label>
                <input type="date" name="effective_date" class="form-control" value="{{ old('effective_date', isset($package) && $package->effective_date ? $package->effective_date->format('Y-m-d') : '') }}">
            </div>
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $package->sort_order ?? 0) }}">
            </div>
            <div class="form-group form-check-group">
                <label class="form-check-label">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $package->is_active ?? true) ? 'checked' : '' }}> Active
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> {{ isset($package) ? 'Update Package' : 'Save Package' }}</button>
            <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary btn-lg">Cancel</a>
        </div>
    </form>
</div>
@endsection
