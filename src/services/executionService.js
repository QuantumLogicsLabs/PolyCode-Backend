const fs = require("fs").promises;
const os = require("os");
const path = require("path");
const crypto = require("crypto");
const { spawn } = require("child_process");
const {
  RUN_TIMEOUT_MS,
  PYTHON_DATA_PATH,
  RUNTIME_TMP_PATH,
  MAX_EXEC_OUTPUT_BYTES,
} = require("../config/constants");

const MATPLOTLIB_PRELUDE = `
import os
os.environ.setdefault("MPLBACKEND", "Agg")
import matplotlib
matplotlib.use("Agg")
import io, base64

_POLYCODE_FIGURES = []

def _polycode_setup_matplotlib_show():
    import matplotlib.pyplot as plt
    def _capture_show(*args, **kwargs):
        fig = plt.gcf()
        if fig.get_axes():
            buf = io.BytesIO()
            fig.savefig(buf, format="png", bbox_inches="tight", dpi=110)
            _POLYCODE_FIGURES.append(base64.b64encode(buf.getvalue()).decode("ascii"))
        plt.close(fig)
    plt.show = _capture_show

_polycode_setup_matplotlib_show()
`;

const MATPLOTLIB_POSTLUDE = `
import json, sys
if "_POLYCODE_FIGURES" in globals() and _POLYCODE_FIGURES:
    sys.stdout.write("\\n__POLYCODE_PLOTS__" + json.dumps(_POLYCODE_FIGURES) + "__\\n")
`;

function codeUsesMatplotlib(source = "") {
  return /(?:^|\n)\s*(?:import|from)\s+matplotlib\b/m.test(source) ||
    /\bmatplotlib\.pyplot\b/.test(source) ||
    /\bplt\.(?:show|plot|scatter|bar|hist|pie|subplots|figure|savefig|annotate)\s*\(/.test(
      source,
    );
}

function wrapPythonWithMatplotlibCapture(code = "") {
  if (!codeUsesMatplotlib(code)) {
    return code;
  }
  return `${MATPLOTLIB_PRELUDE}\n${code}\n${MATPLOTLIB_POSTLUDE}`;
}

/** Skip matplotlib prelude when the server Python has no matplotlib wheel. */
function wrapPythonForExecution(code = "") {
  if (!codeUsesMatplotlib(code)) {
    return code;
  }
  return `${MATPLOTLIB_AVAILABILITY_GUARD}\n${wrapPythonWithMatplotlibCapture(code)}`;
}

const MATPLOTLIB_AVAILABILITY_GUARD = `
try:
    import matplotlib  # noqa: F401
    _POLYCODE_MPL_OK = True
except ModuleNotFoundError:
    _POLYCODE_MPL_OK = False

if not _POLYCODE_MPL_OK:
    import sys
    sys.stderr.write("Matplotlib is not installed on this server Python.\\n")
    sys.exit(1)
`;

let resolvedPythonCommand = null;

function appendWithCap(current, chunk) {
  if (current.length >= MAX_EXEC_OUTPUT_BYTES) return current;
  const next = current + chunk;
  return next.length > MAX_EXEC_OUTPUT_BYTES
    ? next.slice(0, MAX_EXEC_OUTPUT_BYTES)
    : next;
}

/**
 * Run a command using spawn and wait for it to spawn
 * @param {string} command - Command to run
 * @param {string[]} args - Command arguments
 * @param {Object} options - Spawn options
 * @returns {Promise} Child process or error
 */
function runSpawn(command, args = [], options = {}) {
  return new Promise((resolve, reject) => {
    const child = spawn(command, args, options);
    child.once("error", reject);
    child.once("spawn", () => resolve(child));
  });
}

/**
 * Resolve Python command on system
 * @returns {Promise<string>} Python command string
 */
async function resolvePythonCommand() {
  if (resolvedPythonCommand) return resolvedPythonCommand;

  const candidates =
    process.platform === "win32"
      ? [process.env.PYTHON_EXECUTABLE, "py -3", "python", "python3"]
      : [process.env.PYTHON_EXECUTABLE, "python3", "python"];

  for (const candidate of candidates.filter(Boolean)) {
    const [cmd, ...args] = candidate.split(" ");
    try {
      const probe = await runSpawn(cmd, [...args, "--version"], {
        stdio: ["ignore", "pipe", "pipe"],
      });
      await new Promise((resolve, reject) => {
        probe.once("exit", (code) =>
          code === 0 ? resolve() : reject(new Error("non-zero exit")),
        );
        probe.once("error", reject);
      });
      resolvedPythonCommand = candidate;
      return resolvedPythonCommand;
    } catch (_) {
      // Try next candidate
    }
  }
  throw new Error(
    "No Python runtime found on server. Install Python or set PYTHON_EXECUTABLE.",
  );
}

/**
 * Execute Python code with auto-input handling
 * @param {string} code - Python code to execute
 * @param {string} stdin - Standard input
 * @returns {Promise<Object>} Execution result with stdout, stderr, error, exitCode
 */
async function executePythonCode(code, stdin = "") {
  const command = await resolvePythonCommand();
  const [cmd, ...baseArgs] = command.split(" ");

  const autoInputPrelude = `
import builtins, re

def __polycode_auto_input(prompt=''):
    p = '' if prompt is None else str(prompt).lower()
    # yes/no prompts
    if 'y/n' in p or 'y / n' in p or re.search(r'\\by/n\\b', p):
        return 'n'
    # menu/choice prompts
    if 'select' in p or 'choice' in p or 'option' in p or re.search(r'\\(1\\s*-\\s*\\d+\\)', p):
        return '1'
    # common values
    if 'password' in p:
        return 'password'
    if 'name' in p:
        return 'Bob'
    if 'id' in p:
        return '1'
    if 'url' in p:
        return 'http://example.com'
    if 'command' in p:
        return 'echo hello'
    if 'file' in p or 'filename' in p or 'path' in p:
        return 'input.txt'
    if 'directory' in p or 'folder' in p:
        return '.'
    if 'target host' in p or 'host' in p:
        return 'localhost'
    if 'port' in p:
        return '80'
    if 'age' in p:
        return '20'
    # numeric-ish
    if re.search(r'(age|hours|minutes|rpm|degrees|score|rate|amount|quantity|number)', p):
        return '0'
    return ''

builtins.input = __polycode_auto_input
`;

  const prelude = stdin.trim() ? "" : autoInputPrelude;
  const args = [...baseArgs, "-c", `${prelude}\n${wrapPythonForExecution(code)}`];

  return new Promise(async (resolve, reject) => {
    let child;
    try {
      child = await runSpawn(cmd, args, {
        cwd: PYTHON_DATA_PATH,
        env: { ...process.env, PYTHONIOENCODING: "utf-8" },
        stdio: ["pipe", "pipe", "pipe"],
      });
    } catch (e) {
      reject(e);
      return;
    }

    let stdout = "";
    let stderr = "";
    const timer = setTimeout(() => {
      child.kill("SIGKILL");
      reject(new Error(`Python execution timed out after ${RUN_TIMEOUT_MS}ms`));
    }, RUN_TIMEOUT_MS);

    child.stdout.on("data", (chunk) => {
      stdout = appendWithCap(stdout, chunk.toString());
    });
    child.stderr.on("data", (chunk) => {
      stderr = appendWithCap(stderr, chunk.toString());
    });
    child.on("error", (e) => {
      clearTimeout(timer);
      reject(e);
    });
    child.on("close", (code) => {
      clearTimeout(timer);
      resolve({
        stdout: stdout.trimEnd(),
        stderr: stderr.trimEnd(),
        error:
          code === 0
            ? null
            : stderr.trimEnd() || `Python exited with code ${code}`,
        exitCode: code,
      });
    });

    if (stdin) child.stdin.write(stdin);
    child.stdin.end();
  });
}

/**
 * Execute JavaScript code
 * @param {string} code - JavaScript code to execute
 * @returns {Promise<Object>} Execution result
 */
async function executeJavaScriptCode(code) {
  const runtimeDir = await getWritableRuntimeDir();
  const filename = `run_${crypto.randomBytes(8).toString("hex")}.js`;
  const filepath = path.join(runtimeDir, filename);

  await fs.writeFile(filepath, code, "utf8");

  return new Promise(async (resolve, reject) => {
    let child;
    try {
      child = await runSpawn("node", [filepath], {
        cwd: runtimeDir,
        env: { ...process.env },
        stdio: ["pipe", "pipe", "pipe"],
      });
    } catch (e) {
      await fs.unlink(filepath).catch(() => {});
      reject(e);
      return;
    }

    let stdout = "";
    let stderr = "";
    const timer = setTimeout(() => {
      child.kill("SIGKILL");
    }, RUN_TIMEOUT_MS);

    child.stdout.on("data", (chunk) => {
      stdout = appendWithCap(stdout, chunk.toString());
    });
    child.stderr.on("data", (chunk) => {
      stderr = appendWithCap(stderr, chunk.toString());
    });

    child.on("error", async (e) => {
      clearTimeout(timer);
      await fs.unlink(filepath).catch(() => {});
      reject(e);
    });

    child.on("close", async (code) => {
      clearTimeout(timer);
      await fs.unlink(filepath).catch(() => {});
      resolve({
        stdout: stdout.trimEnd(),
        stderr: stderr.trimEnd(),
        error:
          code === 0
            ? null
            : stderr.trimEnd() || `Node exited with code ${code}`,
        exitCode: code,
      });
    });
  });
}

/**
 * Execute Java code
 * @param {string} code - Java code to execute (class must be named 'Solution')
 * @returns {Promise<Object>} Execution result
 */
async function executeJavaCode(code) {
  const runtimeDir = await getWritableRuntimeDir();
  const id = crypto.randomBytes(4).toString("hex");
  const folderPath = path.join(runtimeDir, `java_${id}`);
  await fs.mkdir(folderPath, { recursive: true });
  
  const filepath = path.join(folderPath, "Solution.java");
  await fs.writeFile(filepath, code, "utf8");

  return new Promise(async (resolve, reject) => {
    // 1. Compile
    try {
      const compile = spawn("javac", ["Solution.java"], { cwd: folderPath });
      let compileErr = "";
      compile.stderr.on("data", (data) => compileErr += data.toString());
      
      compile.on("close", async (code) => {
        if (code !== 0) {
          await fs.rm(folderPath, { recursive: true, force: true }).catch(() => {});
          return resolve({ stdout: "", stderr: compileErr, error: `Compilation Error:\n${compileErr}`, exitCode: code });
        }

        // 2. Run
        const child = spawn("java", ["Solution"], { cwd: folderPath });
        let stdout = "";
        let stderr = "";
        const timer = setTimeout(() => child.kill("SIGKILL"), RUN_TIMEOUT_MS);

        child.stdout.on("data", (chunk) => stdout = appendWithCap(stdout, chunk.toString()));
        child.stderr.on("data", (chunk) => stderr = appendWithCap(stderr, chunk.toString()));

        child.on("close", async (exitCode) => {
          clearTimeout(timer);
          await fs.rm(folderPath, { recursive: true, force: true }).catch(() => {});
          resolve({
            stdout: stdout.trimEnd(),
            stderr: stderr.trimEnd(),
            error: exitCode === 0 ? null : (stderr.trimEnd() || `Java exited with code ${exitCode}`),
            exitCode
          });
        });
      });
    } catch (e) {
      await fs.rm(folderPath, { recursive: true, force: true }).catch(() => {});
      reject(e);
    }
  });
}

/**
 * Execute C++ code
 * @param {string} code - C++ code to execute
 * @returns {Promise<Object>} Execution result
 */
async function executeCppCode(code, stdin = "") {
  if (isServerlessRuntime()) {
    return executeCppViaPiston(code, stdin);
  }

  let runtimeDir;
  try {
    runtimeDir = await getWritableRuntimeDir();
  } catch (_) {
    return executeCppViaPiston(code, stdin);
  }

  const id = crypto.randomBytes(8).toString("hex");
  const sourceFile = path.join(runtimeDir, `run_${id}.cpp`);
  const exeFile = path.join(
    runtimeDir,
    `run_${id}${process.platform === "win32" ? ".exe" : ""}`,
  );

  await fs.writeFile(sourceFile, code, "utf8");

  return new Promise(async (resolve) => {
    const cleanupSource = () => fs.unlink(sourceFile).catch(() => {});
    const cleanupBinary = () => fs.unlink(exeFile).catch(() => {});

    const fallbackToRemote = async () => {
      await cleanupSource();
      await cleanupBinary();
      resolve(await executeCppViaPiston(code, stdin));
    };

    let compile;
    try {
      compile = await runSpawn("g++", ["-o", exeFile, sourceFile], {
        cwd: runtimeDir,
        stdio: ["ignore", "pipe", "pipe"],
      });
    } catch (e) {
      if (e.code === "ENOENT") {
        await fallbackToRemote();
        return;
      }
      await cleanupSource();
      resolve({
        stdout: "",
        stderr: e.message,
        error: e.message,
        exitCode: 1,
      });
      return;
    }

    let compileErr = "";
    compile.stderr.on("data", (data) => {
      compileErr = appendWithCap(compileErr, data.toString());
    });

    compile.on("error", async (e) => {
      if (e.code === "ENOENT") {
        await fallbackToRemote();
        return;
      }
      await cleanupSource();
      resolve({
        stdout: "",
        stderr: e.message,
        error: e.message,
        exitCode: 1,
      });
    });

    compile.on("close", async (compileCode) => {
      if (compileCode !== 0) {
        await cleanupSource();
        await cleanupBinary();
        resolve({
          stdout: "",
          stderr: compileErr,
          error: `Compilation Error:\n${compileErr}`,
          exitCode: compileCode,
        });
        return;
      }

      let child;
      try {
        child = await runSpawn(exeFile, [], {
          cwd: runtimeDir,
          stdio: ["pipe", "pipe", "pipe"],
        });
      } catch (e) {
        await cleanupSource();
        await cleanupBinary();
        resolve({
          stdout: "",
          stderr: e.message,
          error: e.message,
          exitCode: 1,
        });
        return;
      }

      let stdout = "";
      let stderr = "";
      const timer = setTimeout(() => child.kill("SIGKILL"), RUN_TIMEOUT_MS);

      child.stdout.on("data", (chunk) => {
        stdout = appendWithCap(stdout, chunk.toString());
      });
      child.stderr.on("data", (chunk) => {
        stderr = appendWithCap(stderr, chunk.toString());
      });

      child.on("error", async (e) => {
        clearTimeout(timer);
        await cleanupSource();
        await cleanupBinary();
        resolve({
          stdout: "",
          stderr: e.message,
          error: e.message,
          exitCode: 1,
        });
      });

      child.on("close", async (exitCode) => {
        clearTimeout(timer);
        await cleanupSource();
        await cleanupBinary();
        resolve({
          stdout: stdout.trimEnd(),
          stderr: stderr.trimEnd(),
          error:
            exitCode === 0
              ? null
              : stderr.trimEnd() || `C++ exited with code ${exitCode}`,
          exitCode,
        });
      });

      if (stdin) {
        child.stdin.write(stdin.endsWith("\n") ? stdin : `${stdin}\n`);
      }
      child.stdin.end();
    });
  });
}

let resolvedRubyCommand = null;
let rubyCommandProbeDone = false;
const PISTON_API_URL =
  process.env.PISTON_API_URL || "https://emkc.org/api/v2/piston/execute";
const PISTON_CPP_VERSION = process.env.PISTON_CPP_VERSION || "*";
const PROBE_TIMEOUT_MS = 4000;

function isServerlessRuntime() {
  return Boolean(process.env.VERCEL || process.env.AWS_LAMBDA_FUNCTION_NAME);
}

async function ensureRuntimeDir(dir) {
  await fs.mkdir(dir, { recursive: true });
  return dir;
}

async function getWritableRuntimeDir() {
  if (isServerlessRuntime()) {
    return ensureRuntimeDir(path.join(os.tmpdir(), "polycode-runtime"));
  }

  try {
    return await ensureRuntimeDir(RUNTIME_TMP_PATH);
  } catch (_) {
    return ensureRuntimeDir(path.join(os.tmpdir(), "polycode-runtime"));
  }
}

async function waitForChildExit(child, timeoutMs) {
  return new Promise((resolve, reject) => {
    const timer = setTimeout(() => {
      child.kill("SIGKILL");
      reject(new Error("probe timeout"));
    }, timeoutMs);

    const finish = (fn) => (value) => {
      clearTimeout(timer);
      fn(value);
    };

    child.once("exit", finish((code) =>
      code === 0 ? resolve() : reject(new Error("non-zero exit")),
    ));
    child.once("error", finish(reject));
  });
}

async function executeRubyViaPiston(code) {
  const controller = new AbortController();
  const timer = setTimeout(() => controller.abort(), RUN_TIMEOUT_MS);
  const apiKey = process.env.PISTON_API_TOKEN || process.env.PISTON_API_KEY;

  const headers = { "Content-Type": "application/json" };
  if (apiKey) {
    headers.Authorization = `Bearer ${apiKey}`;
  }

  try {
    const response = await fetch(PISTON_API_URL, {
      method: "POST",
      headers,
      body: JSON.stringify({
        language: "ruby",
        version: "3.2.2",
        files: [{ name: "script.rb", content: code }],
      }),
      signal: controller.signal,
    });

    if (!response.ok) {
      const body = await response.text().catch(() => "");
      if (response.status === 401) {
        throw new Error(
          apiKey
            ? "Piston API rejected the configured API key."
            : "Ruby is not installed on this server. Install Ruby, set RUBY_EXECUTABLE, or add PISTON_API_TOKEN for remote execution.",
        );
      }
      throw new Error(
        body.trim() || `Piston API responded with status ${response.status}`,
      );
    }

    const data = await response.json();
    const run = data.run || {};
    const exitCode = run.code ?? 1;
    const stderr = (run.stderr || "").trimEnd();
    const stdout = (run.stdout || "").trimEnd();

    return {
      stdout,
      stderr,
      error:
        exitCode === 0 ? null : stderr || `Ruby exited with code ${exitCode}`,
      exitCode,
    };
  } catch (error) {
    const message =
      error.name === "AbortError"
        ? "Ruby execution timed out."
        : error.message || "Could not reach the Ruby execution service.";
    return {
      stdout: "",
      stderr: message,
      error: message,
      exitCode: 1,
    };
  } finally {
    clearTimeout(timer);
  }
}

/**
 * Compile and run C++ on a remote Piston instance.
 * Used when no local g++ exists (notably serverless, where the bundle is read-only).
 */
async function executeCppViaPiston(code, stdin = "") {
  const controller = new AbortController();
  const timer = setTimeout(() => controller.abort(), RUN_TIMEOUT_MS);
  const apiKey = process.env.PISTON_API_TOKEN || process.env.PISTON_API_KEY;

  const headers = { "Content-Type": "application/json" };
  if (apiKey) {
    headers.Authorization = `Bearer ${apiKey}`;
  }

  try {
    const response = await fetch(PISTON_API_URL, {
      method: "POST",
      headers,
      body: JSON.stringify({
        language: "c++",
        version: PISTON_CPP_VERSION,
        files: [{ name: "main.cpp", content: code }],
        stdin,
      }),
      signal: controller.signal,
    });

    if (!response.ok) {
      const body = await response.text().catch(() => "");
      throw new Error(
        body.trim() || `Piston API responded with status ${response.status}`,
      );
    }

    const data = await response.json();

    // Piston reports compile failures separately from the run stage.
    const compile = data.compile;
    if (compile && compile.code !== 0) {
      const compileErr = (compile.stderr || compile.output || "").trimEnd();
      return {
        stdout: "",
        stderr: compileErr,
        error: `Compilation Error:\n${compileErr}`,
        exitCode: compile.code ?? 1,
      };
    }

    const run = data.run || {};
    const exitCode = run.code ?? 1;
    const stderr = (run.stderr || "").trimEnd();
    const stdout = (run.stdout || "").trimEnd();

    return {
      stdout,
      stderr,
      error:
        exitCode === 0 ? null : stderr || `C++ exited with code ${exitCode}`,
      exitCode,
    };
  } catch (error) {
    const message =
      error.name === "AbortError"
        ? "C++ execution timed out."
        : [
            "C++ execution is unavailable: this server has no local g++ compiler and no reachable remote runner.",
            "Set PISTON_API_URL to a self-hosted Piston instance — the public emkc.org API has been whitelist-only since 2026-02-15.",
            error.message ? `Detail: ${error.message}` : "",
          ]
            .filter(Boolean)
            .join("\n");

    return { stdout: "", stderr: message, error: message, exitCode: 1 };
  } finally {
    clearTimeout(timer);
  }
}

async function resolveRubyCommand() {
  if (resolvedRubyCommand) return resolvedRubyCommand;
  if (rubyCommandProbeDone) {
    throw new Error(
      "No Ruby runtime found on server. Install Ruby or set RUBY_EXECUTABLE.",
    );
  }

  const candidates =
    process.platform === "win32"
      ? [
          process.env.RUBY_EXECUTABLE,
          "C:\\Ruby32-x64\\bin\\ruby.exe",
          "C:\\Ruby33-x64\\bin\\ruby.exe",
          "C:\\Ruby34-x64\\bin\\ruby.exe",
          "ruby",
        ]
      : [process.env.RUBY_EXECUTABLE, "ruby", "ruby3"];

  for (const candidate of candidates.filter(Boolean)) {
    const [cmd, ...args] = candidate.split(" ");
    try {
      const probe = await runSpawn(cmd, [...args, "--version"], {
        stdio: ["ignore", "pipe", "pipe"],
      });
      await waitForChildExit(probe, PROBE_TIMEOUT_MS);
      resolvedRubyCommand = candidate;
      rubyCommandProbeDone = true;
      return resolvedRubyCommand;
    } catch (_) {
      // Try next candidate
    }
  }

  rubyCommandProbeDone = true;
  throw new Error(
    "No Ruby runtime found on server. Install Ruby or set RUBY_EXECUTABLE.",
  );
}

/**
 * Execute Ruby code
 * @param {string} code - Ruby source code
 * @param {string} stdin - Standard input (optional)
 * @returns {Promise<Object>} Execution result
 */
async function executeRubyCode(code, stdin = "") {
  if (isServerlessRuntime()) {
    return executeRubyViaPiston(code);
  }

  let runtimeDir;
  try {
    runtimeDir = await getWritableRuntimeDir();
  } catch (_) {
    return executeRubyViaPiston(code);
  }

  const filename = `run_${crypto.randomBytes(8).toString("hex")}.rb`;
  const filepath = path.join(runtimeDir, filename);

  await fs.writeFile(filepath, code, "utf8");

  let command;
  try {
    command = await resolveRubyCommand();
  } catch (_) {
    await fs.unlink(filepath).catch(() => {});
    return executeRubyViaPiston(code);
  }

  const [cmd, ...baseArgs] = command.split(" ");

  return new Promise(async (resolve, reject) => {
    let child;
    try {
      child = await runSpawn(cmd, [...baseArgs, filepath], {
        cwd: runtimeDir,
        env: { ...process.env, RUBYOPT: "-EUTF-8" },
        stdio: ["pipe", "pipe", "pipe"],
      });
    } catch (e) {
      await fs.unlink(filepath).catch(() => {});
      if (e.code === "ENOENT") {
        resolve(await executeRubyViaPiston(code));
        return;
      }
      reject(e);
      return;
    }

    let stdout = "";
    let stderr = "";
    const timer = setTimeout(() => {
      child.kill("SIGKILL");
    }, RUN_TIMEOUT_MS);

    child.stdout.on("data", (chunk) => {
      stdout = appendWithCap(stdout, chunk.toString());
    });
    child.stderr.on("data", (chunk) => {
      stderr = appendWithCap(stderr, chunk.toString());
    });

    child.on("error", async (e) => {
      clearTimeout(timer);
      await fs.unlink(filepath).catch(() => {});
      reject(e);
    });

    child.on("close", async (exitCode) => {
      clearTimeout(timer);
      await fs.unlink(filepath).catch(() => {});
      resolve({
        stdout: stdout.trimEnd(),
        stderr: stderr.trimEnd(),
        error:
          exitCode === 0
            ? null
            : stderr.trimEnd() || `Ruby exited with code ${exitCode}`,
        exitCode,
      });
    });

    if (stdin) {
      child.stdin.write(stdin.endsWith("\n") ? stdin : `${stdin}\n`);
    }
    child.stdin.end();
  });
}

module.exports = {
  executePythonCode,
  executeJavaScriptCode,
  executeJavaCode,
  executeCppCode,
  executeRubyCode,
  runSpawn,
  resolvePythonCommand,
};
