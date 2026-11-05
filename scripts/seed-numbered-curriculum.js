#!/usr/bin/env node
/**
 * Seeds numbered certificate-course folders (00-certification … 05-mastery)
 * for all languages in backend/data, matching the JavaScript pattern.
 *
 * Usage:
 *   node scripts/seed-numbered-curriculum.js
 *   node scripts/seed-numbered-curriculum.js --force   # overwrite existing lessons
 *   node scripts/seed-numbered-curriculum.js --lang Python
 */

const fs = require("fs");
const path = require("path");
const {
  CERT_LESSONS,
  LANGUAGE_LESSONS,
  JS_EXTRA_LESSONS,
} = require("./curriculum-spec");

const DATA_ROOT = path.join(__dirname, "../data");
const args = process.argv.slice(2);
const FORCE = args.includes("--force");
const LANG_FILTER = args.includes("--lang")
  ? args[args.indexOf("--lang") + 1]
  : null;

const LANG_META = {
  Python: { ext: "py", hello: 'print("Hello, Python!")', comment: "#" },
  Java: {
    ext: "java",
    hello: 'public class Main {\n  public static void main(String[] args) {\n    System.out.println("Hello, Java!");\n  }\n}',
    comment: "//",
  },
  "C++": {
    ext: "cpp",
    hello: '#include <iostream>\nint main() {\n  std::cout << "Hello, C++!" << std::endl;\n  return 0;\n}',
    comment: "//",
  },
  C: {
    ext: "c",
    hello: '#include <stdio.h>\nint main() {\n  printf("Hello, C!\\n");\n  return 0;\n}',
    comment: "//",
  },
  "C#": {
    ext: "cs",
    hello: 'using System;\nclass Program {\n  static void Main() {\n    Console.WriteLine("Hello, C#!");\n  }\n}',
    comment: "//",
  },
  Go: {
    ext: "go",
    hello: 'package main\nimport "fmt"\nfunc main() {\n  fmt.Println("Hello, Go!")\n}',
    comment: "//",
  },
  PHP: {
    ext: "php",
    hello: '<?php\necho "Hello, PHP!";\n',
    comment: "//",
  },
  Ruby: {
    ext: "rb",
    hello: 'puts "Hello, Ruby!"',
    comment: "#",
  },
  Rust: {
    ext: "rs",
    hello: 'fn main() {\n  println!("Hello, Rust!");\n}',
    comment: "//",
  },
  Quantum: { ext: "md", hello: null, comment: "#" },
  "Q#": { ext: "md", hello: null, comment: "//" },
  SQL: { ext: "sql", hello: 'SELECT "Hello, SQL!" AS greeting;', comment: "--" },
  Powershell: {
    ext: "ps1",
    hello: 'Write-Host "Hello, PowerShell!"',
    comment: "#",
  },
  Batchfile: {
    ext: "bat",
    hello: "@echo off\necho Hello, Batch!",
    comment: "REM",
  },
  JavaScript: {
    ext: "js",
    hello: 'console.log("Hello, JavaScript!");',
    comment: "//",
  },
};

function ensureDir(dir) {
  fs.mkdirSync(dir, { recursive: true });
}

function writeIfMissing(filePath, content) {
  if (!FORCE && fs.existsSync(filePath)) return false;
  ensureDir(path.dirname(filePath));
  fs.writeFileSync(filePath, content, "utf8");
  return true;
}

function lessonHeader(lang, moduleId, lessonNum, total, title) {
  const moduleNum = moduleId.slice(0, 2);
  const level = moduleId.replace(/^\d{2}-/, "").replace(/-/g, " ");
  const levelTitle = level.charAt(0).toUpperCase() + level.slice(1);
  return `# Lesson ${lessonNum} — ${title}

**Module ${moduleNum} · ${levelTitle} · Lesson ${lessonNum} of ${total}**
`;
}

function genericLesson(lang, moduleId, num, slug, title, total, meta) {
  const relNext = String(parseInt(num, 10) + 1).padStart(2, "0");
  const isHello = slug === "hello-world";

  let body = `${lessonHeader(lang, moduleId, num, total, title)}

## Learning objectives

- Understand **${title.toLowerCase()}** in ${lang}
- Read and write small examples you can run locally
- Connect this topic to the next lesson in the course

## Overview

${title} is a core topic on the PolyCode **${lang} Certificate Course** path. Work through the examples, then try the exercise before moving on.

## Key concepts

1. **Syntax and structure** — how ${lang} expresses this idea clearly
2. **Common patterns** — what you will see in real projects
3. **Mistakes to avoid** — typical beginner errors and fixes

`;

  if (isHello && meta.hello) {
    body += `## Runnable example

\`\`\`${meta.ext === "java" ? "java" : meta.ext}\n${meta.hello}\n\`\`\`

Save as \`${num}-${slug}.${meta.ext}\` and run it in your environment or the PolyCode Playground.

`;
  } else {
    body += `## Example

\`\`\`${meta.ext === "cpp" ? "cpp" : meta.ext === "cs" ? "csharp" : meta.ext}\n${meta.comment} ${title} — practice sketch\n${sampleCode(lang, slug, meta)}\n\`\`\`

`;
  }

  body += `## Exercise

1. Write a short program that uses today's topic.
2. Change one value and predict the output before running.
3. Explain the result in your own words (2–3 sentences).

## Checkpoint

You are ready for the next lesson when you can solve the exercise without copying the example.

---

**Next:** Continue to lesson ${relNext} in this module.
`;

  return body;
}

function sampleCode(lang, slug, meta) {
  const c = meta.comment;
  if (slug.includes("variable")) {
    if (lang === "Python") return "name = \"Alex\"\nage = 21\nprint(name, age)";
    if (lang === "JavaScript") return 'const name = "Alex";\nconsole.log(name);';
    return `${c} declare and print sample variables`;
  }
  if (slug.includes("loop") || slug.includes("control")) {
    if (lang === "Python") return "for i in range(3):\n    print(i)";
    return `${c} loop example`;
  }
  return `${c} add your code here`;
}

function roadmapContent(lang, lessonsByModule) {
  const lines = [
    `# ${lang} Certificate Course — Learning Roadmap`,
    "",
    `Welcome to the **PolyCode ${lang} Certificate Course**. Follow modules **01 → 05** in order, then complete the final assessment in **Module 00**.`,
    "",
    "**Estimated time:** 8–14 weeks at 5–8 hours per week.",
    "",
    "---",
    "",
    "## Module 00 — Certification",
    "",
    "| # | Lesson | Purpose |",
    "|---|--------|---------|",
    "| 00 | [Course roadmap](00-roadmap.md) | You are here |",
    "| 01 | [Certificate requirements](01-certificate-requirements.md) | Requirements |",
    "| 02 | [Final assessment](02-final-assessment.md) | Exam topics |",
    "",
  ];

  const moduleOrder = [
    "01-beginner",
    "02-intermediate",
    "03-advanced",
    "04-professional",
    "05-mastery",
  ];
  const weekLabels = ["1–2", "3–4", "5–6", "7–9", "10–12"];

  moduleOrder.forEach((modId, idx) => {
    const list = lessonsByModule[modId] || [];
    const label = modId.replace(/^\d{2}-/, "").replace(/-/g, " ");
    lines.push(`## Module ${modId.slice(0, 2)} — ${label.charAt(0).toUpperCase() + label.slice(1)} (Weeks ${weekLabels[idx]})`, "");
    lines.push("| # | Lesson | Level |", "|---|--------|-------|");
    list.forEach(([slug, title], i) => {
      const n = String(i + 1).padStart(2, "0");
      lines.push(
        `| ${n} | [${title}](../${modId}/${n}-${slug}.md) | ${label} |`,
      );
    });
    lines.push("");
  });

  lines.push(
    "## Certificate path",
    "",
    "```",
    "01 Beginner → 02 Intermediate → 03 Advanced → 04 Professional → 05 Mastery → Final Assessment",
    "```",
    "",
    "Good luck — and happy coding!",
  );

  return lines.join("\n");
}

function certRequirements(lang, lessonsByModule) {
  const counts = Object.fromEntries(
    Object.entries(lessonsByModule).map(([k, v]) => [k, v.length]),
  );
  return `# PolyCode ${lang} Certificate — Requirements

Earn the **PolyCode ${lang} Developer Certificate** by completing this course and demonstrating practical skills.

## Requirements

### 1. Complete all modules

| Module | Lessons |
|--------|---------|
| 01 Beginner | ${counts["01-beginner"] || 0} |
| 02 Intermediate | ${counts["02-intermediate"] || 0} |
| 03 Advanced | ${counts["03-advanced"] || 0} |
| 04 Professional | ${counts["04-professional"] || 0} |
| 05 Mastery | ${counts["05-mastery"] || 0} |

### 2. Hands-on projects

Complete **at least 3 projects** across the course (console app, structured program, capstone).

### 3. Final assessment

- Score **70% or higher** on the [final assessment](02-final-assessment.md)
- Review weak topics before retaking

## How to submit

Track progress in your PolyCode profile and complete the assessment when ready.

---

See the [course roadmap](00-roadmap.md) for the full lesson list.
`;
}

function finalAssessment(lang) {
  return `# ${lang} Certificate — Final Assessment

## Format

- **40 questions** — mix of multiple choice and short answer
- **70%** required to pass
- Open-book: you may reference PolyCode lessons

## Topics by module

### Module 01 — Beginner
Syntax, variables, control flow, core data types, basic I/O

### Module 02 — Intermediate
Functions/methods, modules, OOP or structured patterns, error handling

### Module 03 — Advanced
Language-specific advanced features, performance, testing

### Module 04 — Professional
Frameworks, APIs, databases, deployment basics

### Module 05 — Mastery
Algorithms, data structures, capstone design

## Sample questions

1. Explain the difference between a compiled and interpreted language (where applicable).
2. Write a function that returns the largest value in a collection.
3. What is the purpose of version control in professional projects?
4. Describe one security practice from Module 03 or 04.

## Retakes

You may retake after **48 hours** and additional practice.

---

When ready, ask your instructor or use the PolyCode assessment portal.
`;
}

function courseReadme(lang, lessonsByModule) {
  const rows = [
    ["00", "00-certification", "Overview", "Roadmap, requirements, exam"],
    ["01", "01-beginner", "Beginner", `${lessonsByModule["01-beginner"]?.length || 0} lessons`],
    ["02", "02-intermediate", "Intermediate", `${lessonsByModule["02-intermediate"]?.length || 0} lessons`],
    ["03", "03-advanced", "Advanced", `${lessonsByModule["03-advanced"]?.length || 0} lessons`],
    ["04", "04-professional", "Professional", `${lessonsByModule["04-professional"]?.length || 0} lessons`],
    ["05", "05-mastery", "Mastery", `${lessonsByModule["05-mastery"]?.length || 0} lessons`],
  ];

  return `# PolyCode ${lang} Certificate Course

A structured, numbered learning path from **beginner to mastery** — designed for the PolyCode Docs Hub and certificate completion.

## Start here

1. Open **[00-certification / Course roadmap](00-certification/00-roadmap.md)**
2. Begin **Module 01 — Beginner** and follow lessons in order (\`01-slug.md\`, \`02-slug.md\`, …)
3. After Module 05, complete the **[final assessment](00-certification/02-final-assessment.md)**

## Course modules

| Module | Folder | Level | Content |
|--------|--------|-------|---------|
${rows.map((r) => "| " + r[0] + " | `" + r[1] + "` | " + r[2] + " | " + r[3] + " |").join("\n")}

## Legacy content

Older material was moved to **\`../_archive/\`** (not shown in Docs Hub). It is kept for reference only.

Follow the numbered modules above for the certificate learning path.

## Certificate

See [certificate requirements](00-certification/01-certificate-requirements.md).
`;
}

function legacyRedirect(lang) {
  return `# ${lang} — Use the numbered course

The **certificate course** lives in numbered folders:

- \`00-certification/\` — roadmap and exam
- \`01-beginner/\` through \`05-mastery/\`

Open **[data/README.md](../README.md)** or **[00-certification/00-roadmap.md](../00-certification/00-roadmap.md)** to start.

This legacy folder is kept for extra examples and projects.
`;
}

function seedLanguage(lang) {
  const meta = LANG_META[lang];
  if (!meta) {
    console.warn(`Skip ${lang}: no LANG_META`);
    return { created: 0, skipped: 0 };
  }

  const lessonsByModule = LANGUAGE_LESSONS[lang];
  if (!lessonsByModule) {
    console.warn(`Skip ${lang}: no lesson spec`);
    return { created: 0, skipped: 0 };
  }

  const base = path.join(DATA_ROOT, lang, "data");
  if (!fs.existsSync(base)) {
    console.warn(`Skip ${lang}: no data folder`);
    return { created: 0, skipped: 0 };
  }

  let created = 0;
  let skipped = 0;

  const track = (filePath, content) => {
    if (writeIfMissing(filePath, content)) {
      created++;
      return;
    }
    skipped++;
  };

  track(path.join(base, "README.md"), courseReadme(lang, lessonsByModule));

  const certDir = path.join(base, "00-certification");
  track(path.join(certDir, "00-roadmap.md"), roadmapContent(lang, lessonsByModule));
  track(
    path.join(certDir, "01-certificate-requirements.md"),
    certRequirements(lang, lessonsByModule),
  );
  track(
    path.join(certDir, "02-final-assessment.md"),
    finalAssessment(lang),
  );

  for (const [moduleId, lessons] of Object.entries(lessonsByModule)) {
    const modDir = path.join(base, moduleId);
    const total = String(lessons.length).padStart(2, "0");
    lessons.forEach(([slug, title], i) => {
      const num = String(i + 1).padStart(2, "0");
      const mdPath = path.join(modDir, `${num}-${slug}.md`);
      track(
        mdPath,
        genericLesson(lang, moduleId, num, slug, title, total, meta),
      );

      if (slug === "hello-world" && meta.hello && meta.ext !== "md") {
        const codePath = path.join(modDir, `${num}-${slug}.${meta.ext}`);
        const codeContent =
          meta.ext === "php"
            ? meta.hello
            : `/**\n * ${lang} — Hello World\n * PolyCode Certificate Course\n */\n${meta.hello}\n`;
        track(codePath, codeContent);
      }
    });
  }

  const legacyDirs = ["tutorials", "learning_curriculum", "basics", "getting_started"];
  legacyDirs.forEach((dir) => {
    const legacyPath = path.join(base, dir);
    if (fs.existsSync(legacyPath)) {
      track(path.join(legacyPath, "00-START-HERE.md"), legacyRedirect(lang));
    }
  });

  return { created, skipped };
}

function seedJavaScriptExtras() {
  const base = path.join(DATA_ROOT, "JavaScript", "data");
  const meta = LANG_META.JavaScript;
  let created = 0;
  let skipped = 0;

  const track = (filePath, content) => {
    if (writeIfMissing(filePath, content)) created++;
    else skipped++;
  };

  for (const [moduleId, extras] of Object.entries(JS_EXTRA_LESSONS)) {
    const modDir = path.join(base, moduleId);
    const existingCount = fs.existsSync(modDir)
      ? fs.readdirSync(modDir).filter((f) => f.endsWith(".md")).length
      : 0;

    extras.forEach(([fileName, title]) => {
      const num = fileName.slice(0, 2);
      const slug = fileName.slice(3);
      const mdPath = path.join(modDir, `${fileName}.md`);
      track(
        mdPath,
        genericLesson(
          "JavaScript",
          moduleId,
          num,
          slug,
          title,
          String(existingCount + extras.length).padStart(2, "0"),
          meta,
        ),
      );

      if (slug.includes("fetch") || slug.includes("strings") || slug.includes("debugging")) {
        const jsSamples = {
          "strings-and-template-literals": `const name = "PolyCode";\nconst greeting = \`Hello, \${name}!\`;\nconsole.log(greeting);\nconsole.log("Length:", name.length);`,
          "debugging-and-devtools": `function divide(a, b) {\n  debugger; // pause in DevTools\n  return a / b;\n}\nconsole.log(divide(10, 2));`,
          "fetch-api": `async function loadUsers() {\n  const res = await fetch("https://jsonplaceholder.typicode.com/users/1");\n  const data = await res.json();\n  console.log(data.name);\n}\nloadUsers();`,
          "json-and-apis": `const payload = { title: "Learn JS", done: false };\nconsole.log(JSON.stringify(payload));\nconsole.log(JSON.parse('{"a":1}'));`,
        };
        if (jsSamples[slug]) {
          track(
            path.join(modDir, `${fileName}.js`),
            `/**\n * ${title}\n */\n${jsSamples[slug]}\n`,
          );
        }
      }
    });
  }

  const beginnerDir = path.join(base, "01-beginner");
  const beginnerJs = {
    "01-introduction": `console.log("Welcome to JavaScript!");\nconsole.log("Run this in Node or the browser console.");`,
    "02-javascript-basics": `// Statements end with semicolons (optional but recommended)\nlet x = 5;\nconst PI = 3.14;\nconsole.log(x, PI);`,
    "04-variables-and-types": `let count = 10;\nlet label = "items";\nlet active = true;\nconsole.log(typeof count, typeof label, typeof active);`,
    "05-control-flow-and-loops": `for (let i = 1; i <= 3; i++) {\n  console.log("step", i);\n}\nlet n = 0;\nwhile (n < 2) { console.log(n); n++; }`,
    "06-functions-basics": `function add(a, b) {\n  return a + b;\n}\nconst square = (n) => n * n;\nconsole.log(add(2, 3), square(4));`,
    "07-arrays": `const nums = [1, 2, 3];\nnums.push(4);\nconsole.log(nums.map((n) => n * 2));`,
    "08-objects": `const user = { name: "Sam", role: "student" };\nconsole.log(user.name);\nuser.level = "beginner";\nconsole.log(user);`,
  };

  Object.entries(beginnerJs).forEach(([baseName, code]) => {
    track(
      path.join(beginnerDir, `${baseName}.js`),
      `/**\n * Companion code for ${baseName}\n */\n${code}\n`,
    );
  });

  const jsReadme = path.join(base, "README.md");
  if (fs.existsSync(jsReadme) && (FORCE || !fs.readFileSync(jsReadme, "utf8").includes("10 lessons"))) {
    const content = fs.readFileSync(jsReadme, "utf8")
      .replace("| 01 | `01-beginner` | Beginner | 8 lessons |", "| 01 | `01-beginner` | Beginner | 10 lessons |")
      .replace("| 02 | `02-intermediate` | Intermediate | 5 lessons |", "| 02 | `02-intermediate` | Intermediate | 7 lessons |")
      .replace("- **Module 01 — Beginner** (8 lessons)", "- **Module 01 — Beginner** (10 lessons)")
      .replace("- **Module 02 — Intermediate** (5 lessons)", "- **Module 02 — Intermediate** (7 lessons)");
    fs.writeFileSync(jsReadme, content, "utf8");
    created++;
  }

  return { created, skipped };
}

function main() {
  const langs = fs
    .readdirSync(DATA_ROOT, { withFileTypes: true })
    .filter((d) => d.isDirectory())
    .map((d) => d.name)
    .filter((name) => name !== "node_modules");

  const targets = LANG_FILTER ? [LANG_FILTER] : langs.filter((l) => l !== "JavaScript");

  console.log(`Seeding numbered curriculum (force=${FORCE})...\n`);

  let totalCreated = 0;
  let totalSkipped = 0;

  for (const lang of targets) {
    const { created, skipped } = seedLanguage(lang);
    console.log(`${lang}: +${created} files (${skipped} skipped)`);
    totalCreated += created;
    totalSkipped += skipped;
  }

  const jsExtra = seedJavaScriptExtras();
  console.log(`JavaScript extras: +${jsExtra.created} files (${jsExtra.skipped} skipped)`);
  totalCreated += jsExtra.created;
  totalSkipped += jsExtra.skipped;

  console.log(`\nDone. Created/updated: ${totalCreated}, skipped: ${totalSkipped}`);
}

main();
