# UCO Website - AI Agent Instructions

Use this repo as a Laravel 12 + Vite application for UC alumni/student businesses, testimonials, and profile data. Keep changes small, framework-native, and aligned with the existing role-based access model.

## Start here

- Read `README.md` for the project overview.
- Check `routes/web.php` before changing navigation or route names; routes are grouped as public, authenticated, and admin-only.
- Review `app/Models/User.php` and `app/Models/Business.php` for the project’s model conventions before editing controllers, policies, or views.

## What matters in this codebase

- Roles live in `users.role` and the app uses `guest`, `student`, `alumni`, and `admin` behavior.
- Use `app/Http/Middleware/IsAdmin.php` for admin-only access and `app/Policies/BusinessPolicy.php` for business authorization.
- Public business viewing is intentional; editing and deletion are owner/admin-only.
- Business routes are order-sensitive: `Route::get('/businesses/{business}', ...)` stays last so it does not shadow static routes like `/businesses/create`.
- Use `getAuthUser()` / `getAuthUserOrNull()` patterns in controllers when authentication may or may not exist.
- Prefer `$user->isAdmin()` and `Business::canBeManagedBy($user)` over ad hoc role checks.

## Data and model conventions

- `User` and `Business` both use `HasImage`; image URLs are resolved through `app/Helpers/cloudinary_helpers.php`.
- `Business` auto-generates a unique slug on create and exposes a `profile_quality_score` accessor.
- `User` strips HTML from testimony text in its accessor.
- Keep Cloudinary in mind: runtime image URLs should go through the helper/trait, not hard-coded storage paths.

## AI, queues, and moderation

- All AI moderation goes through `app/Services/AiModerationService.php` and Google Gemini.
- Do not introduce embeddings or vector databases for this project.
- Testimony moderation expects the queue worker to be running locally.
- Use `GEMINI_MODEL`, `GEMINI_API_KEY`, and `GEMINI_DEBUG` when adjusting AI behavior.

## Import and bulk data rules

- Import users before businesses.
- Excel imports are strict about duplicate IDs and owner matching; check the import classes before changing column mappings.

## Commands to remember

- Setup: `composer run setup`
- Local dev: `composer run dev`
- Tests: `composer run test`
- Frontend build: `npm run build`

## Link, don’t duplicate

- If you need deeper implementation details, link to the relevant source file instead of copying large sections into this file.
- Prefer this file for high-level repo behavior only; keep task-specific guidance in separate instructions, prompts, or skills if needed.

## Useful references

- `routes/web.php`
- `app/Http/Middleware/IsAdmin.php`
- `app/Policies/BusinessPolicy.php`
- `app/Models/User.php`
- `app/Models/Business.php`
- `app/Services/AiModerationService.php`
- `app/Helpers/cloudinary_helpers.php`