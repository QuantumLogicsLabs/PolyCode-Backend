import React, { useState, useRef, useEffect } from 'react';
import { Link, useNavigate, useLocation } from 'react-router-dom';

export default function Navbar({ toggleSidebar }) {
  const [query, setQuery] = useState('');
  const navigate = useNavigate();
  const location = useLocation();
  const inputRef = useRef(null);

  // Keyboard shortcut: Cmd/Ctrl + K
  useEffect(() => {
    const handler = (e) => {
      if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        inputRef.current?.focus();
      }
    };
    window.addEventListener('keydown', handler);
    return () => window.removeEventListener('keydown', handler);
  }, []);

  const handleSearch = (e) => {
    e.preventDefault();
    if (query.trim()) navigate(`/search?q=${encodeURIComponent(query.trim())}`);
  };

  const isActive = (path) =>
    location.pathname === path ? 'active' : '';

  return (
    <nav className="navbar">
      <button className="mobile-menu-toggle" onClick={toggleSidebar}>
        <span></span>
        <span></span>
        <span></span>
      </button>

      {/* Brand */}
      <Link to="/" className="navbar-brand">
        <div className="logo">
          <span style={{ position: 'relative', zIndex: 1 }}>Py</span>
        </div>
        <div className="navbar-brand-text">
          <span className="logo-text">PyHub</span>
          <span className="logo-sub">v2.0 docs</span>
        </div>
      </Link>

      {/* Search */}
      <form className="navbar-search" onSubmit={handleSearch}>
        <span className="search-icon-left">⌕</span>
        <input
          ref={inputRef}
          type="text"
          placeholder="Search docs, topics, code…"
          value={query}
          onChange={e => setQuery(e.target.value)}
          aria-label="Search"
        />
        <span className="search-kbd">⌘K</span>
      </form>

      {/* Links */}
      <div className="navbar-links">
        <Link to="/" className={isActive('/')}>Home</Link>
        <Link to="/search" className={isActive('/search')}>Search</Link>
        <Link to="/stats" className={isActive('/stats')}>Stats</Link>
      </div>
    </nav>
  );
}