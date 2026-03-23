import axios from 'axios';

const API_BASE = process.env.REACT_APP_API_URL || 'http://localhost:5000/api';

const api = axios.create({ baseURL: API_BASE });

export const getDocuments = (params) => api.get('/documents', { params });
export const getDocument  = (id)     => api.get(`/documents/${id}`);
export const getCategories = ()      => api.get('/documents/categories');
export const getStats      = ()      => api.get('/documents/stats');

export default api;
