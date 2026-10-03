import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import '../css/application.css';
import '../css/about.css';
import '../css/brand.css';
import '../css/admin-filters.css';
import '../css/admin-review.css';
import '../css/admin-modern.css';
import '../css/fees.css';
import '../css/contact.css';
import '../css/event.css';
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
