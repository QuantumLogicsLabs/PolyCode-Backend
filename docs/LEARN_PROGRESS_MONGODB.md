# Learn progress: single `learner_progress` collection

## Layout

Auth users stay in `users`. All learner data for a user lives in **one Mongo document**
in the collection `learner_progress` (model `LearnerProgress`).

```js
{
  userId: ObjectId,           // unique
  courses: [{
    courseId,
    completedLessons, savedCode, notes,
    lessonEngagement,         // read / confidence / quiz / challenge / lastTab
    bookmarks, lastLessonId,
    totalXp, totalMinutesSpent, currentStreak, lastActiveDate,
  }],
  dailyXp: { days: [...], totalXp },
  annotations: [{ courseId, lessonId, tab, strokes, labels, updatedAt }],
}
```

### Size limits

- Per-annotation payload: **100KB** (strokes may be downsampled).
- Whole learner document: **10MB** soft ceiling (Mongo hard limit is 16MB).

## When is `learner_progress` created?

Documents are **not** created on GET / login / last-lesson peek. They are created on the
first real write for that user:

| Write | Creates doc? |
|-------|----------------|
| `POST .../progress/complete` | Yes (`createIfMissing: true`) |
| `POST .../code`, `/note`, `/bookmark` | Yes |
| `POST /auth/learn/progress/merge` (login merge with data) | Yes when payload has completions |
| Annotation `PUT` | Yes |
| `GET .../progress`, daily-xp read, annotation get | No — returns empty shell if missing |
| `POST .../last-lesson`, `/time`, `/engagement` | No create (`createIfMissing: false`) |

After the first create, later completes append to `courses[].completedLessons`,
recalculate `courses[].totalXp`, and apply the same XP into `dailyXp.days` via
`dailyXpService.applyLessonXp` in the same save.

## Hub XP vs daily XP

| Surface | Source |
|---------|--------|
| **Hub XP / progress bar** | Client `completedMap` × curriculum `lesson.xp` (local + remote merge) |
| **Course `totalXp` in Mongo** | Sum of completed lesson XP for that course |
| **Profile / Learning activity graph** | `dailyXp.days` (and dashboard overview totals) |

Completing a lesson while signed in should update all three. The shared hook
`useCourseProgress` keeps optimistic local completions so hub counters do not
reset to 0 when GET returns an empty shell before the first DB write.

## Certificates

| Surface | Where | When |
|---------|--------|------|
| **Hub certificate** | Every learn course hub (`CourseCertificate`) | Shown when `completedCount >= totalLessons` |
| **Profile featured certificates** | Profile page only for `PROFILE_FEATURED_TRACKS` | Same completion rule for the five featured tracks: `oops-cpp`, `pointers-cpp`, `numpy-py`, `pandas-py`, `fastapi-py` |

Hub certs are local UI (stable client-generated cert ID + QR to verify). Profile
certs stay limited to featured tracks by design; expanding that list is out of
scope unless product asks for all 49.

## API (unchanged shapes)

Bearer JWT required unless noted:

| Method | Path | Purpose |
|--------|------|---------|
| GET | `/api/auth/learn/progress` | List my courses (embedded slice) |
| GET | `/api/auth/learn/dashboard` | Learner dashboard overview |
| POST | `/api/auth/learn/progress/merge` | Merge browser local progress on login |
| GET | `/api/auth/learn/:courseId/progress` | Load one course |
| POST | `.../complete`, `/last-lesson`, `/code`, `/note`, `/bookmark`, `/time`, `/engagement` | Course writes |
| GET/PUT | `/api/auth/learn/:courseId/annotations/:lessonId?tab=` | Private annotations |
| POST | `/api/auth/learn/annotations/merge` | Merge local annotations on login |
| GET | `/api/auth/username/:username/learn/progress` | Public courses (no code/notes) |
| GET | `/api/auth/username/:username/learn/dashboard` | Public dashboard + activity graph day counts (no engagement counts) |
| GET | `/api/auth/polycoder/:username/progress` | Full polycoder aggregate |
| GET/POST | `/api/auth/progress/daily-xp*` | Daily XP heatmap |

Legacy OOP aliases under `/api/auth/learn/oops-cpp/progress/*` still work.

## Guests (localStorage)

Unsigned visitors still use localStorage keys from
`frontend/src/features/learn/shared/courseRegistry.js`.
On login, `mergeLearnProgressOnLogin` uploads completions, engagement, and
annotations into the single learner document.

## Frontend entry points

- `useCourseProgress` — completions + time tracking (optimistic complete; empty GET does not wipe local XP)
- `useLessonReadGate` / `useLessonQuizAttempts` — engagement
- `LessonAnnotator` — annotations
- `useLearnDashboard` / `useProfileLearnProgress` — profile UI
- `CourseCertificate` — hub certificate UI

## Audit / smoke

```bash
# Frontend: registry, hooks, complete wiring, XP UI, hub certs
cd frontend && npm run audit:learn-courses

# Backend: completeLesson creates learner_progress + dailyXp for 2 courses
cd backend && npm run smoke:learn-xp
```

## Still local-only (by design)

- Theme / IDE chrome / FAB position / annotation color prefs
- Playground files + run history

## Clean database

Wipe the entire MongoDB database (all collections):

```bash
cd backend
npm run clean-db -- --yes
```

After wipe, register/login again. The app recreates `users` and `learner_progress`
on first real progress write (not on empty GET).
