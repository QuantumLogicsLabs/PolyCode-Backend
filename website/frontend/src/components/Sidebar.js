import React, { useEffect, useState } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { getCategories, getStats } from '../utils/api';
import { getCategoryMeta, formatCategory } from '../utils/categories';

export default function Sidebar() {
  const [categories, setCategories] = useState([]);
  const [stats, setStats] = useState(null);
  const location = useLocation();

  useEffect(() => {
    getCategories().then(r => setCategories(r.data)).catch(() => { });
    getStats().then(r => setStats(r.data)).catch(() => { });
  }, []);

  const isActive = (path) => location.pathname === path;
  const catCount = (cat) => {
    if (!stats) return null;
    const found = stats.byCategory.find(b => b._id === cat);
    return found ? found.count : null;
  };

  return (
    <aside className="sidebar">
      {/* Quick Nav */}
      <div className="sidebar-section">
        <div className="sidebar-section-title">Navigate</div>

        <Link to="/" className={`sidebar-item ${isActive('/') ? 'active' : ''}`}>
          <span className="icon">⌂</span> Home
        </Link>
        <Link to="/stats" className={`sidebar-item ${isActive('/stats') ? 'active' : ''}`}>
          <span className="icon">◫</span> Statistics
          {stats && <span className="badge">{stats.totalDocuments}</span>}
        </Link>
        <Link to="/search" className={`sidebar-item ${isActive('/search') ? 'active' : ''}`}>
          <span className="icon">⌕</span> Search
        </Link>
      </div>

      <div className="sidebar-sep" />

      {/* Categories */}
      <div className="sidebar-section">
        <div className="sidebar-section-title">Categories</div>
        {categories.map(cat => {
          const meta = getCategoryMeta(cat);
          const active = location.pathname === `/category/${cat}`;
          return (
            <Link
              key={cat}
              to={`/category/${cat}`}
              className={`sidebar-item ${active ? 'active' : ''}`}
              style={active ? { '--cat-accent': meta.color } : {}}
            >
              <span className="icon">{meta.icon}</span>
              {formatCategory(cat)}
              {catCount(cat) !== null && (
                <span className="badge">{catCount(cat)}</span>
              )}
            </Link>
          );
        })}
      </div>

      <div className="sidebar-sep" />

      {/* File Types */}
      {stats && (
        <div className="sidebar-section">
          <div className="sidebar-section-title">File Types</div>
          {stats.byFileType.map(ft => (
            <Link
              key={ft._id}
              to={`/search?fileType=${ft._id}`}
              className="sidebar-item"
            >
              <span className="icon">
                {ft._id === 'python' || ft._id === 'py' ? '🐍' : 
                 ft._id === 'markdown' || ft._id === 'md' ? '📝' : 
                 ft._id === 'javascript' || ft._id === 'js' ? '📜' :
                 ft._id === 'html' ? '🌐' :
                 ft._id === 'css' ? '🎨' : '📄'}
              </span>
              .{ft._id === 'markdown' ? 'md' : ft._id === 'python' ? 'py' : ft._id}
              <span className="badge">{ft.count}</span>
            </Link>
          ))}
        </div>
      )}
    </aside>
  );
}