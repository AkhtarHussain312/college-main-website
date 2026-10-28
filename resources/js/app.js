import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import 'bootstrap';
import App from './App.vue';
import ChatWidget from './components/ChatWidget.vue';
import HomePage from './pages/HomePage.vue';
import AdminPage from './pages/AdminPage.vue';
import ProgramsPage from './pages/ProgramsPage.vue';
import AboutPage from './pages/AboutPage.vue';
import ApplyPage from './pages/ApplyPage.vue';
import ContactPage from './pages/ContactPage.vue';
import NewsEventPage from './pages/NewsEventPage.vue';
import NewsEventsPage from './pages/NewsEventsPage.vue';

const routes = [
  { path: '/', component: HomePage },
  { path: '/about', component: AboutPage },
  { path: '/programs', component: ProgramsPage },
  { path: '/faculty', beforeEnter: () => { window.location.assign('/faculty'); return false; } },
  { path: '/admissions', beforeEnter: () => { window.location.assign('/admissions'); return false; } },
  { path: '/campus-life', beforeEnter: () => { window.location.assign('/campus-life'); return false; } },
  { path: '/merit-lists', beforeEnter: () => { window.location.assign('/merit-lists'); return false; } },
  { path: '/track', beforeEnter: () => { window.location.assign('/track'); return false; } },
  { path: '/apply', component: ApplyPage },
  { path: '/contact', component: ContactPage },
  { path: '/news-events', component: NewsEventsPage },
  { path: '/news-events/:slug', component: NewsEventPage },
  { path: '/admin', component: AdminPage },
  { path: '/admin/home-page', component: AdminPage },
  { path: '/admin/contact-page', component: AdminPage },
  { path: '/admin/:tab', component: AdminPage },
  { path: '/:pathMatch(.*)*', component: HomePage },
];

function initApps() {
  // Mount SPA if #app exists (e.g. on /admin via welcome.blade.php)
  const appEl = document.getElementById('app');
  if (appEl && !appEl.__vue_app__) {
    createApp(App)
      .use(createRouter({ history: createWebHistory(), routes, scrollBehavior: () => ({ top: 0 }) }))
      .mount('#app');
  }

  // Mount ChatWidget on the public site (excluded on /admin paths)
  const isAdmin = window.location.pathname.startsWith('/admin');
  if (!isAdmin) {
    let chatEl = document.getElementById('dcn-chat-widget');
    if (!chatEl) {
      chatEl = document.createElement('div');
      chatEl.id = 'dcn-chat-widget';
      document.body.appendChild(chatEl);
    }
    if (!chatEl.__vue_app__) {
      createApp(ChatWidget).mount(chatEl);
    }
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initApps);
} else {
  initApps();
}
