const express = require('express');
const router = express.Router();
const { spawn } = require('child_process');
const fs = require('fs');
const path = require('path');
const crypto = require('crypto');

// Map languages to file extensions and execution commands
const RUN_CONFIG = {
  javascript: { ext: 'js', cmd: 'node', args: [] },
  js: { ext: 'js', cmd: 'node', args: [] },
  python: { ext: 'py', cmd: 'python', args: [] },
  py: { ext: 'py', cmd: 'python', args: [] },
  python3: { ext: 'py', cmd: 'python3', args: [] }
};

router.post('/', async (req, res) => {
  const { language, code } = req.body;

  if (!language || !code) {
    return res.status(400).json({ error: 'Language and code are required.' });
  }

  const normalizedLang = language.toLowerCase();
  const config = RUN_CONFIG[normalizedLang];

  if (!config) {
    return res.status(400).json({ 
      error: `Unsupported language: ${language}. Currently supported: javascript, python.` 
    });
  }

  // Create a temporary directory for code execution if it doesn't exist
  const tempDir = path.join(__dirname, '..', 'tmp');
  if (!fs.existsSync(tempDir)) {
    fs.mkdirSync(tempDir);
  }

  // Generate unique filename
  const filename = `run_${crypto.randomBytes(8).toString('hex')}.${config.ext}`;
  const filepath = path.join(tempDir, filename);

  try {
    // Write code to temp file
    fs.writeFileSync(filepath, code, 'utf8');

    // Spawn process
    const process = spawn(config.cmd, [...config.args, filepath]);

    let output = '';
    let errorOutput = '';

    // Collect stdout
    process.stdout.on('data', (data) => {
      output += data.toString();
    });

    // Collect stderr
    process.stderr.on('data', (data) => {
      errorOutput += data.toString();
    });

    // Handle timeout (kill process if it takes longer than 5 seconds)
    const timeoutId = setTimeout(() => {
      process.kill();
      errorOutput += '\nError: Execution timed out after 5 seconds.';
    }, 5000);

    // Handle process completion
    process.on('close', (code) => {
      clearTimeout(timeoutId);
      
      // Cleanup temp file
      try {
        if (fs.existsSync(filepath)) {
          fs.unlinkSync(filepath);
        }
      } catch (cleanupErr) {
        console.error('Failed to cleanup temp file:', cleanupErr);
      }

      res.json({
        output: output,
        error: errorOutput,
        exitCode: code
      });
    });

    process.on('error', (err) => {
      clearTimeout(timeoutId);
      res.status(500).json({ error: `Failed to start execution process: ${err.message}` });
      
      try {
        if (fs.existsSync(filepath)) fs.unlinkSync(filepath);
      } catch (e) {}
    });

  } catch (err) {
    res.status(500).json({ error: 'Internal server error during execution setup' });
    try {
      if (fs.existsSync(filepath)) fs.unlinkSync(filepath);
    } catch (e) {}
  }
});

module.exports = router;
