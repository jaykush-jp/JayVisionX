# JayVisionX — Integrations Setup

How to turn on the three integrations added for the lead form and the support chatbot.
All of them are **optional** and fail safe: if a credential is missing, that feature simply
stays off and the rest of the site keeps working (leads are always saved to the database).

---

## 1. Email notification on every new lead

When someone submits the contact / landing form, an email is sent to your team.

**Setup**
1. Configure SMTP in `.env` (example for Gmail):
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=youraddress@gmail.com
   MAIL_PASSWORD=your_app_password          # Gmail → App Passwords (not your login password)
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS="info@jayvisionx.com"
   MAIL_FROM_NAME="JayVisionX"
   ```
2. Set who receives the notifications (optional — defaults to the site email in Settings):
   ```env
   LEAD_NOTIFY_EMAIL=sales@jayvisionx.com
   ```
3. Run `php artisan config:clear`.

Test by submitting the form. The email has the lead's details and a one-click **Reply** button.
If SMTP isn't set up, the lead still saves to **Admin → Contacts** (the email is skipped silently).

---

## 2. Google Sheet — every lead appended to a spreadsheet

Uses a **Google Apps Script web app** (no API keys, no Google Cloud project needed).

**Step 1 — Create the sheet**
- Create a new Google Sheet. Note the tab name (default `Sheet1`).

**Step 2 — Add the Apps Script**
- In the sheet: **Extensions → Apps Script**, delete any sample code, paste this:

```javascript
function doPost(e) {
  var lock = LockService.getScriptLock();
  lock.waitLock(20000);
  try {
    var sheet = SpreadsheetApp.getActiveSpreadsheet().getSheetByName('Sheet1');
    // Add a header row once if the sheet is empty
    if (sheet.getLastRow() === 0) {
      sheet.appendRow(['Submitted At', 'Name', 'Phone', 'Email', 'Service', 'Budget', 'Message', 'IP']);
    }
    var p = e.parameter;
    sheet.appendRow([
      p.submitted_at || new Date(),
      p.name || '',
      p.phone || '',
      p.email || '',
      p.service_interested || '',
      p.budget || '',
      p.message || '',
      p.ip_address || ''
    ]);
    return ContentService.createTextOutput(JSON.stringify({ ok: true }))
      .setMimeType(ContentService.MimeType.JSON);
  } finally {
    lock.releaseLock();
  }
}
```

**Step 3 — Deploy as a web app**
- **Deploy → New deployment → type: Web app**.
- *Execute as:* **Me**. *Who has access:* **Anyone**.
- Click **Deploy**, authorize, and copy the **Web app URL**.

**Step 4 — Put the URL in `.env`**
```env
GOOGLE_SHEETS_WEBHOOK_URL=https://script.google.com/macros/s/XXXXXXXX/exec
```
Run `php artisan config:clear`. Submit the form — a new row appears in your sheet.

> If you edit the script later, use **Deploy → Manage deployments → Edit → New version**, or the URL won't pick up changes.

---

## 3. AI Support Chatbot

A chat widget (bottom-left on every page) that answers visitors. It works in **two modes**:

- **AI mode** — when `ANTHROPIC_API_KEY` is set, it uses Claude and answers from both your
  site data (services, FAQs, contact info from the database) **and** general knowledge.
- **Fallback mode** — with no key, it answers from your FAQs, services, and contact details
  only (rule-based, free, no external calls).

**Setup (AI mode)**
1. Get an API key from the Anthropic Console (`console.anthropic.com`).
2. Add to `.env`:
   ```env
   ANTHROPIC_API_KEY=sk-ant-xxxxxxxx
   ANTHROPIC_MODEL=claude-haiku-5-5
   ```
   - `claude-haiku-5-5` — fastest & cheapest (~$0.10 / $0.50 per 1M tokens). Recommended for a support widget.
   - `claude-sonnet-5-5` or `claude-opus-5-5` — smarter, more expensive.
3. Run `php artisan config:clear`.

The bot automatically knows your services and FAQs from the database — when you add/edit
those in the admin panel, its answers update too. No code changes needed.

**Cost control:** replies are capped and the conversation history is trimmed to the last few
messages per request to keep token usage low.

---

## Quick checklist

| Integration | Env var(s) | Works without it? |
|-------------|-----------|-------------------|
| Email on new lead | `MAIL_*`, `LEAD_NOTIFY_EMAIL` | Yes — lead still saved to Contacts |
| Google Sheet | `GOOGLE_SHEETS_WEBHOOK_URL` | Yes — integration just off |
| AI chatbot | `ANTHROPIC_API_KEY`, `ANTHROPIC_MODEL` | Yes — runs in free FAQ/DB fallback mode |

After changing `.env`, always run `php artisan config:clear` (and `config:cache` in production).
