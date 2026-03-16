import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { getDocuments, getStats, getCategories } from '../utils/api';
import DocCard from '../components/DocCard';
import { getCategoryMeta, formatCategory } from '../utils/categories';

export default function HomePage() {
  const [categories, setCategories] = useState([]);
  const [recent, setRecent] = useState([]);
  const [pyFiles, setPyFiles] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    Promise.all([
      getStats(),
      getCategories(),
      getDocuments({ limit: 100, fileType: 'md', ungrouped: true }),
      getDocuments({ limit: 100, fileType: 'py', ungrouped: true }),
    ]).then(([s, c, r, p]) => {
      setCategories(c.data);
      setRecent(r.data.documents);
      setPyFiles(p.data.documents);
    }).finally(() => setLoading(false));
  }, []);

  if (loading) {
    return (
      <div className="loading">
        <div className="spinner" />
        <p>initialising hub…</p>
      </div>
    );
  }

  return (
    <div>
      {/* ── Hero ── */}
      <section className="hero fade-up">
        <div className="hero-eyebrow">
          <span>Available for code collaboration</span>
        </div>
        
        <h1>
          Master Python with <br />
          <span className="gradient-text">Absolute Precision.</span>
        </h1>

        <p>
          I provide a curated collection of Python algorithms, data structures, 
          and technical documentation designed for high-performance engineers.
        </p>

        <div className="hero-actions">
          <Link to="/search" className="btn-primary">
            Explore Documentation <span style={{ marginLeft: '4px' }}>→</span>
          </Link>
          <Link to="/stats" className="btn-secondary">
            View Stats
          </Link>
        </div>

      </section>

      <div className="divider-accent" style={{ opacity: 0.1 }} />

      {/* ── Featured Bento Grid ── */}
      <section style={{ marginBottom: '80px' }}>
        <div className="section-header" style={{ marginBottom: '32px' }}>
          <span className="section-title">Latest Archives</span>
          <span className="section-label">Documentation & Scripts</span>
        </div>
        
        <div className="grid grid-bento">
          {recent.map(doc => <DocCard key={doc.path} doc={doc} />)}
          {pyFiles.map(doc => <DocCard key={doc.path} doc={doc} />)}
        </div>
      </section>

      {/* ── Secondary Sections ── */}
      <section style={{ marginBottom: '80px' }}>
        <div className="section-header">
          <span className="section-title">Technical Domains</span>
          <Link to="/search" className="view-all">all categories →</Link>
        </div>
        <div className="cat-grid">
          {categories.slice(0, 8).map((cat, i) => {
            const meta = getCategoryMeta(cat);
            return (
              <Link
                key={cat}
                to={`/category/${cat}`}
                className={`cat-card fade-up fade-up-${(i % 4) + 1}`}
                style={{ '--cat-color': meta.color }}
              >
                <span className="cat-card-icon">{meta.icon}</span>
                <span className="cat-card-name">{formatCategory(cat)}</span>
              </Link>
            );
          })}
        </div>
      </section>
    </div>
  );
}