<script setup>
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { Modal } from 'bootstrap';
import { adminApi, getCollegeContent } from '../services/api';

const route = useRoute();
const router = useRouter();

const adminSite = ref({});
const user = ref(null);
const checking = ref(true);
const login = ref({ email: 'akhtar@website.com', password: '', remember: false });
const loginError = ref('');
function getInitialTab() {
  const path = typeof window !== 'undefined' ? window.location.pathname.replace(/\/$/, '') : '';
  if (path === '/admin/contact-page' || path.endsWith('/contact-page')) return 'contact-page';
  if (path === '/admin/home-page' || path.endsWith('/home-page') || path === '/admin') return 'site-content';
  if (path.startsWith('/admin/')) {
    const segment = path.replace('/admin/', '');
    return segment || 'site-content';
  }
  return 'site-content';
}

const busy = ref(false);
const tab = ref(getInitialTab());
const items = ref([]);
const content = ref([]);
const editing = ref(null);
const notice = ref('');
const savingGroup = ref('');
const savedFields = ref({});

const applicationFilters = ref({ program_id: '', year: '' });
const filterOptions = ref({ programs: [], years: [] });
const feePrograms = ref([]);

const menuSections = [
  {
    title: 'ADMINISTRATION',
    items: [
      ['site-content', 'Home Page Content', 'bi-layout-text-window', '/admin/home-page'],
      ['contact-page', 'Contact Settings', 'bi-telephone', '/admin/contact-page'],
    ],
  },
  {
    title: 'ACADEMIC CONTENT',
    items: [
      ['programs', 'Programs', 'bi-mortarboard', '/admin/programs'],
      ['about-sections', 'About Sections', 'bi-card-text', '/admin/about-sections'],
      ['fees', 'Fee Structure', 'bi-cash-stack', '/admin/fees'],
      ['faculty', 'Faculty', 'bi-people', '/admin/faculty'],
      ['events', 'News & Events', 'bi-calendar-event', '/admin/events'],
      ['milestones', 'Admissions Steps', 'bi-signpost-split', '/admin/milestones'],
    ],
  },
  {
    title: 'RECORDS & INBOX',
    items: [
      ['applications', 'Applications', 'bi-file-earmark-check', '/admin/applications'],
      ['messages', 'Contact Messages', 'bi-chat-left-text', '/admin/messages'],
      ['inquiries', 'Inquiries', 'bi-envelope', '/admin/inquiries'],
    ],
  },
];

const tabs = menuSections.flatMap((s) => s.items);

function syncFromRoute() {
  const path = (route?.path || window.location.pathname).replace(/\/$/, '');
  if (path === '/admin/contact-page' || path.endsWith('/contact-page')) {
    tab.value = 'contact-page';
  } else if (path === '/admin/home-page' || path.endsWith('/home-page') || path === '/admin') {
    tab.value = 'site-content';
  } else if (path.startsWith('/admin/')) {
    const segment = path.replace('/admin/', '');
    if (tabs.some((t) => t[0] === segment)) {
      tab.value = segment;
    }
  }
}

watch(
  () => route?.path,
  () => {
    syncFromRoute();
    load();
  }
);

const fields = {
  programs: [
    ['name', 'Name'],
    ['slug', 'Slug'],
    ['duration', 'Duration'],
    ['description', 'Description', 'textarea'],
    ['image', 'Program image', 'file'],
    ['sort_order', 'Order', 'number'],
    ['is_active', 'Active', 'checkbox'],
  ],
  'about-sections': [
    ['title', 'Title'],
    ['body', 'Text', 'textarea'],
    ['image', 'Section image', 'file'],
    ['image_position', 'Image side', 'select'],
    ['sort_order', 'Order', 'number'],
    ['is_active', 'Active', 'checkbox'],
  ],
  fees: [
    ['program_id', 'Program', 'program'],
    ['fee_type', 'Fee type'],
    ['amount', 'Amount', 'number'],
    ['currency', 'Currency code'],
    ['billing_basis', 'Billing basis'],
    ['notes', 'Notes', 'textarea'],
    ['sort_order', 'Order', 'number'],
    ['is_active', 'Active', 'checkbox'],
  ],
  faculty: [
    ['name', 'Name'],
    ['title', 'Title'],
    ['qualification', 'Qualification'],
    ['image', 'Faculty photo', 'file'],
    ['sort_order', 'Order', 'number'],
    ['is_active', 'Active', 'checkbox'],
  ],
  events: [
    ['title', 'Title'],
    ['slug', 'Slug'],
    ['excerpt', 'Card summary', 'textarea'],
    ['body', 'Full article', 'textarea'],
    ['event_date', 'Event date', 'date'],
    ['image', 'Event image', 'file'],
    ['is_published', 'Published', 'checkbox'],
  ],
  milestones: [
    ['title', 'Title'],
    ['icon', 'Bootstrap icon'],
    ['sort_order', 'Order', 'number'],
  ],
  applications: [
    ['status', 'Application status', 'select'],
    ['admin_notes', 'Admin notes', 'textarea'],
  ],
  messages: [
    ['message', 'Message', 'textarea'],
    ['status', 'Message status', 'select'],
  ],
  inquiries: [
    ['status', 'Status', 'select'],
  ],
};

const statusChoices = computed(() =>
  tab.value === 'applications'
    ? ['submitted', 'under_review', 'shortlisted', 'accepted', 'rejected']
    : tab.value === 'messages'
    ? ['new', 'read', 'replied', 'closed']
    : tab.value === 'about-sections'
    ? ['left', 'right']
    : ['new', 'contacted', 'enrolled', 'closed']
);

const currentFields = computed(() => fields[tab.value] || []);

onMounted(async () => {
  syncFromRoute();
  try {
    adminSite.value = (await getCollegeContent()).data.data.site || {};
    document.title = `Admin | ${adminSite.value.college_name || 'College Website'}`;
  } catch {}
  try {
    user.value = (await adminApi.me()).data.data;
    await load();
  } catch {} finally {
    checking.value = false;
  }
});

async function signIn() {
  busy.value = true;
  loginError.value = '';
  try {
    user.value = (await adminApi.login(login.value)).data.data;
    await load();
  } catch (e) {
    loginError.value = e.response?.data?.message || 'Unable to sign in.';
  } finally {
    busy.value = false;
  }
}

async function signOut() {
  await adminApi.logout();
  user.value = null;
  login.value.password = '';
}

async function selectTab(value) {
  tab.value = value;
  editing.value = null;
  const tabItem = tabs.find((t) => t[0] === value);
  const targetUrl = tabItem?.[3] || `/admin/${value}`;
  if (router) {
    router.push(targetUrl).catch(() => {});
  } else {
    window.history.pushState(null, '', targetUrl);
  }
  await load();
}

async function load() {
  if (busy.value) return;
  busy.value = true;
  try {
    if (['site-content', 'contact-page'].includes(tab.value)) {
      content.value = (await adminApi.content()).data.data;
    } else {
      const response = await adminApi.list(
        tab.value,
        tab.value === 'applications' ? applicationFilters.value : {}
      );
      items.value = response.data.data.data;
      if (tab.value === 'applications') filterOptions.value = response.data.filters;
      if (tab.value === 'fees') feePrograms.value = response.data.options?.programs || [];
    }
  } finally {
    busy.value = false;
  }
}

async function clearApplicationFilters() {
  applicationFilters.value = { program_id: '', year: '' };
  await load();
}

async function add() {
  editing.value = {
    sort_order: 0,
    is_active: true,
    is_published: true,
    status: 'new',
    billing_basis: 'one time',
    currency: 'PKR',
    image_position: 'left',
  };
  await nextTick();
  Modal.getOrCreateInstance(document.getElementById('contentEditorModal')).show();
}

async function edit(item) {
  editing.value = { image_position: 'left', ...item };
  if (tab.value === 'applications') {
    await nextTick();
    Modal.getOrCreateInstance(document.getElementById('applicationReviewModal')).show();
  } else {
    await nextTick();
    Modal.getOrCreateInstance(document.getElementById('contentEditorModal')).show();
  }
}

async function save() {
  busy.value = true;
  notice.value = '';
  try {
    let payload = editing.value;
    if (editing.value.imageFile) {
      payload = new FormData();
      Object.entries(editing.value).forEach(([k, v]) => {
        if (!['imageFile', 'image_url', 'image_path', 'previewUrl'].includes(k) && v !== null && v !== undefined) {
          payload.append(k, typeof v === 'boolean' ? (v ? '1' : '0') : v);
        }
      });
      payload.append('image', editing.value.imageFile);
    }
    if (editing.value.id) await adminApi.update(tab.value, editing.value.id, payload);
    else await adminApi.create(tab.value, payload);
    if (tab.value === 'applications') Modal.getInstance(document.getElementById('applicationReviewModal'))?.hide();
    else Modal.getInstance(document.getElementById('contentEditorModal'))?.hide();
    editing.value = null;
    notice.value = 'Changes saved successfully.';
    await load();
  } catch (e) {
    notice.value = Object.values(e.response?.data?.errors || {}).flat().join(' ') || 'Unable to save changes.';
  } finally {
    busy.value = false;
  }
}

async function remove(item) {
  if (['applications', 'messages', 'inquiries'].includes(tab.value)) return;
  if (!confirm(`Delete “${item.name || item.title}”? This cannot be undone.`)) return;
  await adminApi.remove(tab.value, item.id);
  await load();
}

const MULTILINE_KEYS = [
  'footer_description',
  'contact_description',
  'hero_text',
  'hero_panel_text',
  'about_text',
  'clinical_text',
  'college_address',
  'proof_1_text',
  'proof_2_text',
  'programs_text',
  'testimonial_quote',
  'admissions_banner',
];

function autoResize(el) {
  if (!el) return;
  el.style.height = 'auto';
  el.style.height = Math.max(96, el.scrollHeight) + 'px';
}

const vAutoExpand = {
  mounted(el) {
    autoResize(el);
  },
  updated(el) {
    autoResize(el);
  },
};

function isTextareaField(item) {
  if (!item) return false;
  if (item.type === 'textarea') return true;
  const key = String(item.key || '').toLowerCase().trim();

  // Short field exclusions (titles, stats, percentages, labels, numbers, questions, etc.)
  if (
    key.endsWith('_title') ||
    key.endsWith('_stat') ||
    key.endsWith('_label') ||
    key.endsWith('_name') ||
    key.endsWith('_phone') ||
    key.endsWith('_email') ||
    key.endsWith('_kicker') ||
    key.endsWith('_cta') ||
    key.endsWith('_hours') ||
    key.endsWith('_days') ||
    key.endsWith('_time') ||
    key.endsWith('_question') ||
    key.endsWith('_value') ||
    key.endsWith('_val') ||
    key.endsWith('_number') ||
    key.endsWith('_link') ||
    key.endsWith('_banner') ||
    key.includes('point_')
  ) {
    return false;
  }

  // Primary rule: keys containing description, text, quote, or answer
  if (
    key.includes('description') ||
    key.includes('text') ||
    key.includes('quote') ||
    key.includes('answer')
  ) {
    return true;
  }

  // Additional multi-line text fields
  if (MULTILINE_KEYS.includes(key)) return true;

  return (
    key.includes('description') ||
    key.endsWith('_address') ||
    key === 'college_address' ||
    key.endsWith('_body') ||
    key === 'body' ||
    key.endsWith('_notes') ||
    key === 'notes' ||
    key.endsWith('_statement')
  );
}

function isFullWidthField(item) {
  if (!item) return false;
  if (item.type === 'image') return false;
  const key = String(item.key || '').toLowerCase().trim();
  if (key.includes('faq') || key.includes('question') || key.includes('footer')) return true;
  return isTextareaField(item);
}

const contactCards = [
  {
    id: 'direct-phones',
    title: 'Direct Phone Lines & WhatsApp',
    icon: 'bi-telephone-outbound',
    description: 'Configure dedicated admissions, student support, and accounts telephone lines and WhatsApp connectivity.',
    keys: ['college_whatsapp', 'admissions_phone', 'support_phone', 'accounts_phone'],
  },
  {
    id: 'location-hours',
    title: 'Location & Hours',
    icon: 'bi-clock-history',
    description: 'Specify campus office schedules, working days, response times, and street address.',
    keys: ['contact_office_hours', 'contact_office_days', 'contact_response_time', 'college_address'],
  },
  {
    id: 'form-intro',
    title: 'Contact Form & Intro',
    icon: 'bi-card-heading',
    description: 'Manage public contact page headlines, eyebrow labels, introduction copy, and form instructions.',
    keys: ['contact_eyebrow', 'contact_title', 'contact_description', 'contact_form_title', 'contact_form_text'],
  },
  {
    id: 'campus-faqs',
    title: 'Dynamic Campus FAQs',
    icon: 'bi-question-circle',
    description: 'Manage public frequently asked questions and detailed answers for admissions, campus visits, and WhatsApp inquiries.',
    keys: [
      'contact_faq_1_question',
      'contact_faq_1_answer',
      'contact_faq_2_question',
      'contact_faq_2_answer',
      'contact_faq_3_question',
      'contact_faq_3_answer',
      'contact_faq_4_question',
      'contact_faq_4_answer',
    ],
  },
];

function getContactCardItems(card) {
  return card.keys
    .map((k) => content.value.find((c) => c.key === k))
    .filter(Boolean);
}

const unassignedContactItems = computed(() => {
  const cardKeySet = new Set(contactCards.flatMap((c) => c.keys));
  return content.value.filter((c) => c.group === 'contact page' && !cardKeySet.has(c.key));
});

async function saveContactCard(card) {
  const items = getContactCardItems(card);
  savingGroup.value = card.id;
  notice.value = '';
  try {
    await Promise.all(
      items.map(async (item) => {
        await adminApi.saveContent(item.id, item.value);
        if (['college_name', 'college_short_name'].includes(item.key)) {
          adminSite.value[item.key] = item.value;
          document.title = `Admin | ${adminSite.value.college_name || 'College Website'}`;
        }
      })
    );
    notice.value = `"${card.title}" settings saved successfully.`;
    await load();
  } catch (e) {
    notice.value = e.response?.data?.message || `Failed to save "${card.title}".`;
  } finally {
    savingGroup.value = '';
  }
}

async function saveUnassignedContact() {
  savingGroup.value = 'extra-contact';
  notice.value = '';
  try {
    await Promise.all(
      unassignedContactItems.value.map(async (item) => {
        await adminApi.saveContent(item.id, item.value);
      })
    );
    notice.value = 'Additional contact settings saved successfully.';
    await load();
  } catch (e) {
    notice.value = e.response?.data?.message || 'Failed to save additional settings.';
  } finally {
    savingGroup.value = '';
  }
}

function getGroupIcon(group) {
  const g = (group || '').toLowerCase();
  if (g.includes('college')) return 'bi-building';
  if (g.includes('home')) return 'bi-house-door';
  if (g.includes('contact')) return 'bi-telephone';
  if (g.includes('image')) return 'bi-images';
  return 'bi-folder2';
}

async function saveGroup(groupName) {
  savingGroup.value = groupName;
  notice.value = '';
  const groupItems = content.value.filter((x) => x.group === groupName);
  try {
    await Promise.all(
      groupItems.map(async (item) => {
        if (item.type === 'image') {
          if (item.imageFile) {
            const data = new FormData();
            data.append('image', item.imageFile);
            await adminApi.saveContent(item.id, data);
            item.imageFile = null;
          }
        } else {
          await adminApi.saveContent(item.id, item.value);
          if (['college_name', 'college_short_name'].includes(item.key)) {
            adminSite.value[item.key] = item.value;
            document.title = `Admin | ${adminSite.value.college_name || 'College Website'}`;
          }
        }
      })
    );
    notice.value = `All changes in "${groupName}" saved successfully.`;
    await load();
  } catch (e) {
    notice.value = e.response?.data?.message || `Failed to save changes for "${groupName}".`;
  } finally {
    savingGroup.value = '';
  }
}

async function saveText(item) {
  if (item.type === 'image' && !item.imageFile) return;
  item.saving = true;
  try {
    const payload =
      item.type === 'image'
        ? (() => {
            const data = new FormData();
            data.append('image', item.imageFile);
            return data;
          })()
        : item.value;
    await adminApi.saveContent(item.id, payload);
    if (['college_name', 'college_short_name'].includes(item.key)) {
      adminSite.value[item.key] = item.value;
      document.title = `Admin | ${adminSite.value.college_name || 'College Website'}`;
    }
    savedFields.value[item.id] = true;
    setTimeout(() => {
      delete savedFields.value[item.id];
    }, 2500);
    notice.value = `${item.label} updated.`;
  } catch (e) {
    notice.value = `Unable to update ${item.label}.`;
  } finally {
    item.saving = false;
  }
}

function chooseImage(target, event) {
  const file = event.target?.files?.[0] || event.dataTransfer?.files?.[0];
  if (file) {
    target.imageFile = file;
    target.previewUrl = URL.createObjectURL(file);
  }
}
</script>

<template>
  <div v-if="checking" class="admin-loading">
    <div class="spinner-border text-primary"></div>
  </div>

  <!-- Sign In Screen -->
  <main v-else-if="!user" class="login-page">
    <section class="login-card">
      <div class="login-brand"><i class="bi bi-heart-pulse-fill"></i></div>
      <h1>{{ adminSite.college_name || 'College' }} Admin</h1>
      <p>Sign in to manage the {{ adminSite.college_name || 'college' }} website.</p>
      <div v-if="loginError" class="alert alert-danger">{{ loginError }}</div>
      <form @submit.prevent="signIn">
        <label>Email address</label>
        <input v-model="login.email" type="email" class="form-control" required autocomplete="username" />
        <label>Password</label>
        <input v-model="login.password" type="password" class="form-control" required autocomplete="current-password" />
        <label class="form-check mt-3">
          <input v-model="login.remember" class="form-check-input" type="checkbox" /> Remember me
        </label>
        <button class="btn btn-primary w-100 mt-4" :disabled="busy">
          <span v-if="busy" class="spinner-border spinner-border-sm me-2"></span>Sign in
        </button>
      </form>
      <router-link to="/" class="back-link"><i class="bi bi-arrow-left"></i> Back to website</router-link>
    </section>
  </main>

  <!-- Modern Admin Shell -->
  <div v-else class="admin-shell">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
      <div class="admin-brand">
        <div class="admin-brand-icon"><i class="bi bi-heart-pulse-fill"></i></div>
        <div class="admin-brand-meta">
          <strong>{{ adminSite.college_short_name || adminSite.college_name || 'College' }}</strong>
          <small>CMS Portal</small>
        </div>
      </div>
      <nav>
        <template v-for="section in menuSections" :key="section.title">
          <div class="sidebar-section-label">{{ section.title }}</div>
          <button
            v-for="t in section.items"
            :key="t[0]"
            :class="[
              'sidebar-nav-btn',
              { active: tab === t[0], 'bg-teal-600/10 text-teal-600 font-medium border-r-2 border-teal-600': tab === t[0] }
            ]"
            @click="selectTab(t[0])"
          >
            <i :class="['bi', t[2]]"></i>
            <span>{{ t[1] }}</span>
          </button>
        </template>
      </nav>
      <div class="sidebar-bottom">
        <a href="/" target="_blank" class="view-site-btn">
          <span>View Live Website</span>
          <i class="bi bi-box-arrow-up-right"></i>
        </a>
      </div>
    </aside>

    <!-- Main Content Area -->
    <div class="admin-main">
      <!-- Modern Top Utility Bar -->
      <header class="admin-topbar">
        <div class="topbar-left">
          <button class="mobile-menu-btn" data-bs-toggle="offcanvas" aria-label="Open Navigation">
            <i class="bi bi-list"></i>
          </button>
          <div class="topbar-breadcrumbs">
            <span class="breadcrumb-sub">Administration</span>
            <h2 class="breadcrumb-title">{{ tabs.find((t) => t[0] === tab)?.[1] }}</h2>
          </div>
        </div>
        <div class="topbar-right">
          <a href="/" target="_blank" class="topbar-view-site">
            <i class="bi bi-box-arrow-up-right"></i>
            <span>View Website</span>
          </a>
          <div class="topbar-user-badge">
            <div class="user-avatar-circle">{{ (user.email || 'A').charAt(0).toUpperCase() }}</div>
            <div class="user-meta-info">
              <span class="user-meta-name">{{ adminSite.college_short_name || 'Admin' }}</span>
              <span class="user-meta-email">{{ user.email }}</span>
            </div>
            <button class="signout-btn" @click="signOut" title="Sign out">
              <i class="bi bi-box-arrow-right"></i>
            </button>
          </div>
        </div>
      </header>

      <div class="admin-content">
        <!-- Floating/Top Feedback Banner -->
        <div v-if="notice" class="admin-notice-banner">
          <div><i class="bi bi-check-circle-fill"></i> {{ notice }}</div>
          <button class="btn-close" style="font-size: 10px;" @click="notice = ''"></button>
        </div>

        <!-- 1. Home Page / Site Content Tab -->
        <template v-if="tab === 'site-content'">
          <div class="admin-page-header">
            <div>
              <h1>Home Page & Content Management</h1>
              <p>Configure college identity, landing page components, academic pathways, metrics, and website images.</p>
            </div>
          </div>

          <div class="content-groups">
            <section
              v-for="group in [...new Set(content.filter((x) => x.group !== 'contact page').map((x) => x.group))]"
              :key="group"
              class="admin-section-card"
            >
              <div class="admin-section-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-100">
                <div class="admin-section-title-wrap">
                  <h2 class="admin-section-title">
                    <i :class="['bi', getGroupIcon(group)]"></i>
                    {{ group }}
                  </h2>
                  <p class="admin-section-desc text-muted small">
                    {{ content.filter((x) => x.group === group).length }} configured dynamic keys
                  </p>
                </div>
                <button
                  class="btn-section-save"
                  :disabled="savingGroup === group"
                  @click="saveGroup(group)"
                >
                  <span v-if="savingGroup === group" class="spinner-border spinner-border-sm me-1"></span>
                  <i v-else class="bi bi-cloud-arrow-up-fill me-1"></i>
                  {{ savingGroup === group ? 'Saving Changes…' : `Save ${group}` }}
                </button>
              </div>

              <div class="admin-form-grid grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div
                  v-for="item in content.filter((x) => x.group === group)"
                  :key="item.id"
                  class="admin-field-group"
                  :class="{ 'full-width col-span-full': isFullWidthField(item) }"
                >
                  <label class="admin-field-label">
                    <span class="field-label-title">{{ item.label }}</span>
                    <code class="field-key-badge">{{ item.key }}</code>
                  </label>

                  <!-- Image File Uploader / Media Card -->
                  <div v-if="item.type === 'image'" class="admin-media-card">
                    <div class="admin-media-preview-wrap">
                      <img
                        v-if="item.previewUrl || item.image_url"
                        :src="item.previewUrl || item.image_url"
                        :alt="item.label"
                        class="media-preview-thumb"
                      />
                      <div v-else class="media-preview-empty">
                        <i class="bi bi-image"></i>
                        <small>No image</small>
                      </div>
                    </div>
                    <div class="admin-media-dropzone">
                      <label
                        :for="'file-' + item.id"
                        class="dropzone-box"
                        @dragover.prevent
                        @dragenter.prevent
                        @drop.prevent="chooseImage(item, $event)"
                      >
                        <i class="bi bi-cloud-arrow-up dropzone-icon"></i>
                        <div class="dropzone-prompt">
                          <span class="dropzone-browse-text">Click to upload</span>
                          <span class="dropzone-subtext">or drag & drop</span>
                        </div>
                        <span class="dropzone-limit">PNG, JPG, WebP · Max 5 MB</span>
                      </label>
                      <input
                        :id="'file-' + item.id"
                        type="file"
                        class="dropzone-file-input"
                        accept="image/jpeg,image/png,image/webp"
                        @change="chooseImage(item, $event)"
                      />
                      <div v-if="item.imageFile" class="dropzone-selected-bar">
                        <span class="dropzone-file-name" :title="item.imageFile.name">
                          <i class="bi bi-file-earmark-image me-1"></i>{{ item.imageFile.name }}
                        </span>
                        <button
                          class="btn btn-sm btn-primary py-0 px-2"
                          style="font-size: 11px; height: 24px;"
                          :disabled="item.saving"
                          @click="saveText(item)"
                        >
                          {{ item.saving ? 'Uploading…' : 'Upload' }}
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Multi-line Textarea -->
                  <div v-else-if="isTextareaField(item)" class="admin-input-wrap">
                    <textarea
                      v-model="item.value"
                      v-auto-expand
                      class="admin-textarea"
                      rows="3"
                      :placeholder="item.label"
                      @input="autoResize($event.target)"
                      @blur="saveText(item)"
                    ></textarea>
                    <button
                      class="inline-save-btn"
                      :title="savedFields[item.id] ? 'Saved' : 'Save field'"
                      @click="saveText(item)"
                    >
                      <i
                        :class="[
                          'bi',
                          savedFields[item.id]
                            ? 'bi-check-all text-success'
                            : item.saving
                            ? 'spinner-border spinner-border-sm'
                            : 'bi-check-lg',
                        ]"
                      ></i>
                    </button>
                  </div>

                  <!-- Single-line Text Input -->
                  <div v-else class="admin-input-wrap">
                    <input
                      v-model="item.value"
                      type="text"
                      class="admin-input"
                      :placeholder="item.label"
                      @blur="saveText(item)"
                      @keyup.enter="saveText(item)"
                    />
                    <button
                      class="inline-save-btn"
                      :title="savedFields[item.id] ? 'Saved' : 'Save field'"
                      @click="saveText(item)"
                    >
                      <i
                        :class="[
                          'bi',
                          savedFields[item.id]
                            ? 'bi-check-all text-success'
                            : item.saving
                            ? 'spinner-border spinner-border-sm'
                            : 'bi-check-lg',
                        ]"
                      ></i>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Dual Action Placement: Bottom Save Button -->
              <div class="admin-section-footer flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-4 border-t border-slate-100">
                <div class="footer-meta-tip text-muted small">
                  <i class="bi bi-info-circle me-1"></i> You can save the entire section or blur fields to save individually.
                </div>
                <button
                  class="btn-section-save"
                  :disabled="savingGroup === group"
                  @click="saveGroup(group)"
                >
                  <span v-if="savingGroup === group" class="spinner-border spinner-border-sm me-1"></span>
                  <i v-else class="bi bi-cloud-arrow-up-fill me-1"></i>
                  {{ savingGroup === group ? 'Saving Changes…' : `Save ${group}` }}
                </button>
              </div>
            </section>
          </div>
        </template>

        <!-- 2. Dedicated Contact Page Settings Tab -->
        <template v-else-if="tab === 'contact-page'">
          <div class="admin-page-header">
            <div>
              <h1>Contact Page & Communication Settings</h1>
              <p>Configure direct support lines, WhatsApp numbers, office hours, location details, and campus FAQs.</p>
            </div>
          </div>

          <div class="content-groups">
            <section
              v-for="card in contactCards"
              :key="card.id"
              class="admin-section-card"
            >
              <!-- Card Header Action Bar -->
              <div class="admin-section-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-100">
                <div class="admin-section-title-wrap">
                  <h2 class="admin-section-title">
                    <i :class="['bi', card.icon]"></i>
                    {{ card.title }}
                  </h2>
                  <p class="admin-section-desc text-muted small">
                    {{ card.description }}
                  </p>
                </div>
                <button
                  class="btn-section-save"
                  :disabled="savingGroup === card.id"
                  @click="saveContactCard(card)"
                >
                  <span v-if="savingGroup === card.id" class="spinner-border spinner-border-sm me-1"></span>
                  <i v-else class="bi bi-cloud-arrow-up-fill me-1"></i>
                  {{ savingGroup === card.id ? 'Saving Changes…' : `Save ${card.title}` }}
                </button>
              </div>

              <!-- 2-Column Responsive Form Grid -->
              <div class="admin-form-grid grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div
                  v-for="item in getContactCardItems(card)"
                  :key="item.id"
                  class="admin-field-group"
                  :class="{ 'full-width col-span-full': isFullWidthField(item) }"
                >
                  <label class="admin-field-label">
                    <span class="field-label-title">{{ item.label }}</span>
                    <code class="field-key-badge">{{ item.key }}</code>
                  </label>

                  <!-- Multi-line Textarea -->
                  <div v-if="isTextareaField(item)" class="admin-input-wrap">
                    <textarea
                      v-model="item.value"
                      v-auto-expand
                      class="admin-textarea"
                      rows="3"
                      :placeholder="item.label"
                      @input="autoResize($event.target)"
                      @blur="saveText(item)"
                    ></textarea>
                    <button
                      class="inline-save-btn"
                      :title="savedFields[item.id] ? 'Saved' : 'Save field'"
                      @click="saveText(item)"
                    >
                      <i
                        :class="[
                          'bi',
                          savedFields[item.id]
                            ? 'bi-check-all text-success'
                            : item.saving
                            ? 'spinner-border spinner-border-sm'
                            : 'bi-check-lg',
                        ]"
                      ></i>
                    </button>
                  </div>

                  <!-- Single-line Text Input -->
                  <div v-else class="admin-input-wrap">
                    <input
                      v-model="item.value"
                      type="text"
                      class="admin-input"
                      :placeholder="item.label"
                      @blur="saveText(item)"
                      @keyup.enter="saveText(item)"
                    />
                    <button
                      class="inline-save-btn"
                      :title="savedFields[item.id] ? 'Saved' : 'Save field'"
                      @click="saveText(item)"
                    >
                      <i
                        :class="[
                          'bi',
                          savedFields[item.id]
                            ? 'bi-check-all text-success'
                            : item.saving
                            ? 'spinner-border spinner-border-sm'
                            : 'bi-check-lg',
                        ]"
                      ></i>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Card Footer Action Bar -->
              <div class="admin-section-footer flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-4 border-t border-slate-100">
                <div class="footer-meta-tip text-muted small">
                  <i class="bi bi-info-circle me-1"></i> You can save the entire section or blur fields to auto-save individually.
                </div>
                <button
                  class="btn-section-save"
                  :disabled="savingGroup === card.id"
                  @click="saveContactCard(card)"
                >
                  <span v-if="savingGroup === card.id" class="spinner-border spinner-border-sm me-1"></span>
                  <i v-else class="bi bi-cloud-arrow-up-fill me-1"></i>
                  {{ savingGroup === card.id ? 'Saving Changes…' : `Save ${card.title}` }}
                </button>
              </div>
            </section>

            <!-- Resilient Fallback for Any Unassigned Contact Keys -->
            <section
              v-if="unassignedContactItems.length"
              class="admin-section-card"
            >
              <div class="admin-section-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-100">
                <div class="admin-section-title-wrap">
                  <h2 class="admin-section-title">
                    <i class="bi bi-sliders"></i>
                    Additional Contact Keys
                  </h2>
                  <p class="admin-section-desc text-muted small">
                    Other configured dynamic contact values
                  </p>
                </div>
                <button
                  class="btn-section-save"
                  :disabled="savingGroup === 'extra-contact'"
                  @click="saveUnassignedContact"
                >
                  <span v-if="savingGroup === 'extra-contact'" class="spinner-border spinner-border-sm me-1"></span>
                  <i v-else class="bi bi-cloud-arrow-up-fill me-1"></i>
                  Save Additional Keys
                </button>
              </div>
              <div class="admin-form-grid grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div
                  v-for="item in unassignedContactItems"
                  :key="item.id"
                  class="admin-field-group"
                  :class="{ 'full-width col-span-full': isFullWidthField(item) }"
                >
                  <label class="admin-field-label">
                    <span class="field-label-title">{{ item.label }}</span>
                    <code class="field-key-badge">{{ item.key }}</code>
                  </label>
                  <div v-if="isTextareaField(item)" class="admin-input-wrap">
                    <textarea
                      v-model="item.value"
                      v-auto-expand
                      class="admin-textarea"
                      rows="3"
                      :placeholder="item.label"
                      @input="autoResize($event.target)"
                      @blur="saveText(item)"
                    ></textarea>
                    <button class="inline-save-btn" @click="saveText(item)">
                      <i :class="['bi', savedFields[item.id] ? 'bi-check-all text-success' : item.saving ? 'spinner-border spinner-border-sm' : 'bi-check-lg']"></i>
                    </button>
                  </div>
                  <div v-else class="admin-input-wrap">
                    <input
                      v-model="item.value"
                      type="text"
                      class="admin-input"
                      :placeholder="item.label"
                      @blur="saveText(item)"
                      @keyup.enter="saveText(item)"
                    />
                    <button class="inline-save-btn" @click="saveText(item)">
                      <i :class="['bi', savedFields[item.id] ? 'bi-check-all text-success' : item.saving ? 'spinner-border spinner-border-sm' : 'bi-check-lg']"></i>
                    </button>
                  </div>
                </div>
              </div>
            </section>
          </div>
        </template>

        <!-- 2. Other Data Tabs (Programs, About, Fees, Faculty, Events, etc.) -->
        <template v-else>
          <div class="admin-title">
            <div>
              <h1>{{ tabs.find((t) => t[0] === tab)?.[1] }}</h1>
              <p>{{ items.length }} records</p>
            </div>
            <button
              v-if="!['inquiries', 'applications', 'messages'].includes(tab)"
              class="btn btn-primary"
              @click="add"
            >
              <i class="bi bi-plus-lg"></i> Add New
            </button>
          </div>

          <!-- Application Filters -->
          <section v-if="tab === 'applications'" class="admin-card application-filters">
            <div>
              <label for="filter-program">Program</label>
              <select
                id="filter-program"
                v-model="applicationFilters.program_id"
                class="form-select"
                @change="load"
              >
                <option value="">All programs</option>
                <option v-for="program in filterOptions.programs" :key="program.id" :value="program.id">
                  {{ program.name }}
                </option>
              </select>
            </div>
            <div>
              <label for="filter-year">Submission year</label>
              <select
                id="filter-year"
                v-model="applicationFilters.year"
                class="form-select"
                @change="load"
              >
                <option value="">All years</option>
                <option v-for="year in filterOptions.years" :key="year" :value="year">
                  {{ year }}
                </option>
              </select>
            </div>
            <button
              class="btn btn-outline-secondary"
              :disabled="!applicationFilters.program_id && !applicationFilters.year"
              @click="clearApplicationFilters"
            >
              <i class="bi bi-x-circle"></i> Clear filters
            </button>
          </section>

          <!-- Records Table -->
          <section class="admin-card table-responsive">
            <table class="table align-middle">
              <thead>
                <tr>
                  <th>Name / Title</th>
                  <th>Details</th>
                  <th>Status</th>
                  <th class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in items" :key="item.id">
                  <td>
                    <b>{{ item.name || item.title }}</b>
                    <small v-if="item.email">{{ item.email }}</small>
                    <small v-if="item.reference">{{ item.reference }}</small>
                  </td>
                  <td>
                    {{
                      item.description ||
                      item.excerpt ||
                      item.body ||
                      item.qualification ||
                      item.program ||
                      item.duration
                    }}
                  </td>
                  <td>
                    <span class="status">
                      {{
                        (
                          item.status ||
                          ((item.is_active ?? item.is_published) ? 'active' : 'hidden')
                        ).replaceAll('_', ' ')
                      }}
                    </span>
                  </td>
                  <td class="text-end">
                    <a
                      v-if="tab === 'applications'"
                      class="icon-btn d-inline-grid place-items-center"
                      :href="`/api/admin/applications/${item.id}/print`"
                      target="_blank"
                      title="View and print"
                    >
                      <i class="bi bi-printer"></i>
                    </a>
                    <button
                      class="icon-btn"
                      @click="edit(item)"
                      :title="tab === 'applications' ? 'Review application' : 'Edit'"
                    >
                      <i :class="['bi', tab === 'applications' ? 'bi-eye' : 'bi-pencil']"></i>
                    </button>
                    <button
                      v-if="!['inquiries', 'applications'].includes(tab)"
                      class="icon-btn danger"
                      @click="remove(item)"
                    >
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>
                <tr v-if="!items.length">
                  <td colspan="4" class="text-center py-5 text-muted">No records found.</td>
                </tr>
              </tbody>
            </table>
          </section>
        </template>
      </div>
    </div>
  </div>

  <!-- Content Editor Modal -->
  <div
    id="contentEditorModal"
    class="modal fade"
    tabindex="-1"
    aria-labelledby="contentEditorTitle"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div v-if="editing && tab !== 'applications'" class="modal-content">
        <form @submit.prevent="save">
          <div class="modal-header">
            <div>
              <span class="review-reference">{{ tabs.find((t) => t[0] === tab)?.[1] }}</span>
              <h2 id="contentEditorTitle" class="modal-title">
                {{ editing.id ? 'Edit' : 'Add' }} {{ tabs.find((t) => t[0] === tab)?.[1] }}
              </h2>
              <p class="text-muted small mb-0">Manage this record without leaving the current dashboard view.</p>
            </div>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
              @click="editing = null"
            ></button>
          </div>
          <div class="modal-body">
            <div class="row g-3">
              <div
                v-for="f in currentFields"
                :key="f[0]"
                :class="f[2] === 'textarea' || f[2] === 'file' ? 'col-12' : 'col-md-6'"
              >
                <label class="form-label fw-bold small">{{ f[1] }}</label>
                <textarea
                  v-if="f[2] === 'textarea'"
                  v-model="editing[f[0]]"
                  class="form-control"
                  rows="4"
                />
                <select
                  v-else-if="f[2] === 'program'"
                  v-model="editing[f[0]]"
                  class="form-select"
                  required
                >
                  <option value="" disabled>Select a program</option>
                  <option v-for="program in feePrograms" :key="program.id" :value="program.id">
                    {{ program.name }}
                  </option>
                </select>
                <select
                  v-else-if="f[2] === 'select'"
                  v-model="editing[f[0]]"
                  class="form-select"
                >
                  <option v-for="s in statusChoices" :key="s" :value="s">
                    {{ s.replaceAll('_', ' ') }}
                  </option>
                </select>
                <template v-else-if="f[2] === 'file'">
                  <div class="mb-2">
                    <img
                      v-if="editing.previewUrl || editing.image_url"
                      class="admin-preview rounded"
                      :src="editing.previewUrl || editing.image_url"
                      alt="Current image"
                      style="max-height: 100px; object-fit: cover;"
                    />
                  </div>
                  <input
                    type="file"
                    class="form-control"
                    accept="image/jpeg,image/png,image/webp"
                    @change="chooseImage(editing, $event)"
                  />
                  <small class="text-muted">JPG, PNG or WebP, maximum 5 MB.</small>
                </template>
                <label v-else-if="f[2] === 'checkbox'" class="form-check form-switch mt-4">
                  <input
                    v-model="editing[f[0]]"
                    class="form-check-input"
                    type="checkbox"
                  />
                  Enabled
                </label>
                <input
                  v-else
                  v-model="editing[f[0]]"
                  :type="f[2] || 'text'"
                  class="form-control"
                  required
                />
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-light"
              data-bs-dismiss="modal"
              @click="editing = null"
            >
              Cancel
            </button>
            <button type="submit" class="btn btn-primary" :disabled="busy">
              <span v-if="busy" class="spinner-border spinner-border-sm me-2"></span>
              {{ editing.id ? 'Save Changes' : 'Create Record' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Application Review Modal -->
  <div
    id="applicationReviewModal"
    class="modal fade"
    tabindex="-1"
    aria-labelledby="applicationReviewTitle"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div v-if="editing && tab === 'applications'" class="modal-content">
        <div class="modal-header">
          <div>
            <span class="review-reference">{{ editing.reference }}</span>
            <h2 id="applicationReviewTitle" class="modal-title">
              {{ editing.first_name }} {{ editing.last_name }}
            </h2>
            <p class="text-muted small mb-0">
              {{ editing.program }} · Submitted
              {{ new Date(editing.submitted_at).toLocaleDateString() }}
            </p>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="review-grid">
            <section>
              <h3>Personal Information</h3>
              <dl>
                <div>
                  <dt>Date of birth</dt>
                  <dd>{{ editing.date_of_birth }}</dd>
                </div>
                <div>
                  <dt>Gender</dt>
                  <dd>{{ editing.gender?.replaceAll('_', ' ') }}</dd>
                </div>
                <div>
                  <dt>Email</dt>
                  <dd>{{ editing.email }}</dd>
                </div>
                <div>
                  <dt>Phone</dt>
                  <dd>{{ editing.phone }}</dd>
                </div>
                <div>
                  <dt>National ID / Passport</dt>
                  <dd>{{ editing.national_id || 'Not provided' }}</dd>
                </div>
                <div class="wide">
                  <dt>Address</dt>
                  <dd>{{ editing.address }}, {{ editing.city }}, {{ editing.country }}</dd>
                </div>
              </dl>
            </section>
            <section>
              <h3>Educational Background</h3>
              <dl>
                <div>
                  <dt>Qualification</dt>
                  <dd>{{ editing.highest_qualification }}</dd>
                </div>
                <div>
                  <dt>Institution</dt>
                  <dd>{{ editing.institution }}</dd>
                </div>
                <div>
                  <dt>Graduation year</dt>
                  <dd>{{ editing.graduation_year }}</dd>
                </div>
                <div>
                  <dt>Grade</dt>
                  <dd>{{ editing.grade_percentage ? editing.grade_percentage + '%' : 'Not provided' }}</dd>
                </div>
              </dl>
              <template v-if="editing.statement">
                <h3 class="mt-4">Personal Statement</h3>
                <p class="review-statement">{{ editing.statement }}</p>
              </template>
            </section>
          </div>
          <section class="review-controls mt-4">
            <h3>Admissions Review</h3>
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label fw-bold small">Status</label>
                <select v-model="editing.status" class="form-select">
                  <option v-for="status in statusChoices" :key="status" :value="status">
                    {{ status.replaceAll('_', ' ') }}
                  </option>
                </select>
              </div>
              <div class="col-md-8">
                <label class="form-label fw-bold small">Internal notes</label>
                <textarea
                  v-model="editing.admin_notes"
                  class="form-control"
                  rows="3"
                  placeholder="Notes visible only to administrators"
                ></textarea>
              </div>
            </div>
          </section>
        </div>
        <div class="modal-footer justify-content-between">
          <div class="d-flex gap-2 flex-wrap">
            <a
              class="btn btn-outline-secondary"
              :href="`/api/admin/applications/${editing.id}/documents/transcript`"
            >
              <i class="bi bi-download"></i> Transcript
            </a>
            <a
              class="btn btn-outline-secondary"
              :href="`/api/admin/applications/${editing.id}/documents/identity`"
            >
              <i class="bi bi-download"></i> Identity Document
            </a>
            <a
              class="btn btn-outline-primary"
              :href="`/api/admin/applications/${editing.id}/print`"
              target="_blank"
            >
              <i class="bi bi-printer"></i> Print
            </a>
          </div>
          <div>
            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" :disabled="busy" @click="save">
              <span v-if="busy" class="spinner-border spinner-border-sm me-2"></span>Save Review
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
