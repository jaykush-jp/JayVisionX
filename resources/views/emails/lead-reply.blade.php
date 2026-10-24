<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ \App\Models\Setting::get('site_name', 'JayVisionX') }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f7ff;font-family:Arial,Helvetica,sans-serif;color:#0a1628;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7ff;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 8px 32px rgba(10,22,40,0.08);">
                    <tr>
                        <td style="background:linear-gradient(120deg,#0a5baa,#0a7ce0 60%,#15c6ff);padding:24px 28px;">
                            <h1 style="margin:0;color:#ffffff;font-size:20px;">{{ \App\Models\Setting::get('site_name', 'JayVisionX') }}</h1>
                            <p style="margin:6px 0 0;color:#e8f3ff;font-size:13px;">{{ \App\Models\Setting::get('site_tagline', 'Digital Growth & Technology Company') }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;font-size:15px;line-height:1.7;color:#0a1628;">
                            @if($leadName)
                                <p style="margin:0 0 16px;">Hi {{ $leadName }},</p>
                            @endif
                            {!! nl2br(e($bodyText)) !!}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 28px;background:#0a1628;color:#8b9bb4;font-size:12px;line-height:1.6;text-align:center;">
                            {{ \App\Models\Setting::get('site_name', 'JayVisionX') }}
                            @if(\App\Models\Setting::get('site_phone')) · {{ \App\Models\Setting::get('site_phone') }} @endif
                            @if(\App\Models\Setting::get('site_email')) · {{ \App\Models\Setting::get('site_email') }} @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
