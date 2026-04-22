# PolyCode Complete Organization Blueprint

This document is the implementation guide for reorganizing both projects.

- Scope: `PolyCode-Backend` and `PolyCode-Frontend`
- Constraint: this file lives outside both folders (workspace root)
- Goal: clean architecture, clear ownership, easy scaling

---

## 1) Final Target Workspace Hierarchy

```text
INTERNSHIP/
├─ POLYCODE_ORGANIZATION_PLAN.md
├─ content/
│  └─ data/
│     ├─ Batchfile/
│     ├─ C/
│     ├─ C#/
│     ├─ C++/
│     ├─ Go/
│     ├─ Java/
│     ├─ JavaScript/
│     ├─ PHP/
│     ├─ Powershell/
│     ├─ Python/
│     ├─ Q#/
│     ├─ Ruby/
│     ├─ Rust/
│     └─ SQL/
├─ PolyCode-Backend/
│  ├─ package.json
│  ├─ package-lock.json
│  ├─ README.md
│  ├─ .env
│  ├─ .env.example
│  ├─ .gitignore
│  ├─ LICENSE
│  ├─ src/
│  │  ├─ server.js
│  │  ├─ app.js
│  │  ├─ config/
│  │  │  ├─ env.js
│  │  │  ├─ cors.js
│  │  │  ├─ rateLimit.js
│  │  │  └─ paths.js
│  │  ├─ modules/
│  │  │  ├─ health/
│  │  │  │  └─ health.route.js
│  │  │  ├─ documents/
│  │  │  │  ├─ documents.route.js
│  │  │  │  ├─ documents.controller.js
│  │  │  │  ├─ documents.service.js
│  │  │  │  ├─ documents.cache.js
│  │  │  │  ├─ documents.tree.js
│  │  │  │  └─ parsers/
│  │  │  │     └─ fileParser.js
│  │  │  └─ playground/
│  │  │     ├─ playground.route.js
│  │  │     ├─ playground.controller.js
│  │  │     └─ playground.service.js
│  │  ├─ shared/
│  │  │  ├─ middleware/
│  │  │  │  ├─ requestLogger.js
│  │  │  │  └─ errorHandler.js
│  │  │  └─ utils/
│  │  │     ├─ fileType.js
│  │  │     └─ path.js
│  │  └─ jobs/
│  │     └─ cacheCleanup.job.js
│  ├─ tests/
│  │  ├─ routes/
│  │  └─ integration/
│  ├─ scripts/
│  │  └─ debug/
│  ├─ runtime/
│  │  └─ tmp/
│  └─ legacy/
│     ├─ Document.model.legacy.js
│     └─ execute.route.legacy.js
└─ PolyCode-Frontend/
   ├─ package.json
   ├─ README.md
   ├─ public/
   │  ├─ index.html
   │  ├─ manifest.json
   │  └─ robots.txt
   └─ src/
      ├─ app/
      │  ├─ App.js
      │  ├─ App.css
      │  ├─ routes.js
      │  ├─ providers.js
      │  ├─ index.css
      │  └─ reportWebVitals.js
      ├─ main/
      │  └─ index.js
      ├─ features/
      │  ├─ navigation/
      │  │  └─ components/
      │  │     ├─ Navbar.js
      │  │     └─ Sidebar.js
      │  ├─ language/
      │  │  └─ pages/
      │  │     └─ LanguageSelectPage.js
      │  ├─ docs/
      │  │  ├─ pages/
      │  │  │  ├─ HomePage.js
      │  │  │  ├─ DocumentPage.js
      │  │  │  ├─ CategoryPage.js
      │  │  │  ├─ SearchPage.js
      │  │  │  └─ StatsPage.js
      │  │  ├─ components/
      │  │  │  ├─ DocCard.js
      │  │  │  ├─ LazyDocCard.js
      │  │  │  ├─ MarkdownRenderer.js
      │  │  │  └─ CodeBlock.js
      │  │  └─ services/
      │  │     └─ api.js
      │  └─ playground/
      │     ├─ pages/
      │     │  ├─ PlaygroundPage.js
      │     │  └─ PlaygroundPage.css
      │     ├─ components/
      │     │  ├─ CodePlayground.js
      │     │  ├─ CodePlayground.css
      │     │  └─ IDE.css
      │     ├─ constants/
      │     │  └─ playgroundStarters.js
      │     ├─ context/
      │     │  └─ PlaygroundContext.js
      │     └─ services/
      │        └─ BrowserExecutor.js
      ├─ shared/
      │  ├─ components/
      │  │  ├─ LazyImage.js
      │  │  ├─ SkeletonLoader.js
      │  │  └─ SkeletonLoader.css
      │  ├─ utils/
      │  │  ├─ categories.js
      │  │  └─ format.js
      │  └─ assets/
      │     └─ logo.svg
      └─ tests/
         ├─ setupTests.js
         └─ app/
            └─ App.test.js
```

---

## 2) Exact File Placement Map (Current -> Target)

### Backend

- `server.js` -> `src/server.js` (+ split bootstrap logic into `src/app.js`)
- `routes/documents.js` ->
  - `src/modules/documents/documents.route.js`
  - `src/modules/documents/documents.controller.js`
  - `src/modules/documents/documents.service.js`
  - `src/modules/documents/documents.cache.js`
  - `src/modules/documents/documents.tree.js`
  - python execution parts -> `src/modules/playground/playground.service.js`
- `routes/execute.js` -> `src/modules/playground/playground.route.js` (or `legacy/execute.route.legacy.js` if replaced)
- `utils/fileParser.js` -> `src/modules/documents/parsers/fileParser.js`
- `models/Document.js` -> `legacy/Document.model.legacy.js` (then remove after confirmation)
- `test-route.js` -> `tests/routes/pathPattern.spec.js` (or `scripts/debug/test-route.js`)
- `tmp/*` -> `runtime/tmp/*`
- `data/*` -> `../content/data/*` (outside backend)

### Frontend

- `src/index.js` -> `src/main/index.js`
- `src/App.js` -> `src/app/App.js`
- `src/App.css` -> `src/app/App.css`
- `src/index.css` -> `src/app/index.css`
- `src/reportWebVitals.js` -> `src/app/reportWebVitals.js`

- `src/pages/HomePage.js` -> `src/features/docs/pages/HomePage.js`
- `src/pages/DocumentPage.js` -> `src/features/docs/pages/DocumentPage.js`
- `src/pages/CategoryPage.js` -> `src/features/docs/pages/CategoryPage.js`
- `src/pages/SearchPage.js` -> `src/features/docs/pages/SearchPage.js`
- `src/pages/StatsPage.js` -> `src/features/docs/pages/StatsPage.js`
- `src/pages/LanguageSelectPage.js` -> `src/features/language/pages/LanguageSelectPage.js`
- `src/pages/PlaygroundPage.js` -> `src/features/playground/pages/PlaygroundPage.js`
- `src/pages/PlaygroundPage.css` -> `src/features/playground/pages/PlaygroundPage.css`

- `src/components/Navbar.js` -> `src/features/navigation/components/Navbar.js`
- `src/components/Sidebar.js` -> `src/features/navigation/components/Sidebar.js`
- `src/components/DocCard.js` -> `src/features/docs/components/DocCard.js`
- `src/components/LazyDocCard.js` -> `src/features/docs/components/LazyDocCard.js`
- `src/components/MarkdownRenderer.js` -> `src/features/docs/components/MarkdownRenderer.js`
- `src/components/CodeBlock.js` -> `src/features/docs/components/CodeBlock.js`

- `src/components/CodePlayground.js` -> `src/features/playground/components/CodePlayground.js`
- `src/components/CodePlayground.css` -> `src/features/playground/components/CodePlayground.css`
- `src/components/IDE.css` -> `src/features/playground/components/IDE.css`

- `src/constants/playgroundStarters.js` -> `src/features/playground/constants/playgroundStarters.js`
- `src/context/PlaygroundContext.js` -> `src/features/playground/context/PlaygroundContext.js`
- `src/utils/BrowserExecutor.js` -> `src/features/playground/services/BrowserExecutor.js`
- `src/utils/api.js` -> `src/features/docs/services/api.js`

- `src/components/LazyImage.js` -> `src/shared/components/LazyImage.js`
- `src/components/SkeletonLoader.js` -> `src/shared/components/SkeletonLoader.js`
- `src/components/SkeletonLoader.css` -> `src/shared/components/SkeletonLoader.css`
- `src/utils/categories.js` -> `src/shared/utils/categories.js`
- `src/utils/format.js` -> `src/shared/utils/format.js`
- `src/logo.svg` -> `src/shared/assets/logo.svg`

- `src/setupTests.js` -> `src/tests/setupTests.js`
- `src/App.test.js` -> `src/tests/app/App.test.js`

---

## 3) Implementation Sequence (Safe Order)

1. Create target folders first.
2. Move files without changing logic.
3. Fix all imports after each move batch.
4. Start backend and frontend to verify no runtime break.
5. Split backend monolith route (`documents.js`) into route/controller/service/cache/tree.
6. Move data corpus out of backend into `content/data` and update env path config.
7. Archive legacy files and remove only after final validation.
8. Update READMEs with new architecture and run commands.

---

## 4) Import Update Rules

- Keep absolute behavior unchanged while moving files.
- Prefer feature-local relative imports inside each feature.
- For shared modules, import from `src/shared/...`.
- Do not rename symbols and paths in the same commit as major moves if avoidable.

---

## 5) Validation Checklist After Reorganization

### Backend

- `npm run dev` starts successfully.
- Health endpoint works: `/api/health`.
- Documents endpoints work for list/tree/categories/stats/doc-by-path.
- Playground execution endpoint still works.
- Runtime temp files are created under `runtime/tmp`.

### Frontend

- `npm start` starts successfully.
- Routing works:
  - `/`
  - `/hub`
  - `/doc/*`
  - `/category/*`
  - `/search`
  - `/playground`
- Sidebar tree loads documents.
- Playground executes in browser/server mode as before.
- No broken CSS imports after moves.

---

## 6) What To Keep Stable During Refactor

- API response shapes must stay unchanged.
- Existing route URLs must stay unchanged.
- Existing React page URLs must stay unchanged.
- Existing environment variables should continue to work.

---

## 7) Recommended Follow-Up (After Structure Is Done)

- Add ESLint import ordering and path lint rules.
- Add backend tests for documents and playground modules.
- Add frontend smoke tests for route rendering.
- Introduce aliases (optional) for cleaner imports.

---

## 8) One-Time Notes

- `models/Document.js` is currently a compatibility stub; keep it in `legacy/` until fully removed.
- `routes/execute.js` overlaps with execution logic in `documents.js`; consolidate under `modules/playground`.
- `data/` is content, not service code, so it should be outside backend app code lifecycle.
