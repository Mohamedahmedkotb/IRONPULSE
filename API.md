# IronPulse REST API

Base URL (default from frontend): `…/backend/api/`  
All JSON responses:

```json
{ "success": true, "message": "…", "data": { } }
```

Errors: `success: false`, HTTP 4xx/5xx, same shape.

**Credentials:** send cookies (`credentials: "include"`).  
**CSRF:** after login/register/me, store `csrf_token` and send header `X-CSRF-Token` on `POST`, `PUT`, `DELETE` (except `auth/register.php` and `auth/login.php`).

---

## Auth

| Method | Path | Auth | Body / query | Data |
|--------|------|------|----------------|------|
| POST | `auth/register.php` | No | JSON: `full_name`, `email`, `password`, optional `fitness_goal`, `gender` | `{ user, csrf_token }` |
| POST | `auth/login.php` | No | JSON: `email`, `password` | `{ user, csrf_token }` |
| POST | `auth/logout.php` | Yes + CSRF | JSON optional `{}` | `null` |
| GET | `auth/me.php` | Yes | — | `{ user, csrf_token, stats }` — `stats`: `workout_count`, `routines_count`, `calories_week`, `streak_days`, `chart_calories_last_7` |

`user` objects never include `password_hash`.

---

## Users

| Method | Path | Auth | Notes |
|--------|------|------|--------|
| GET | `users/profile.php` | Yes | Returns `{ profile }` (name, email, goals[], etc.) |
| PUT / POST | `users/update.php` | Yes + CSRF | JSON: `full_name`/`name`, `email`, `phone`, `city`, `gender`, `bio`, `goals`[], optional `age`, `height`, `weight`, `activity_level` |
| POST | `users/upload-avatar.php` | Yes + CSRF | `multipart/form-data` field `avatar` or `file` | Returns `{ url, path }` |

---

## Workouts

| Method | Path | Auth | Notes |
|--------|------|------|--------|
| GET | `workouts/list.php` | Yes | `{ workouts: [{ id, name, type, date, durationMin, calories, notes }] }` |
| POST | `workouts/create.php` | Yes + CSRF | JSON: `name`/`title`, `type`/`category`, `durationMin`, `date`, `notes`, optional `calories` |
| PUT / POST | `workouts/update.php` | Yes + CSRF | JSON: `id`, same fields as create |
| DELETE / POST | `workouts/delete.php` | Yes + CSRF | JSON or query: `id` |

---

## Routines

| Method | Path | Auth | Notes |
|--------|------|------|--------|
| GET | `routines/list.php` | Yes | `{ routines: [{ id, title, description, exercises: [{ name, sets, reps, exercise_id }] }] }` |
| POST | `routines/create.php` | Yes + CSRF | JSON: optional `id` (upsert), `title`, `description`, `exercises: [{ name?, exercise_id?, sets, reps }]` |
| DELETE / POST | `routines/delete.php` | Yes + CSRF | `id` in JSON or query string |

---

## Exercises (catalog)

| Method | Path | Auth | Notes |
|--------|------|------|--------|
| GET | `exercises/list.php` | No | Query: `q`, `muscle` / `muscle_group` — returns `{ exercises }` |

---

## Meals

| Method | Path | Auth | Notes |
|--------|------|------|--------|
| GET | `meals/list.php` | Yes | `{ meals }` rows from `meal_plans` |
| POST | `meals/create.php` | Yes + CSRF | JSON: `plan_date`, `title`, `breakfast`, `lunch`, `dinner`, `calories`, `protein_g`, `carbs_g`, `fats_g` (upsert by user + date) |

---

## Progress

| Method | Path | Auth | Notes |
|--------|------|------|--------|
| GET | `progress/list.php` | Yes | `{ logs }` |
| POST | `progress/create.php` | Yes + CSRF | JSON: `log_date`/`date`, `weight`, `body_fat`, `bmi`, `notes` |

---

## Coaches & bookings

| Method | Path | Auth | Notes |
|--------|------|------|--------|
| GET | `coaches/list.php` | No | `{ coaches }` |
| POST | `coaches/book.php` | Yes + CSRF | JSON: `coach_id`, `booking_date` (SQL datetime string), `notes` |

---

## Notifications

| Method | Path | Auth | Notes |
|--------|------|------|--------|
| GET | `notifications/list.php` | Yes | `{ notifications }` |
| PUT / POST | `notifications/read.php` | Yes + CSRF | JSON or query `id` — if omitted, marks all as read |

---

## Manual test checklist

1. Import `schema.sql` + `seed.sql`.
2. `POST auth/login.php` as demo user → receive `csrf_token`.
3. `GET auth/me.php` → stats populated.
4. CRUD a workout; list updates.
5. Create/update routine with nested exercises; delete routine.
6. `POST coaches/book.php` → new booking + notification.
7. `GET notifications/list.php` then `PUT notifications/read.php`.
8. Upload avatar via `users/upload-avatar.php`.
