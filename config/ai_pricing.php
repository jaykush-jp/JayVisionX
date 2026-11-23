<?php

/*
|--------------------------------------------------------------------------
| AI pricing (estimates)
|--------------------------------------------------------------------------
| Approximate public list rates in USD per 1,000,000 tokens, used to estimate
| chatbot AI spend on the Admin → AI Insights page. These are estimates — the
| providers' own billing is the source of truth. Update as rates change.
*/

return [

    // Rough INR per USD, for display only.
    'usd_to_inr' => 88,

    // Per-generated-image cost (USD), if image generation is ever added.
    'image_cost' => 0.04,

    // Per-model rates: ['in' => $/1M input tokens, 'out' => $/1M output tokens].
    'models' => [
        // Google Gemini (Flash tiers are cheap)
        'gemini-3.8-flash'        => ['in' => 0.075, 'out' => 0.30],
        'gemini-3.6-flash'        => ['in' => 0.075, 'out' => 0.30],
        'gemini-2.5-flash'        => ['in' => 0.075, 'out' => 0.30],
        'gemini-2.0-flash'        => ['in' => 0.075, 'out' => 0.30],
        'gemini-1.5-flash'        => ['in' => 0.075, 'out' => 0.30],
        'gemini-1.5-pro'          => ['in' => 1.25,  'out' => 5.00],

        // OpenAI
        'gpt-4o-mini'             => ['in' => 0.15,  'out' => 0.60],
        'gpt-4o'                  => ['in' => 2.50,  'out' => 10.00],

        // Anthropic Claude
        'claude-haiku-5-5'        => ['in' => 0.80,  'out' => 4.00],
        'claude-sonnet-5-5'       => ['in' => 3.00,  'out' => 15.00],
        'claude-opus-5-5'         => ['in' => 15.00, 'out' => 75.00],

        // DeepSeek / Grok / Mistral
        'deepseek-chat'           => ['in' => 0.14,  'out' => 0.28],
        'grok-2-latest'           => ['in' => 2.00,  'out' => 10.00],
        'mistral-small-latest'    => ['in' => 0.20,  'out' => 0.60],
    ],

    // Per-provider fallback when the exact model isn't listed above.
    'defaults' => [
        'gemini'    => ['in' => 0.10, 'out' => 0.40],
        'openai'    => ['in' => 0.15, 'out' => 0.60],
        'anthropic' => ['in' => 1.00, 'out' => 5.00],
        'deepseek'  => ['in' => 0.14, 'out' => 0.28],
        'grok'      => ['in' => 2.00, 'out' => 10.00],
        'mistral'   => ['in' => 0.20, 'out' => 0.60],
    ],
];
