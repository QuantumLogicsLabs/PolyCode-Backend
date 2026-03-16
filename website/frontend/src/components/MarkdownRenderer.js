import React from 'react';
import ReactMarkdown from 'react-markdown';
import remarkGfm from 'remark-gfm';
import CodeBlock from './CodeBlock';

export default function MarkdownRenderer({ content, fileType, relatedCode, title }) {
  if (fileType === 'py' || fileType === 'python') {
    return (
      <div className="doc-body">
        <CodeBlock
          language="python"
          code={content}
          filename={`${title}.py`}
        />
      </div>
    );
  }

  return (
    <div className="doc-body">
      <ReactMarkdown
        remarkPlugins={[remarkGfm]}
        components={{
          code({ node, inline, className, children, ...props }) {
            const match = /language-(\w+)/.exec(className || '');
            const lang = match ? match[1] : 'python';
            const codeStr = String(children).replace(/\n$/, '');

            if (!inline && (match || codeStr.length > 60)) {
              return <CodeBlock language={lang} code={codeStr} />;
            }
            return <code className={className} {...props}>{children}</code>;
          },
          // Open links in new tab
          a({ href, children, ...props }) {
            return <a href={href} target="_blank" rel="noopener noreferrer" {...props}>{children}</a>;
          },
        }}
      >
        {content}
      </ReactMarkdown>

      {relatedCode && relatedCode.length > 0 && (
        <div className="related-code-section">
          <div style={{ textAlign: 'left', marginBottom: '40px', paddingLeft: '20px', borderLeft: '4px solid var(--accent)' }}>
            <span style={{
              fontSize: '0.75rem',
              color: 'var(--text-muted)',
              fontWeight: 700,
              textTransform: 'uppercase',
              letterSpacing: '0.2em',
              display: 'block',
              marginBottom: '8px'
            }}>
              Core Implementation
            </span>
            <h2 style={{ fontSize: '1.6rem', margin: 0, fontWeight: 800 }}>
              {relatedCode.length === 1 ? 'Source Code Reference' : 'Integrated Logic Modules'}
            </h2>
          </div>

          {relatedCode.map((codeDoc, index) => (
            <div key={index} className="related-code-item" style={{ marginBottom: '32px' }}>
              <div style={{
                background: 'rgba(5, 8, 16, 0.5)',
                border: '1px solid var(--border)',
                padding: '0',
                borderRadius: 'var(--radius)'
              }}>
                <CodeBlock
                  language="python"
                  code={codeDoc.content}
                  filename={`${codeDoc.title}.py`}
                />
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
