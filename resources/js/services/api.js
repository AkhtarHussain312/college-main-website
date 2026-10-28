import axios from 'axios';

const api = axios.create({
  baseURL: '/api',
  headers: { Accept: 'application/json' },
});

api.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.content;

let cachedCollegeContent = null;
let collegeContentPromise = null;

export const getCollegeContent = (forceRefresh = false) => {
  if (cachedCollegeContent && !forceRefresh) {
    return Promise.resolve({ data: { data: cachedCollegeContent } });
  }
  if (!collegeContentPromise || forceRefresh) {
    collegeContentPromise = api.get('/college-content').then((response) => {
      cachedCollegeContent = response.data.data;
      return response;
    }).finally(() => {
      collegeContentPromise = null;
    });
  }
  return collegeContentPromise;
};

export const clearCollegeContentCache = () => {
  cachedCollegeContent = null;
};

export const getEvents = (params = {}) => api.get('/events', { params });
export const getEvent = (slug) => api.get(`/events/${encodeURIComponent(slug)}`);
export const sendInquiry = (data) => api.post('/inquiries', data);
export const submitApplication = (data) => api.post('/applications', data);
export const sendContactMessage = (data) => api.post('/contact-messages', data);
export const sendChatMessage = (data) => api.post('/chat', data);

export const adminApi = {
  login: (data) => api.post('/admin/login', data),
  me: () => api.get('/admin/me'),
  logout: () => api.post('/admin/logout'),
  list: (type, params = {}) => api.get(`/admin/${type}`, { params: { ...params, _t: Date.now() } }),
  create: (type, data) => api.post(`/admin/${type}`, data).then(r => { clearCollegeContentCache(); return r; }),
  update: (type, id, data) => (data instanceof FormData ? api.post(`/admin/${type}/${id}`, data) : api.put(`/admin/${type}/${id}`, data)).then(r => { clearCollegeContentCache(); return r; }),
  remove: (type, id) => api.delete(`/admin/${type}/${id}`).then(r => { clearCollegeContentCache(); return r; }),
  content: () => api.get('/admin/site-content', { params: { _t: Date.now() } }),
  saveContent: (id, value) => (value instanceof FormData ? api.post(`/admin/site-content/${id}`, value) : api.put(`/admin/site-content/${id}`, { value })).then(r => { clearCollegeContentCache(); return r; }),
  updateProfile: (data) => api.put('/admin/profile', data),
};
