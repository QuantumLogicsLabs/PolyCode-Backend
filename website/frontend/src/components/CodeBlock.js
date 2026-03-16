import React, { useState } from 'react';
import { Prism as SyntaxHighlighter } from 'react-syntax-highlighter';
import { vscDarkPlus } from 'react-syntax-highlighter/dist/esm/styles/prism';

const customStyle = {
  ...vscDarkPlus,
  'pre[class*="language-"]': {
    ...vscDarkPlus['pre[class*="language-"]'],
    background: 'transparent',
    margin: 0,
    padding: '20px',
    fontSize: '0.845rem',
    lineHeight: '1.75',
  },
  'code[class*="language-"]': {
    ...vscDarkPlus['code[class*="language-"]'],
    background: 'transparent',
  },
};

export default function CodeBlock({ code, language = 'python', filename }) {
  const [copied, setCopied] = useState(false);

  const handleCopy = () => {
    navigator.clipboard.writeText(code).then(() => {
      setCopied(true);
      setTimeout(() => setCopied(false), 2000);
    });
  };

  const langMap = { python: 'py', javascript: 'js', bash: 'sh', sql: 'sql' };
  const displayLang = langMap[language] || language;

  return (
    <div className="code-block">
      <div className="code-block-header">
        <div className="dots">
          <span className="dot dot-red" />
          <span className="dot dot-amber" />
          <span className="dot dot-green" />
        </div>

        <span className="lang-label">
          {filename ? filename : displayLang}
        </span>

        <button
          className={`copy-trigger ${copied ? 'active' : ''}`}
          onClick={handleCopy}
          aria-label="Copy code"
        >
          <span className="copy-label">{copied ? '✓ COPIED' : 'COPY'}</span>
          <div className="copy-glow"></div>
        </button>
      </div>

      <SyntaxHighlighter
        language={language}
        style={customStyle}
        showLineNumbers
        lineNumberStyle={{
          color: 'rgba(255,255,255,0.12)',
          fontSize: '0.7rem',
          minWidth: '2.5em',
          paddingRight: '1em',
          userSelect: 'none',
        }}
        wrapLongLines={false}
      >
        {code}
      </SyntaxHighlighter>
    </div>
  );
}