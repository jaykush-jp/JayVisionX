<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OauthToken;
use App\Models\Setting;
use App\Services\GoogleBusinessProfileService;
use App\Services\MetaGraphService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Central integrations hub + OAuth connect/callback for Google & Meta.
 *
 * The connect flow is fully wired but only runs when the app credentials exist
 * in config/env. Without them the UI shows a "not configured" state instead of
 * erroring, so the panel is safe to ship before the Google/Meta apps are
 * approved.
 */
class AdminIntegrationController extends Controller
{
    public function index(GoogleBusinessProfileService $gbp, MetaGraphService $meta)
    {
        $cards = [
            [
                'key'   => 'google_analytics',
                'name'  => 'Google Analytics (GA4)',
                'icon'  => 'fas fa-chart-line',
                'connected' => (bool) Setting::get('google_analytics'),
                'detail'    => Setting::get('google_analytics') ?: 'Add your GA4 Measurement ID in Website Settings.',
                'manage'    => route('admin.settings.index'),
            ],
            [
                'key'   => 'google_tag_manager',
                'name'  => 'Google Tag Manager',
                'icon'  => 'fab fa-google',
                'connected' => (bool) Setting::get('google_tag_manager'),
                'detail'    => Setting::get('google_tag_manager') ?: 'Add your GTM container ID (needed for Google Ads conversions).',
                'manage'    => route('admin.settings.index'),
            ],
            [
                'key'   => 'google_sheets',
                'name'  => 'Google Sheets (Leads)',
                'icon'  => 'fas fa-table',
                'connected' => (bool) config('services.google_sheets.webhook_url'),
                'detail'    => config('services.google_sheets.webhook_url') ? 'Leads are pushed to your sheet.' : 'Set GOOGLE_SHEETS_WEBHOOK_URL in .env.',
                'manage'    => null,
            ],
            [
                'key'   => 'anthropic',
                'name'  => 'AI Chatbot (Anthropic)',
                'icon'  => 'fas fa-robot',
                'connected' => (bool) config('services.anthropic.key'),
                'detail'    => config('services.anthropic.key') ? 'Chatbot runs in AI mode.' : 'Runs in free FAQ fallback mode until ANTHROPIC_API_KEY is set.',
                'manage'    => null,
            ],
            [
                'key'       => 'google',
                'name'      => 'Google Business Profile',
                'icon'      => 'fas fa-map-marker-alt',
                'oauth'     => true,
                'configured'=> $gbp->isConfigured(),
                'connected' => $gbp->isConnected(),
                'detail'    => $gbp->isConnected()
                    ? ('Connected: ' . ($gbp->accountLabel() ?: 'Google account'))
                    : ($gbp->isConfigured() ? 'Ready to connect — reviews & Google Posts.' : 'Add Google OAuth credentials in .env to enable.'),
            ],
            [
                'key'       => 'meta',
                'name'      => 'Meta (Facebook & Instagram)',
                'icon'      => 'fab fa-facebook',
                'oauth'     => true,
                'configured'=> $meta->isConfigured(),
                'connected' => $meta->isConnected(),
                'detail'    => $meta->isConnected()
                    ? ('Connected: ' . ($meta->accountLabel() ?: 'Meta account'))
                    : ($meta->isConfigured() ? 'Ready to connect — publish posts to FB & IG.' : 'Add Meta app credentials in .env to enable.'),
            ],
        ];

        return view('admin.integrations.index', compact('cards'));
    }

    /** Build the provider consent URL and redirect. */
    public function connect(string $provider)
    {
        if ($provider === 'google') {
            if (! config('services.google.client_id')) {
                return back()->with('error', 'Google OAuth credentials are not configured in .env yet.');
            }
            $params = http_build_query([
                'client_id'     => config('services.google.client_id'),
                'redirect_uri'  => route('admin.integrations.callback', 'google'),
                'response_type' => 'code',
                'access_type'   => 'offline',
                'prompt'        => 'consent',
                'scope'         => config('services.google.scopes', 'https://www.googleapis.com/auth/business.manage'),
                'state'         => csrf_token(),
            ]);
            return redirect('https://accounts.google.com/o/oauth2/v2/auth?' . $params);
        }

        if ($provider === 'meta') {
            if (! config('services.meta.app_id')) {
                return back()->with('error', 'Meta app credentials are not configured in .env yet.');
            }
            $params = http_build_query([
                'client_id'     => config('services.meta.app_id'),
                'redirect_uri'  => route('admin.integrations.callback', 'meta'),
                'response_type' => 'code',
                'scope'         => config('services.meta.scopes', 'pages_manage_posts,pages_read_engagement,instagram_basic,instagram_content_publish'),
                'state'         => csrf_token(),
            ]);
            return redirect('https://www.facebook.com/' . config('services.meta.graph_version', 'v21.0') . '/dialog/oauth?' . $params);
        }

        abort(404);
    }

    /** Exchange the authorization code for a token and store it. */
    public function callback(Request $request, string $provider)
    {
        if ($request->filled('error') || ! $request->filled('code')) {
            return redirect()->route('admin.integrations.index')
                ->with('error', 'Connection cancelled or failed.');
        }

        try {
            if ($provider === 'google') {
                $res = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                    'code'          => $request->code,
                    'client_id'     => config('services.google.client_id'),
                    'client_secret' => config('services.google.client_secret'),
                    'redirect_uri'  => route('admin.integrations.callback', 'google'),
                    'grant_type'    => 'authorization_code',
                ])->json();

                $this->storeToken('google', $res, 'Google Business Profile');
            } elseif ($provider === 'meta') {
                $res = Http::get('https://graph.facebook.com/' . config('services.meta.graph_version', 'v21.0') . '/oauth/access_token', [
                    'client_id'     => config('services.meta.app_id'),
                    'client_secret' => config('services.meta.app_secret'),
                    'redirect_uri'  => route('admin.integrations.callback', 'meta'),
                    'code'          => $request->code,
                ])->json();

                $this->storeToken('meta', $res, 'Meta (Facebook & Instagram)');
            } else {
                abort(404);
            }
        } catch (\Throwable $e) {
            Log::warning("OAuth callback failed for {$provider}: " . $e->getMessage());
            return redirect()->route('admin.integrations.index')->with('error', 'Could not complete the connection.');
        }

        return redirect()->route('admin.integrations.index')->with('success', Str::title($provider) . ' connected.');
    }

    public function disconnect(string $provider)
    {
        OauthToken::where('provider', $provider)->delete();
        return back()->with('success', Str::title($provider) . ' disconnected.');
    }

    private function storeToken(string $provider, array $res, string $label): void
    {
        if (empty($res['access_token'])) {
            throw new \RuntimeException('No access_token in token response.');
        }

        OauthToken::updateOrCreate(
            ['provider' => $provider],
            [
                'account_label' => $label,
                'access_token'  => $res['access_token'],
                'refresh_token' => $res['refresh_token'] ?? null,
                'expires_at'    => isset($res['expires_in']) ? now()->addSeconds((int) $res['expires_in']) : null,
                'scopes'        => $res['scope'] ?? null,
                'meta'          => [],
            ]
        );
    }
}
