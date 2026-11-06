<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Credential;
use Illuminate\Http\Request;

/**
 * Admin → API Credentials.
 *
 * One place to add / update / delete every third-party API credential (AI
 * provider keys, Google & Meta app credentials and tokens). Everything is
 * stored encrypted in the `credentials` table — nothing lives in .env.
 *
 * Field types:
 *  - secret  : shown masked ("saved — leave blank to keep"); blank keeps the
 *              stored value, the "Clear" checkbox removes it.
 *  - visible : shown with its current value; saving stores exactly what's in the
 *              box (an empty box removes it). Used for non-secret config such as
 *              scopes and the Graph API version.
 */
class AdminCredentialController extends Controller
{
    /**
     * Grouped field definitions. Keys are the credential keys used across the
     * app (see AiProviderService, GoogleBusinessProfileService, MetaGraphService).
     *
     * @return array<string, array<string, array{label:string, secret:bool, hint?:string}>>
     */
    public static function groups(): array
    {
        return [
            'AI Provider Keys' => [
                'ai.anthropic.key' => ['label' => 'Anthropic (Claude) API key', 'secret' => true, 'hint' => 'console.anthropic.com'],
                'ai.gemini.key'    => ['label' => 'Google Gemini API key',      'secret' => true, 'hint' => 'aistudio.google.com'],
                'ai.openai.key'    => ['label' => 'OpenAI (GPT) API key',        'secret' => true, 'hint' => 'platform.openai.com'],
                'ai.deepseek.key'  => ['label' => 'DeepSeek API key',            'secret' => true, 'hint' => 'platform.deepseek.com'],
                'ai.grok.key'      => ['label' => 'Grok (xAI) API key',          'secret' => true, 'hint' => 'console.x.ai'],
                'ai.mistral.key'   => ['label' => 'Mistral API key',             'secret' => true, 'hint' => 'console.mistral.ai'],
            ],
            'Google' => [
                'google.client_id'          => ['label' => 'OAuth Client ID',            'secret' => false],
                'google.client_secret'      => ['label' => 'OAuth Client Secret',        'secret' => true],
                'google.scopes'             => ['label' => 'OAuth Scopes',               'secret' => false, 'hint' => 'Business Profile: https://www.googleapis.com/auth/business.manage'],
                'google.sheets_webhook_url' => ['label' => 'Sheets Webhook URL (leads)', 'secret' => false],
                'google.lead_webhook_key'   => ['label' => 'Google Ads Lead Webhook Key', 'secret' => true],
            ],
            'Meta (Facebook & Instagram)' => [
                'meta.app_id'            => ['label' => 'App ID',                   'secret' => false],
                'meta.app_secret'        => ['label' => 'App Secret',               'secret' => true],
                'meta.graph_version'     => ['label' => 'Graph API Version',        'secret' => false, 'hint' => 'e.g. v21.0'],
                'meta.scopes'            => ['label' => 'OAuth Scopes',             'secret' => false],
                'meta.page_access_token' => ['label' => 'Page Access Token',        'secret' => true],
                'meta.lead_verify_token' => ['label' => 'Lead Webhook Verify Token', 'secret' => true],
            ],
        ];
    }

    public function index()
    {
        // Build a view model that never exposes a secret value.
        $groups = [];
        foreach (self::groups() as $group => $fields) {
            foreach ($fields as $key => $meta) {
                $groups[$group][$key] = $meta + [
                    'isSet'  => Credential::rawHas($key),          // value stored (even if disabled)
                    'active' => Credential::isActive($key),        // enabled flag
                    // Only non-secret config values are echoed back to the form.
                    'value'  => $meta['secret'] ? null : (Credential::rawValue($key) ?? ''),
                ];
            }
        }

        return view('admin.credentials.index', ['groups' => $groups]);
    }

    public function update(Request $request)
    {
        $values  = (array) $request->input('cred', []);
        $clears  = (array) $request->input('clear', []);
        $actives = (array) $request->input('active', []);  // checkbox present only when ON

        $changed = [];

        foreach (self::groups() as $fields) {
            foreach ($fields as $key => $meta) {
                // Explicit clear wins.
                if (! empty($clears[$key])) {
                    if (Credential::rawHas($key)) {
                        Credential::set($key, null);
                        $changed[] = $key . ' (cleared)';
                    }
                    continue;
                }

                $val = array_key_exists($key, $values) ? trim((string) $values[$key]) : null;

                if ($meta['secret']) {
                    // Blank keeps the stored secret.
                    if ($val !== null && $val !== '') {
                        Credential::set($key, $val);
                        $changed[] = $key;
                    }
                } else {
                    // Visible config: save exactly what's in the box (blank removes).
                    if ($val !== null) {
                        $current = Credential::rawValue($key) ?? '';
                        if ($val !== $current) {
                            Credential::set($key, $val);
                            $changed[] = $key;
                        }
                    }
                }

                // Enable/disable toggle (only affects a stored credential).
                if (Credential::rawHas($key)) {
                    $wantActive = isset($actives[$key]);
                    if (Credential::isActive($key) !== $wantActive) {
                        Credential::setActive($key, $wantActive);
                        $changed[] = $key . ($wantActive ? ' (enabled)' : ' (disabled)');
                    }
                }
            }
        }

        if ($changed) {
            AuditLog::record('updated', 'credentials', null, ['keys' => $changed]);
        }

        return back()->with('success', 'API credentials saved.');
    }
}
