<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Enquiry</title>
</head>
<body style="margin:0;padding:0;background:#f4f7ff;font-family:Arial,Helvetica,sans-serif;color:#0a1628;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7ff;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 8px 32px rgba(10,22,40,0.08);">
                    <tr>
                        <td style="background:linear-gradient(120deg,#0a5baa,#0a7ce0 60%,#15c6ff);padding:24px 28px;">
                            <h1 style="margin:0;color:#ffffff;font-size:20px;">🔔 New Enquiry Received</h1>
                            <p style="margin:6px 0 0;color:#e8f3ff;font-size:13px;">JayVisionX — Lead Notification</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;line-height:1.6;">
                                <tr><td style="padding:8px 0;color:#6b7fa3;width:140px;">Name</td><td style="padding:8px 0;font-weight:bold;">{{ $lead->name }}</td></tr>
                                <tr><td style="padding:8px 0;color:#6b7fa3;">Phone</td><td style="padding:8px 0;"><a href="tel:{{ $lead->phone }}" style="color:#0a7ce0;">{{ $lead->phone ?: '—' }}</a></td></tr>
                                <tr><td style="padding:8px 0;color:#6b7fa3;">Email</td><td style="padding:8px 0;"><a href="mailto:{{ $lead->email }}" style="color:#0a7ce0;">{{ $lead->email }}</a></td></tr>
                                <tr><td style="padding:8px 0;color:#6b7fa3;">Service</td><td style="padding:8px 0;">{{ $lead->service_interested ?: '—' }}</td></tr>
                                <tr><td style="padding:8px 0;color:#6b7fa3;">Budget</td><td style="padding:8px 0;">{{ $lead->budget ?: '—' }}</td></tr>
                                <tr><td style="padding:8px 0;color:#6b7fa3;vertical-align:top;">Message</td><td style="padding:8px 0;">{{ $lead->message ?: '—' }}</td></tr>
                                <tr><td style="padding:8px 0;color:#6b7fa3;">Received</td><td style="padding:8px 0;">{{ $lead->created_at->format('d M Y, h:i A') }}</td></tr>
                            </table>
                            <div style="margin-top:24px;">
                                <a href="mailto:{{ $lead->email }}" style="display:inline-block;background:#0a7ce0;color:#ffffff;text-decoration:none;padding:12px 24px;border-radius:8px;font-weight:bold;font-size:14px;">Reply to {{ $lead->name }}</a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 28px;background:#0a1628;color:#8b9bb4;font-size:12px;text-align:center;">
                            This lead is also saved in your Admin Panel → Contacts.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
