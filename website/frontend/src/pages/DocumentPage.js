import React, { useEffect, useState } from 'react';
import { useParams, Link } from 'react-router-dom';
import { getDocument } from '../utils/api';
import MarkdownRenderer from '../components/MarkdownRenderer';
import { getCategoryMeta, formatCategory } from '../utils/categories';

export default function DocumentPage() {
  const { id } = useParams();
  const [doc, setDoc] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    setLoading(true);
    getDocument(id)
      .then(r => setDoc(r.data))
      .catch(e => setError(e.message))
      .finally(() => setLoading(false));
  }, [id]);

  if (loading) return (
    <div className="loading">
      <div className="spinner" />
      <p>decoding file structure…</p>
    </div>
  );
  if (error) return <div className="error-box">⚠ {error}</div>;
  if (!doc) return null;

  const meta = getCategoryMeta(doc.category);
  const isPython = doc.fileType === 'python' || doc.fileType === 'py';

  return (
    <article className={`fade-up doc-container ${isPython ? 'code-mode' : 'reading-mode'}`}>
      {/* Breadcrumb */}
      <nav className="breadcrumb">
        <Link to="/">root</Link>
        <span className="sep">/</span>
        <Link to={`/category/${doc.category}`}>{doc.category}</Link>
        <span className="sep">/</span>
        <span className="active-path">{doc.title}{isPython ? '.py' : '.md'}</span>
      </nav>

      {/* Header - Only for Non-Python/Documentation files */}
      {!isPython && (
        <div className="doc-header">
          <div className="doc-badge-row">
            <span className="cat-pill" style={{ '--color': meta.color }}>
              {meta.icon} {formatCategory(doc.category)}
            </span>
            <span className="type-pill markdown">📝 Documentation</span>
          </div>
          <h1>{doc.title}</h1>
          <div className="doc-stats">
            {doc.wordCount > 0 && <span>{doc.wordCount.toLocaleString()} words</span>}
            {doc.lines > 0 && <span>{doc.lines} lines</span>}
          </div>
        </div>
      )}

      {/* Content */}
      <div className="doc-content-wrapper">
        <MarkdownRenderer 
          content={doc.content} 
          fileType={doc.fileType} 
          title={doc.title}
          relatedCode={doc.relatedCode}
        />
      </div>
    </article>
  );
}