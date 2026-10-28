<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';

const props = defineProps({
  active: { type: String, default: 'home' },
  site: { type: Object, default: () => ({}) }
});

const route = useRoute();
const sectionByHash = {
  '#about-us': 'about us',
  '#admissions': 'admissions',
  '#clinical-training': 'clinical training',
  '#campus-life': 'campus life',
  '#news-and-events': 'news & events'
};
const currentActive = computed(() => route.path === '/' ? (sectionByHash[route.hash] || props.active) : props.active);

const navItems = [
  { label: 'Home', path: '/' },
  { label: 'Programs', path: '/programs' },
  { label: 'Faculty', path: '/faculty' },
  { label: 'Admissions', path: '/admissions' },
  { label: 'Campus Life', path: '/campus-life' },
  { label: 'News & Events', path: '/news-events' },
  { label: 'About Us', path: '/about' },
  { label: 'Contact', path: '/contact' },
];
</script>

<template>
  <div class="topbar">
    <div class="container d-flex justify-content-between align-items-center">
      <div class="topbar-announcement text-truncate">
        <span class="badge bg-danger-subtle text-danger px-2 py-1 me-2 fw-bold" style="font-size: 10px;">
          <i class="bi bi-megaphone-fill"></i> UPDATE
        </span>
        <span>{{ site.admissions_banner || 'Admissions Open for Fall 2026 — Start Your Nursing Career Today' }}</span>
      </div>
      <div class="topbar-actions d-none d-md-flex align-items-center gap-3">
        <a href="/merit-lists" class="topbar-link"><i class="bi bi-file-earmark-arrow-down"></i> Merit Lists</a>
        <a href="/track" class="topbar-link"><i class="bi bi-search"></i> Track Application</a>
        <router-link to="/apply" class="topbar-link text-white fw-bold">Apply Now <i class="bi bi-arrow-right"></i></router-link>
      </div>
    </div>
  </div>
  <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
    <div class="container">
      <router-link class="navbar-brand d-flex align-items-center gap-2" to="/">
        <span class="brand-mark logo-mark">
          <img class="brand-logo" src="/logo.png" :alt="`${site.college_short_name || site.college_name || 'College'} logo`">
        </span>
        <span>
          {{ site.nav_brand_title || site.college_short_name || site.college_name || 'College' }}
          <small>{{ site.nav_brand_subtitle || site.college_name || 'College' }}</small>
        </span>
      </router-link>
      <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div id="nav" class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
          <li v-for="item in navItems" :key="item.label" class="nav-item">
            <router-link
              class="nav-link"
              :class="{ active: item.label.toLowerCase() === currentActive }"
              :to="item.path"
            >
              {{ item.label }}
            </router-link>
          </li>
          <li>
            <router-link class="btn btn-primary ms-lg-3" to="/apply">Apply Now</router-link>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>
