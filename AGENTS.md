# AGENTS.md

## Stack & runtime
- Plain PHP (no framework) + MySQL/MariaDB (`nbsc_ojt`), served via XAMPP at `http://localhost/ICS-PORTAL/`. Verify by browser, not CLI; there's no dev server runner.
- Composer deps: phpmailer, phpdotenv, phpspreadsheet, google/apiclient, mpdf/mpdf (PDF export). `vendor/` is gitignored → run `composer install` after clone. No composer scripts.
- Frontend npm deps (tailwind/postcss/autoprefixer) exist but there is NO build script and no Tailwind-compiled output; the app uses hand-written CSS in `public/css/style.css`. Don't assume Tailwind classes render.
- `python/extract_entities.py` is a standalone spacy/pdfplumber entity-extraction tool with hardcoded localhost DB creds — not part of the PHP app runtime.

## Architecture (controller-view pattern)
- `.htaccess` web-blocks `config/`, `src/`, `database/`, `vendor/`, `documentation/`, `.env`. Those are PHP-include-only — never reference them from markup or client JS.
- Top-level PHP files are controllers: `session_start()` → require `config/db.php` → auth-guard on `$_SESSION['role']` (session keys: `user_id`, `role`, `student_id`) → query via global `$pdo` → then `require_once` a view from `src/pages/<role>/`. Keep markup in views, logic in controllers; views render plain `$variables` (no template engine).
- `config/db.php` sets up global `$pdo` (defaults localhost/root/empty-password, overridable via `.env` `DB_*`) and defines global helper `logActivity($pdo, $userId, $role, $action, $description)` — use it for user/audit events.
- Role pages: root scripts `dashboard.php`, `reports.php`, `profile.php`, `submit_report.php` are student-only; `review_report.php` is role-agnostic; `coordinator/` and `supervisor/` (incl. `supervisor/api/evaluation_otp.php`) are staff-only.
- All mail (welcome, reset, eval OTP) goes through `src/services/MailerService.php` (namespace `services`, Gmail SMTP from `.env`). It deliberately disables SSL cert verification (XAMPP localhost fix) — don't "fix" that for local dev.
- URLs inside views hardcode the `/ICS-PORTAL/` base path (e.g. `src/pages/student/dashboardPage.php`), and Google OAuth callback is hardcoded to `http://localhost/ICS-PORTAL/auth/google-callback.php`.

## Database & migrations
- Main DDL: `database/schema.sql` (array of sql dumps; names contain spaces, e.g. `nbsc_ojt v3.sql`). Follow `documentation/changes_08182026.md`: after schema, run the `migration_*.sql` files (or rewrite schema directly). Migration files include data dumps from the live DB.

## Gotchas
- `.env` holds live Google OAuth + Gmail app-password secrets; it's gitignored and web-blocked. Never commit it or echo its values.
- No lint/typecheck/test/build tooling is configured (`npm test` just errors). Verification is manual browser testing under XAMPP Apache+MySQL.
- Commit style from history: short imperative summaries ("Added …", "Updated …").