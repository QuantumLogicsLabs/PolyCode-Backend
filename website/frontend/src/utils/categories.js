export const CATEGORY_META = {
  algorithms:       { icon: '⚡', color: '#f59e0b', bg: 'rgba(245,158,11,0.1)' },
  tutorials:        { icon: '📖', color: '#10b981', bg: 'rgba(16,185,129,0.1)' },
  learning_curriculum: { icon: '🎓', color: '#8b5cf6', bg: 'rgba(139,92,246,0.1)' },
  data_structures:  { icon: '🌲', color: '#06b6d4', bg: 'rgba(6,182,212,0.1)' },
  api_development:   { icon: '🔌', color: '#ec4899', bg: 'rgba(236,72,153,0.1)' },
  concepts:         { icon: '💡', color: '#f97316', bg: 'rgba(249,115,22,0.1)' },
  faq:              { icon: '❓', color: '#64748b', bg: 'rgba(100,116,139,0.1)' },
  reference:        { icon: '📚', color: '#3b82f6', bg: 'rgba(59,130,246,0.1)' },
  examples:         { icon: '🧪', color: '#14b8a6', bg: 'rgba(20,184,166,0.1)' },
  getting_started:  { icon: '🚀', color: '#22c55e', bg: 'rgba(34,197,94,0.1)' },
  projects:         { icon: '🏗️', color: '#a78bfa', bg: 'rgba(167,139,250,0.1)' },
  advanced_python:  { icon: '🔥', color: '#ccff00', bg: 'rgba(204,255,0,0.1)' },
  concurrency:      { icon: '🧵', color: '#00f2ff', bg: 'rgba(0,242,255,0.1)' },
  data_science:     { icon: '📊', color: '#bf66ff', bg: 'rgba(191,102,255,0.1)' },
  database:         { icon: '🛢️', color: '#ffcc00', bg: 'rgba(255,204,0,0.1)' },
  deployment:       { icon: '🚢', color: '#ff3366', bg: 'rgba(255,51,102,0.1)' },
  games:            { icon: '🎮', color: '#00ff88', bg: 'rgba(0,255,136,0.1)' },
  general:          { icon: '💠', color: '#94a3b8', bg: 'rgba(148,163,184,0.1)' },
  mathematics:      { icon: '♾️', color: '#6366f1', bg: 'rgba(99,102,241,0.1)' },
  testing:          { icon: '🧪', color: '#14b8a6', bg: 'rgba(20,184,166,0.1)' },
  utilities:        { icon: '🛠️', color: '#f43f5e', bg: 'rgba(244,63,94,0.1)' },
  web_development:  { icon: '🌐', color: '#0ea5e9', bg: 'rgba(14,165,233,0.1)' },
  src:              { icon: '📂', color: '#94a3b8', bg: 'rgba(148,163,184,0.1)' },
};

export function getCategoryMeta(cat) {
  return CATEGORY_META[cat] || { icon: '📄', color: '#94a3b8', bg: 'rgba(148,163,184,0.1)' };
}

export function formatCategory(cat) {
  return (cat || '')
    .replace(/_/g, ' ')
    .replace(/\b\w/g, c => c.toUpperCase());
}
