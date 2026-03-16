import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { getStats } from '../utils/api';
import { getCategoryMeta, formatCategory } from '../utils/categories';

export default function StatsPage() {
  const [stats, setStats] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getStats().then(r => setStats(r.data)).finally(() => setLoading(false));
  }, []);

  if (loading) return <div className="loading"><div className="spinner" /></div>;
  if (!stats) return <div className="error-box">Could not load statistics.</div>;

  const maxCount = Math.max(...stats.byCategory.map(c => c.count), 1);

  // Sort categories by count descending
  const sorted = [...stats.byCategory].sort((a, b) => b.count - a.count);

  return (
    <div className="fade-up">
      {/* Breadcrumb */}
      <nav className="breadcrumb">
        <Link to="/">home</Link>
        <span className="sep">/</span>
        <span className="active-path">statistics</span>
      </nav>

      <h1 className="page-title" style={{ fontSize: 'clamp(3rem, 6vw, 5rem)', fontWeight: 800, letterSpacing: '-0.05em', color: '#fff', marginBottom: '8px' }}>
        Repository Stats
      </h1>
      <p className="page-subtitle" style={{ fontSize: '0.9rem', color: 'var(--txt-2)', marginBottom: '64px' }}>
        Quantifying the documentation complexity and reach
      </p>

      {/* ── Top stat cards ── */}
      <div className="grid grid-3" style={{ marginBottom: '80px' }}>
        <div className="premium-doc-card">
          <span style={{ fontSize: '3rem', fontWeight: 800, color: '#fff' }}>{stats.totalDocuments}</span>
          <span style={{ fontSize: '0.75rem', textTransform: 'uppercase', letterSpacing: '0.1em', color: 'var(--txt-2)' }}>Total Documents</span>
        </div>
        <div className="premium-doc-card">
          <span style={{ fontSize: '3rem', fontWeight: 800, color: '#fff' }}>{stats.byCategory.length}</span>
          <span style={{ fontSize: '0.75rem', textTransform: 'uppercase', letterSpacing: '0.1em', color: 'var(--txt-2)' }}>Categories</span>
        </div>
        <div className="premium-doc-card">
          <span style={{ fontSize: '3rem', fontWeight: 800, color: '#fff' }}>{(stats.totalWords / 1000).toFixed(1)}k</span>
          <span style={{ fontSize: '0.75rem', textTransform: 'uppercase', letterSpacing: '0.1em', color: 'var(--txt-2)' }}>Complexity Hub</span>
        </div>
      </div>

      {/* ── File type breakdown ── */}
      <section style={{ marginBottom: '44px' }}>
        <div className="section-header">
          <span className="section-title">File Types</span>
        </div>
        <div className="grid grid-3">
          {stats.byFileType.map(ft => (
            <div key={ft._id} className="card" style={{ textAlign: 'center' }}>
              <div style={{ fontSize: '1.8rem', marginBottom: '10px' }}>
                {ft._id === 'py' ? '🐍' : ft._id === 'md' ? '📝' : '📄'}
              </div>
              <div style={{
                fontFamily: 'var(--font-mono)',
                fontSize: '2rem', fontWeight: 700,
                color: ft._id === 'py' ? 'var(--amber)' : 'var(--acid)',
                marginBottom: '6px',
              }}>
                {ft.count}
              </div>
              <div style={{ color: 'var(--txt-2)', fontSize: '0.75rem', fontFamily: 'var(--font-mono)' }}>
                .{ft._id} files
              </div>
            </div>
          ))}
        </div>
      </section>

      {/* ── Category breakdown bar chart ── */}
      <section>
        <div className="section-header" style={{ marginBottom: '20px' }}>
          <span className="section-title">Documents by Category</span>
          <span className="section-label">sorted by count</span>
        </div>

        <div className="card">
          {sorted.map((cat, i) => {
            const meta = getCategoryMeta(cat._id);
            const pct = (cat.count / maxCount) * 100;
            return (
              <Link
                key={cat._id}
                to={`/category/${cat._id}`}
                style={{ textDecoration: 'none', display: 'block' }}
              >
                <div
                  className="stat-bar-row"
                  style={{
                    animationDelay: `${i * 0.04}s`,
                    animation: 'fadeUp 0.4s ease both',
                  }}
                >
                  <span className="stat-bar-label">
                    {meta.icon} {formatCategory(cat._id)}
                  </span>
                  <div className="stat-bar-track">
                    <div
                      className="stat-bar-fill"
                      style={{
                        width: `${pct}%`,
                        background: `linear-gradient(90deg, ${meta.color}, var(--cyan))`,
                      }}
                    />
                  </div>
                  <span className="stat-bar-count">{cat.count}</span>
                </div>
              </Link>
            );
          })}
        </div>
      </section>
    </div>
  );
}