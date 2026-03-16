import React, { useEffect, useState } from 'react';
import { useParams, Link } from 'react-router-dom';
import { getDocuments } from '../utils/api';
import DocCard from '../components/DocCard';
import { getCategoryMeta, formatCategory } from '../utils/categories';

export default function CategoryPage() {
  const { category } = useParams();
  const [docs, setDocs]     = useState([]);
  const [total, setTotal]   = useState(0);
  const [page, setPage]     = useState(1);
  const [loading, setLoading] = useState(true);
  const LIMIT = 24;

  useEffect(() => {
    setLoading(true);
    getDocuments({ category, page, limit: LIMIT, ungrouped: true })
      .then(r => {
        setDocs(r.data.documents);
        setTotal(r.data.total);
      })
      .finally(() => setLoading(false));
  }, [category, page]);

  const meta = getCategoryMeta(category);

  return (
    <div>
      <nav className="breadcrumb">
        <Link to="/">home</Link>
        <span className="sep">/</span>
        <span className="active-path">{formatCategory(category)}</span>
      </nav>

      <div style={{ marginBottom: '64px' }}>
        <h1 className="page-title" style={{ fontSize: 'clamp(3rem, 6vw, 5rem)', fontWeight: 800, letterSpacing: '-0.02em', lineHeight: 1.1, color: '#fff', marginBottom: '8px' }}>
          {formatCategory(category)}
        </h1>
        <p className="page-subtitle" style={{ fontSize: '0.9rem', color: 'var(--txt-2)' }}>
          {total} technical archives in this domain
        </p>
      </div>

      <hr className="divider" />

      {loading ? (
        <div className="loading"><div className="spinner" /></div>
      ) : (
        <>
          <div className="grid grid-bento">
            {docs.map(doc => <DocCard key={doc.path} doc={doc} />)}
          </div>

          {/* Pagination */}
          {total > LIMIT && (
            <div style={{ display: 'flex', gap: '8px', justifyContent: 'center', marginTop: '32px' }}>
              {Array.from({ length: Math.ceil(total / LIMIT) }, (_, i) => i + 1).map(p => (
                <button
                  key={p}
                  onClick={() => setPage(p)}
                  style={{
                    padding: '8px 14px', borderRadius: '8px', cursor: 'pointer',
                    border: p === page ? '1px solid var(--accent)' : '1px solid var(--border)',
                    background: p === page ? 'rgba(0,212,255,0.1)' : 'var(--bg-card)',
                    color: p === page ? 'var(--accent)' : 'var(--text-secondary)',
                    fontFamily: 'Sora, sans-serif',
                  }}
                >
                  {p}
                </button>
              ))}
            </div>
          )}
        </>
      )}
    </div>
  );
}
