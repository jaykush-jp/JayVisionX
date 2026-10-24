<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Package;
use App\Models\Service;
use Illuminate\Http\Request;

class AdminPackageController extends Controller
{
    public const PRICE_TYPES = [
        'fixed'         => 'Fixed',
        'starting_from' => 'Starting From',
        'hourly'        => 'Hourly',
        'monthly'       => 'Monthly',
        'custom'        => 'Custom Quote',
    ];

    public function index()
    {
        $packages = Package::with('service')->orderBy('service_id')->orderBy('sort_order')->paginate(20);
        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.packages.create', [
            'services'   => Service::orderBy('title')->get(),
            'priceTypes' => self::PRICE_TYPES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $package = Package::create($data);
        AuditLog::record('created', 'package', $package->id, ['name' => $package->name, 'amount' => $package->amount]);

        return redirect()->route('admin.packages.index')->with('success', 'Package created.');
    }

    public function edit(Package $package)
    {
        return view('admin.packages.edit', [
            'package'    => $package,
            'services'   => Service::orderBy('title')->get(),
            'priceTypes' => self::PRICE_TYPES,
        ]);
    }

    public function update(Request $request, Package $package)
    {
        $data = $this->validated($request);

        // Audit price changes specifically (SOP: price change history).
        if ((string) $package->amount !== (string) ($data['amount'] ?? null) || $package->price_type !== $data['price_type']) {
            AuditLog::record('price_changed', 'package', $package->id, [
                'from' => ['amount' => $package->amount, 'price_type' => $package->price_type],
                'to'   => ['amount' => $data['amount'] ?? null, 'price_type' => $data['price_type']],
            ]);
        }

        $package->update($data);
        AuditLog::record('updated', 'package', $package->id, ['name' => $package->name]);

        return redirect()->route('admin.packages.index')->with('success', 'Package updated.');
    }

    public function destroy(Package $package)
    {
        AuditLog::record('deleted', 'package', $package->id, ['name' => $package->name]);
        $package->delete();

        return redirect()->route('admin.packages.index')->with('success', 'Package deleted.');
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'service_id'         => 'nullable|exists:services,id',
            'name'               => 'required|string|max:150',
            'tier'               => 'nullable|string|max:50',
            'description'        => 'nullable|string',
            'price_type'         => 'required|in:' . implode(',', array_keys(self::PRICE_TYPES)),
            'amount'             => 'nullable|numeric|min:0',
            'currency'           => 'nullable|string|max:8',
            'pricing_unit'       => 'nullable|string|max:50',
            'min_budget'         => 'nullable|numeric|min:0',
            'max_budget'         => 'nullable|numeric|min:0',
            'estimated_timeline' => 'nullable|string|max:100',
            'tax_note'           => 'nullable|string|max:100',
            'quote_validity'     => 'nullable|string|max:100',
            'effective_date'     => 'nullable|date',
            'sort_order'         => 'nullable|integer',
        ]);

        // Custom quotes carry no amount.
        if ($validated['price_type'] === 'custom') {
            $validated['amount'] = null;
        }

        $validated['currency']      = $validated['currency'] ?: 'INR';
        $validated['sort_order']    = (int) ($validated['sort_order'] ?? 0);
        $validated['is_active']     = $request->boolean('is_active');
        $validated['features']      = $this->linesToArray($request->features_raw);
        $validated['add_ons']       = $this->linesToArray($request->add_ons_raw);
        $validated['exclusions']    = $this->linesToArray($request->exclusions_raw);

        return $validated;
    }

    private function linesToArray(?string $raw): ?array
    {
        if (! $raw) {
            return null;
        }
        $items = array_values(array_filter(array_map('trim', preg_split('/\r?\n/', $raw))));
        return $items ?: null;
    }
}
