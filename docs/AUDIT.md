# JayVisionX — Audit & Issue Tracker

**Scope:** Phases 1–4 of the Upgrade SOP (source-code audit, backend, database).
**Date:** 2026-10-22
**Branch:** `security/harden-admin-auth-and-secrets`
**Stack confirmed:** Laravel 11, PHP 8.2, MySQL. Routes: 69 total (frontend + session-based admin CMS). No `routes/api.php` (no API layer yet).

Status legend: **Pending / In Progress / Blocked / Completed / Deferred**
Priority: **P0** critical · **P1** high · **P2** medium · **P3** low

---

## A. Fixed earlier in this session (security + responsive)

| ID | Module | Issue | Priority | Status |
|----|--------|-------|----------|--------|
| JVX-001 | Config / VCS | `.env` committed to git; no `.gitignore` | P0 | Completed (commit `e580629`) — secrets still need rotating |
| JVX-002 | Auth | Plaintext admin password compare; no hashing/throttle/session-regeneration | P0 | Completed (`e580629`) |
| JVX-003 | Auth | Credentials silently fell back to documented defaults under `config:cache` | P0 | Completed (`e580629`) |
| JVX-004 | Uploads | Settings logo/favicon upload had no mime/size validation (RCE risk) | P0 | Completed (`e580629`) |
| JVX-005 | Frontend | Not responsive below 640px; horizontal overflow; broken mobile drawer | P1 | Completed (uncommitted CSS) |

---

## B. Fixed in this audit pass (Phases 1–4)

| ID | Module | Issue | Priority | Affected files | Status |
|----|--------|-------|----------|----------------|--------|
| JVX-010 | Routing | All 7 admin `show` resource routes 500 — Service/Project/Blog controllers have no `show()`; CaseStudy/Faq/Testimonial/Page have `show()` but their Blade views are missing. No view links to them. | P1 | `routes/web.php` | Completed — excluded `show` from admin resources |
| JVX-011 | Settings | `AdminSettingController@update` writes **every** POSTed field as a settings row (no allow-list) — arbitrary/overwritten settings, incl. the GA snippet. | P1 | `app/Http/Controllers/Admin/AdminSettingController.php` | Completed — whitelisted keys |
| JVX-012 | Frontend | `Page::getPage()` returns `null` when a page row is missing → property-on-null warnings in every frontend view. | P2 | `app/Models/Page.php` | Completed — returns empty model instead of null |

---

## C. Open findings (recommended next)

| ID | Module | Issue | Priority | Affected files | Proposed fix | Status |
|----|--------|-------|----------|----------------|--------------|--------|
| JVX-020 | Security (XSS) | Admin-authored HTML rendered raw via `{!! !!}` in 4 detail views. Admin-only, but chains with any admin compromise. | P1 | `blog/show`, `services/show`, `projects/show`, `our-work/show` Blade | Sanitize with `mews/purifier` (`{!! clean(...) !!}`). Needs composer. | Deferred → "Finish security" increment |
| JVX-021 | Security | `APP_DEBUG=true` and `APP_ENV=local` in `.env`; leaks stack traces/env in production. | P0 (prod only) | `.env` | Set `APP_ENV=production`, `APP_DEBUG=false` on the live server. | Pending (env change, not code) |
| JVX-022 | Performance / DB | No indexes on hot filter/sort columns (`is_active`, `sort_order`) queried on every page. | P2 | new migration | Composite `(is_active, sort_order)` indexes. Migration drafted — see JVX-023. | Pending (needs `php artisan migrate`) |
| JVX-023 | Performance | Admin index pages (`services/projects/blogs/testimonials/faqs/case-studies`) use `->get()` — load all rows, no pagination. | P2 | 6 admin controllers | Switch to `->paginate(20)` + pagination links in views. | Pending |
| JVX-024 | Robustness | CRUD controllers build `$data = $request->except([...])` then `create/update($data)` — bounded by `$fillable` but not per-field whitelisted; status flags (`is_active`, `show_on_home`, `sort_order`) settable from any field. | P2 | all admin CRUD controllers | Prefer `$request->validate()` + `$request->only([...])`. | Pending |
| JVX-025 | Housekeeping | `database/migrations/README.php` is clutter. **Not a bug** — migrator globs `*_*.php`, so it's skipped. | P3 | `database/migrations/README.php` | Delete the file. | Pending |
| JVX-026 | Content | No 404 / Privacy Policy / Terms pages. | P1–P2 | views + routes | Phase 7 increment. | Deferred → "Pages + SEO" increment |
| JVX-027 | Model | `Project.completion_date` not cast to `date`. | P3 | `app/Models/Project.php` | Add `'completion_date' => 'date'` cast. | Pending |

---

## D. Verified OK (no action)

- Frontend detail controllers use `firstOrFail()` → proper 404 on bad slug.
- All forms include `@csrf`; admin routes behind `admin.auth` + session; web CSRF active.
- Models use `$fillable` (mass assignment bounded).
- Pages are seeded (home/about/services/projects/blog/contact); frontend meta uses `?? default`.
- Schema has `unique` on all slugs/keys; JSON columns (`features`, `gallery`, `technologies`, `stats`) cast to array.
- `toggle-status` endpoints are POST (CSRF-protected).

---

## E. Test checklist for this pass

- [ ] Admin login still works; dashboard loads.
- [ ] Each admin list (services/projects/blogs/etc.) loads and edit works.
- [ ] Hitting an old `/admin/services/{id}` (show) now 404s cleanly instead of 500.
- [ ] Settings save persists known keys; unknown injected keys ignored.
- [ ] Every frontend page loads with no property-on-null warning.
