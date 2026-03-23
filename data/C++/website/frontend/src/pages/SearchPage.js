import React, { useEffect, useState } from 'react';
import { useSearchParams, Link } from 'react-router-dom';
import { getDocuments } from '../utils/api';
import DocCard from '../components/DocCard';

export default function SearchPage() {
  const [searchParams, setSearchParams] = useSearchParams();
  const [query, setQuery]   = useState(searchParams.get('q') || '');
  const [fileType, setFileType] = useState(searchParams.get('fileType') || '');
  const [docs, setDocs]     = useState([]);
  const [total, setTotal]   = useState(0);
  const [loading, setLoading] = useState(false);
  const [searched, setSearched] = useState(false);

  const doSearch = (q, ft, pg = 1) => {
    if (!q && !ft) return;
    setLoading(true);
    setSearched(true);
    getDocuments({ search: q || undefined, fileType: ft || undefined, page: pg, limit: 40 })
      .then(r => {
        setDocs(r.data.documents);
        setTotal(r.data.total);
      })
      .finally(() => setLoading(false));
  };

  useEffect(() => {
    const q  = searchParams.get('q') || '';
    const ft = searchParams.get('fileType') || '';
    setQuery(q);
    setFileType(ft);
    doSearch(q, ft);
  }, [searchParams]);

  const handleSubmit = (e) => {
    e.preventDefault();
    setSearchParams(query ? { q: query } : {});
  };

  return (
    <div>
      <nav className="breadcrumb">
        <Link to="/">home</Link>
        <span className="sep">/</span>
        <span className="active-path">search</span>
      </nav>

      <div style={{ marginBottom: '64px' }}>
        <h1 className="page-title" style={{ fontSize: 'clamp(3rem, 6vw, 5rem)', fontWeight: 800, letterSpacing: '-0.02em', lineHeight: 1.1, color: '#fff', marginBottom: '8px' }}>
          Search Hub
        </h1>
        <p className="page-subtitle" style={{ fontSize: '0.9rem', color: 'var(--txt-2)' }}>
          Scan the entire technical repository in real-time
        </p>
      </div>

      <form onSubmit={handleSubmit} style={{ display: 'flex', gap: '16px', marginBottom: '64px' }}>
        <input
          type="text"
          value={query}
          onChange={e => setQuery(e.target.value)}
          placeholder="Query topics, functions, snippets…"
          style={{
            flex: 1, padding: '16px 24px', background: 'rgba(255,255,255,0.03)',
            border: '1px solid var(--border)', borderRadius: '12px',
            color: '#fff', fontFamily: 'var(--font-body)',
            fontSize: '1rem', outline: 'none',
          }}
          autoFocus
        />
        <select
          value={fileType}
          onChange={e => setFileType(e.target.value)}
          style={{
            padding: '16px 24px', background: 'rgba(255,255,255,0.03)',
            border: '1px solid var(--border)', borderRadius: '12px',
            color: '#fff', fontFamily: 'var(--font-mono)',
            outline: 'none', cursor: 'pointer',
          }}
        >
          <option value="">All</option>
          <option value="md">.md</option>
          <option value="py">.py</option>
        </select>
        <button
          type="submit"
          className="btn-primary"
          style={{ borderRadius: '12px' }}
        >
          Execute Search
        </button>
      </form>

      {loading && (
        <div className="loading"><div className="spinner" /></div>
      )}

      {!loading && searched && (
        <>
          <div style={{ marginBottom: '20px', color: 'var(--text-muted)', fontSize: '0.9rem' }}>
            Found <strong style={{ color: 'var(--accent)' }}>{total}</strong> result{total !== 1 ? 's' : ''}
            {query && <> for "<span style={{ color: 'var(--text-primary)' }}>{query}</span>"</>}
          </div>
          {docs.length > 0 ? (
            <>
              <div className="grid grid-bento">
                {docs.map(doc => <DocCard key={doc.path} doc={doc} />)}
              </div>
              
              {total > 40 && (
                <div style={{ display: 'flex', justifyContent: 'center', gap: '8px', marginTop: '64px' }}>
                  {Array.from({ length: Math.ceil(total / 40) }).map((_, i) => (
                    <button
                      key={i}
                      onClick={() => {
                        const page = i + 1;
                        const q = searchParams.get('q') || '';
                        const ft = searchParams.get('fileType') || '';
                        doSearch(q, ft, page);
                        window.scrollTo(0, 0);
                      }}
                      className="btn-primary"
                      style={{
                        padding: '10px 18px',
                        background: (parseInt(searchParams.get('page') || '1') === i + 1) ? '#fff' : 'rgba(255,255,255,0.05)',
                        color: (parseInt(searchParams.get('page') || '1') === i + 1) ? '#000' : '#fff'
                      }}
                    >
                      {i + 1}
                    </button>
                  ))}
                </div>
              )}
            </>
          ) : (
            <div style={{ textAlign: 'center', padding: '60px', color: 'var(--text-muted)' }}>
              <div style={{ fontSize: '3rem', marginBottom: '16px' }}>🔎</div>
              <p>No documents found. Try a different search term.</p>
            </div>
          )}
        </>
      )}

      {!searched && (
        <div style={{ textAlign: 'center', padding: '60px', color: 'var(--text-muted)' }}>
          <div style={{ fontSize: '3rem', marginBottom: '16px' }}>⌨️</div>
          <p>Type something to search across all documents.</p>
        </div>
      )}
    </div>
  );
}
