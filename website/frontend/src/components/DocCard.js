import React from 'react';
import { Link } from 'react-router-dom';
import { getCategoryMeta, formatCategory } from '../utils/categories';

export default function DocCard({ doc }) {
  const meta = getCategoryMeta(doc.category);
  const isTopicCard = doc.fileType === 'folder' || doc.isTopic;

  return (
    <Link
      to={`/doc/${encodeURIComponent(doc.path)}`}
      className={`premium-doc-card fade-up ${isTopicCard ? 'topic' : 'file'}`}
      style={{ '--accent-color': meta.color }}
    >
      <div className="card-glass-glow"></div>
      
      {/* Type pill */}
      <div className="card-type-row">
        <span className={`type-tag ${doc.fileType}`}>
          {isTopicCard ? '◈ ARCHIVE' : doc.fileType === 'python' || doc.fileType === 'py' ? '🐍 PYTHON' : '📝 DOCS'}
        </span>
      </div>

      <h3>{doc.title}</h3>

      {/* Excerpt */}
      {doc.excerpt && <p>{doc.excerpt}</p>}

      {/* Topic: code count */}
      {isTopicCard && doc.codeCount > 0 && (
        <div className="code-count">⬡ {doc.codeCount} scripts</div>
      )}

      {/* Meta row */}
      <div className="doc-card-meta">
        <span
          className="cat-badge"
          style={{
            color: meta.color,
            borderColor: `${meta.color}33`,
            background: `${meta.color}12`,
          }}
        >
          {meta.icon} {formatCategory(doc.category)}
        </span>
        {doc.wordCount > 0 && (
          <span>{doc.wordCount.toLocaleString()} words</span>
        )}
        {doc.codeBlockCount > 0 && (
          <span>⬡ {doc.codeBlockCount}</span>
        )}
      </div>
    </Link>
  );
}