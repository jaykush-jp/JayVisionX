<?php

namespace App\Services;

use App\Models\Package;

/**
 * Deterministic quote estimator. Produces a min/max range ONLY from approved,
 * active package amounts in the database — it never fabricates a number. When
 * no matching priced package exists it returns nulls so the caller can offer a
 * custom quote instead.
 */
class QuoteBuilder
{
    /** Keywords that map a project type to matching services/packages. */
    private const KEYWORDS = [
        'website' => ['website', 'web design', 'web development', 'wordpress', 'landing'],
        'android' => ['android', 'app', 'mobile', 'ios'],
        'webapp'  => ['web app', 'application', 'saas', 'portal', 'software', 'crm'],
        'other'   => [],
    ];

    /**
     * @return array{min:?float,max:?float,currency:string,basis:array<int,string>}
     */
    public function estimate(string $projectType, array $packageIds = []): array
    {
        $query = Package::query()->with('service')
            ->whereNotNull('amount')
            ->where('price_type', '!=', 'custom')
            ->where('is_active', true);

        if ($packageIds) {
            $query->whereIn('id', $packageIds);
        } else {
            $keywords = self::KEYWORDS[$projectType] ?? [];
            if ($keywords) {
                $query->where(function ($w) use ($keywords) {
                    foreach ($keywords as $kw) {
                        $w->orWhere('name', 'like', "%{$kw}%")
                          ->orWhereHas('service', fn ($s) => $s->where('title', 'like', "%{$kw}%"));
                    }
                });
            }
        }

        $packages = $query->get();

        if ($packages->isEmpty()) {
            return ['min' => null, 'max' => null, 'currency' => 'INR', 'basis' => []];
        }

        $amounts = $packages->pluck('amount')->map(fn ($a) => (float) $a);

        return [
            'min'      => $amounts->min(),
            'max'      => $amounts->max(),
            'currency' => $packages->first()->currency ?: 'INR',
            'basis'    => $packages->map(fn ($p) => ($p->service ? $p->service->title . ' — ' : '') . $p->name . ': ' . $p->price_label)->all(),
        ];
    }
}
