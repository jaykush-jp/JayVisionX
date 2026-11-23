<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiSetting;
use App\Models\AiUsage;
use App\Models\Credential;
use App\Services\AiProviderService;
use Illuminate\Http\Request;

class AdminAiInsightController extends Controller
{
    /** Providers shown as cards on the insights page. */
    private const CARDS = ['gemini', 'openai', 'anthropic'];

    public function index(Request $request)
    {
        $range = in_array($request->get('range'), ['7', '30', 'month', 'all'], true) ? $request->get('range') : '30';
        $from  = match ($range) {
            '7'     => now()->subDays(7),
            'month' => now()->startOfMonth(),
            'all'   => null,
            default => now()->subDays(30),
        };

        $inr = (float) config('ai_pricing.usd_to_inr', 88);
        $ai  = AiSetting::current();

        // Headline totals (fixed windows)
        $summary = [
            'today' => (float) AiUsage::whereDate('created_at', today())->sum('cost'),
            'month' => (float) AiUsage::where('created_at', '>=', now()->startOfMonth())->sum('cost'),
            'd30'   => (float) AiUsage::where('created_at', '>=', now()->subDays(30))->sum('cost'),
            'all'   => (float) AiUsage::sum('cost'),
        ];

        // Per-provider cards (within the selected range)
        $providers = [];
        foreach (self::CARDS as $p) {
            $q = AiUsage::where('provider', $p);
            if ($from) {
                $q->where('created_at', '>=', $from);
            }
            $providers[$p] = [
                'label'   => AiProviderService::PROVIDERS[$p]['label'] ?? ucfirst($p),
                'cost'    => (float) (clone $q)->sum('cost'),
                'calls'   => (int) (clone $q)->count(),
                'in'      => (int) (clone $q)->sum('input_tokens'),
                'out'     => (int) (clone $q)->sum('output_tokens'),
                'images'  => (int) (clone $q)->sum('images'),
                'model'   => $ai->provider === $p ? ($ai->model ?: (AiProviderService::PROVIDERS[$p]['default_model'] ?? '—')) : (AiProviderService::PROVIDERS[$p]['default_model'] ?? '—'),
                'key_set' => Credential::rawHas("ai.{$p}.key"),
                'active'  => $ai->provider === $p,
            ];
        }

        // Daily spend — last 30 days
        $rows = AiUsage::where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->selectRaw('DATE(created_at) d, SUM(cost) c')
            ->groupBy('d')->pluck('c', 'd');
        $daily = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $daily[$date] = (float) ($rows[$date] ?? 0);
        }

        // Spend by model (within range)
        $bm = AiUsage::query();
        if ($from) {
            $bm->where('created_at', '>=', $from);
        }
        $byModel = $bm->selectRaw('provider, model, COUNT(*) calls, SUM(input_tokens) intok, SUM(output_tokens) outtok, SUM(images) images, SUM(cost) cost')
            ->groupBy('provider', 'model')
            ->orderByDesc('cost')->get();

        return view('admin.ai-insights.index', [
            'range'       => $range,
            'inr'         => $inr,
            'summary'     => $summary,
            'providers'   => $providers,
            'activeLabel' => AiProviderService::PROVIDERS[$ai->provider]['label'] ?? ($ai->provider ?: '—'),
            'daily'       => $daily,
            'byModel'     => $byModel,
        ]);
    }
}
