import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Navbar from './components/Navbar';
import Sidebar from './components/Sidebar';
import HomePage from './pages/HomePage';
import DocumentPage from './pages/DocumentPage';
import CategoryPage from './pages/CategoryPage';
import SearchPage from './pages/SearchPage';
import StatsPage from './pages/StatsPage';
import './App.css';

function App() {
  const [isSidebarOpen, setIsSidebarOpen] = React.useState(false);

  const toggleSidebar = () => setIsSidebarOpen(!isSidebarOpen);
  const closeSidebar = () => setIsSidebarOpen(false);

  return (
    <Router>
      <div className="app">
        <Navbar toggleSidebar={toggleSidebar} />
        <div className="layout">
          {isSidebarOpen && <div className="backdrop" onClick={closeSidebar}></div>}
          <Sidebar isOpen={isSidebarOpen} onClose={closeSidebar} />
          <main className="main-content">
            <Routes>
              <Route path="/" element={<HomePage />} />
              <Route path="/doc/:id" element={<DocumentPage />} />
              <Route path="/category/:category" element={<CategoryPage />} />
              <Route path="/search" element={<SearchPage />} />
              <Route path="/stats" element={<StatsPage />} />
            </Routes>
          </main>
        </div>
      </div>
    </Router>
  );
}


export default App;
