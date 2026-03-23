import React, { useEffect, useState } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { getCategories, getStats } from '../utils/api';
import { getCategoryMeta, formatCategory } from '../utils/categories';

export default function Sidebar({ isOpen, onClose }) {
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

  const handleItemClick = () => {
    if (window.innerWidth <= 900) {
      onClose();
    }
  };

  return (
    <aside className={`sidebar ${isOpen ? 'active' : 'mini'}`}>
      <button className="sidebar-close" onClick={onClose}>×</button>
      
      {/* Quick Nav */}
      <div className="sidebar-section">
        <div className="sidebar-section-title">Navigate</div>

        <Link to="/" className={`sidebar-item ${isActive('/') ? 'active' : ''}`} onClick={handleItemClick}>
          <span className="icon">⌂</span>
          <span className="sidebar-text">Home</span>
        </Link>
        <Link to="/stats" className={`sidebar-item ${isActive('/stats') ? 'active' : ''}`} onClick={handleItemClick}>
          <span className="icon">◫</span>
          <span className="sidebar-text">Statistics</span>
          {stats && <span className="badge">{stats.totalDocuments}</span>}
        </Link>
        <Link to="/search" className={`sidebar-item ${isActive('/search') ? 'active' : ''}`} onClick={handleItemClick}>
          <span className="icon">⌕</span>
          <span className="sidebar-text">Search</span>
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
              onClick={handleItemClick}
            >
              <span className="icon">{meta.icon}</span>
              <span className="sidebar-text">{formatCategory(cat)}</span>
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
              onClick={handleItemClick}
            >
              <span className="icon">
                {ft._id === 'python' || ft._id === 'py' ? '🐍' : 
                 ft._id === 'markdown' || ft._id === 'md' ? '📝' : 
                 ft._id === 'javascript' || ft._id === 'js' ? '📜' :
                 ft._id === 'html' ? '🌐' :
                 ft._id === 'css' ? '🎨' : '📄'}
              </span>
              <span className="sidebar-text">.{ft._id === 'markdown' ? 'md' : ft._id === 'python' ? 'py' : ft._id}</span>
              <span className="badge">{ft.count}</span>
            </Link>
          ))}
        </div>
      )}
    </aside>
  );
}
