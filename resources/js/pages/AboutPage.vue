<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import SectionTitle from '../components/SectionTitle.vue';
import SiteFooter from '../components/SiteFooter.vue';
import SiteHeader from '../components/SiteHeader.vue';
import { getCollegeContent } from '../services/api';

const data = ref({ site: {}, programs: [], faculty: [], milestones: [], about_sections: [] });
const loading = ref(true);
const previewImage = ref(null);
const value = (key, fallback) => data.value.site?.[key] || fallback;

const INSTITUTIONAL_STORY_TITLE = 'Our History & Mission';
const INSTITUTIONAL_STORY_BODY =
  'Founded with a vision to transform healthcare education in the region, Dir College of Nursing & Allied Health Sciences was established to address the critical demand for skilled, ethical, and compassionate medical personnel. Affiliated with statutory healthcare councils and accredited teaching hospitals, our institution prepares graduates for clinical excellence through rigorous academics, modern simulation laboratories, and extensive bedside patient care training.';

const fallbackSections = computed(() => [
  {
    id: 'story',
    title: value('about_title', INSTITUTIONAL_STORY_TITLE),
    body: value('about_text', INSTITUTIONAL_STORY_BODY),
    image_url: value('about_image', '/images/nursing-hero.png'),
    image_position: 'left',
  },
]);

const resolveImageUrl = (url) => {
  if (!url) return '/images/nursing-hero.png';
  try {
    if (url.startsWith('http://') || url.startsWith('https://')) {
      const parsed = new URL(url);
      if (parsed.pathname.startsWith('/storage/')) {
        return parsed.pathname;
      }
    }
  } catch {
    // fallback to original url
  }
  return url;
};

const sanitizeSection = (sec) => {
  let title = sec.title;
  let body = sec.body;

  if (!title || /when we established/i.test(title)) {
    title = INSTITUTIONAL_STORY_TITLE;
  }
  if (!body || /some text about the college/i.test(body)) {
    body = INSTITUTIONAL_STORY_BODY;
  }

  return {
    ...sec,
    title,
    body,
  };
};

const aboutSections = computed(() => {
  const sections = data.value.about_sections?.length ? data.value.about_sections : fallbackSections.value;
  return sections.map(sanitizeSection);
});

const openPreview = (url, title = '') => {
  if (!url) return;
  previewImage.value = { url, title };
  if (typeof document !== 'undefined') {
    document.body.style.overflow = 'hidden';
  }
};

const closePreview = () => {
  previewImage.value = null;
  if (typeof document !== 'undefined') {
    document.body.style.overflow = '';
  }
};

const handleKeydown = (event) => {
  if (event.key === 'Escape' && previewImage.value) {
    closePreview();
  }
};

onMounted(async () => {
  window.addEventListener('keydown', handleKeydown);
  try {
    const res = await getCollegeContent();
    data.value = res.data.data;
    document.title = `About | ${value('college_name', 'Dir College Of Nursing')}`;
  } finally {
    loading.value = false;
  }
});

onBeforeUnmount(() => {
  window.removeEventListener('keydown', handleKeydown);
  if (typeof document !== 'undefined') {
    document.body.style.overflow = '';
  }
});
</script>

<template>
  <SiteHeader :site="data.site" active="about us" />
  <main class="page-content-wrapper">
    <!-- Institutional Story & Mission Section -->
    <section class="section about-dynamic pt-4 pt-md-5">
      <div class="container">
        <nav aria-label="breadcrumb" class="mb-3">
          <ol class="breadcrumb custom-breadcrumb">
            <li class="breadcrumb-item"><router-link to="/"><i class="bi bi-house-door me-1"></i>Home</router-link></li>
            <li class="breadcrumb-item active" aria-current="page">About Us</li>
          </ol>
        </nav>
        <div class="about-page-header mb-5">
          <span class="eyebrow">ABOUT OUR COLLEGE</span>
          <h1 class="page-title mb-2">{{ value('college_name', 'Dir College Of Nursing and Allied Health Sciences') }}</h1>
          <p class="text-muted mb-0" style="max-width: 760px; font-size: 16px; line-height: 1.6;">
            {{ value('about_text', 'Our college combines academic excellence, practical clinical training, modern facilities, and compassionate mentorship to prepare students for meaningful healthcare careers.') }}
          </p>
        </div>

        <div class="about-section-stack">
          <article
            v-for="(section, index) in aboutSections"
            :key="section.id || section.title"
            class="about-story-block"
            :class="{ 'is-reversed': section.image_position === 'right' }"
          >
            <div
              class="about-story-media"
              role="button"
              tabindex="0"
              :aria-label="`View full image: ${section.title}`"
              @click="openPreview(resolveImageUrl(section.image_url || value('about_image', '/images/nursing-hero.png')), section.title)"
              @keydown.enter="openPreview(resolveImageUrl(section.image_url || value('about_image', '/images/nursing-hero.png')), section.title)"
            >
              <img
                class="about-page-image"
                :src="resolveImageUrl(section.image_url || value('about_image', '/images/nursing-hero.png'))"
                :alt="section.title"
                loading="lazy"
              >
              <span class="about-media-badge">
                <i class="bi bi-arrows-fullscreen"></i> View Full
              </span>
            </div>
            <div class="about-story-copy">
              <span class="eyebrow">{{ index === 0 ? 'OUR STORY & MISSION' : 'ABOUT OUR INSTITUTION' }}</span>
              <h2>{{ section.title }}</h2>
              <p>{{ section.body }}</p>
              <div v-if="index === 0" class="d-flex flex-wrap gap-3 mt-4">
                <router-link to="/apply" class="btn btn-primary">
                  Apply for Admission <i class="bi bi-arrow-right ms-1"></i>
                </router-link>
                <router-link to="/programs" class="btn btn-outline-secondary">
                  Explore Programs
                </router-link>
              </div>
            </div>
          </article>
        </div>
      </div>
    </section>

    <!-- Institutional Value Cards -->
    <section class="section section-soft">
      <div class="container">
        <SectionTitle
          title="What Makes Us Different"
          text="A disciplined and supportive learning environment built for students who demand academic rigor and authentic healthcare practice."
        />
        <div class="row g-4">
          <div
            v-for="item in [
              ['bi-hospital-fill', 'Clinical Hospital Rotations', 'Students gain immersive, real-world clinical experience and patient bedside care through affiliated tertiary teaching hospitals.'],
              ['bi-mortarboard-fill', 'Accredited Academic Rigor', 'Structured nursing and allied health curricula aligned with national statutory council standards and medical board guidelines.'],
              ['bi-diagram-3-fill', 'Advanced Simulation Labs', 'High-fidelity anatomical models, patient simulation mannequins, and medical equipment for safe hands-on procedural mastery.'],
              ['bi-award-fill', 'Licensure & Career Pathways', 'Dedicated licensing examination preparation, compassionate mentorship, and strong healthcare facility placement support.']
            ]"
            :key="item[1]"
            class="col-md-6 col-lg-3"
          >
            <article class="about-value-card">
              <i :class="['bi', item[0]]"></i>
              <h3>{{ item[1] }}</h3>
              <p>{{ item[2] }}</p>
            </article>
          </div>
        </div>
      </div>
    </section>

    <!-- Campus Visit / Call to Action -->
    <section class="about-cta">
      <div class="container text-center">
        <span class="eyebrow text-info">VISIT OUR CAMPUS</span>
        <h2>See the environment where your healthcare journey begins.</h2>
        <p class="mb-4">Schedule a personal campus tour, consult our admissions counselors, or start your online application today.</p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
          <router-link to="/contact" class="btn btn-primary btn-lg">Contact Admissions</router-link>
          <router-link to="/apply" class="btn btn-outline-light btn-lg">Apply Online</router-link>
        </div>
      </div>
    </section>
  </main>
  <SiteFooter :site="data.site" />
  <!-- Lightbox Modal for Full Image View -->
  <Teleport to="body">
    <div
      v-if="previewImage"
      class="about-lightbox-backdrop"
      role="dialog"
      aria-modal="true"
      :aria-label="previewImage.title || 'Image Preview'"
      @click.self="closePreview"
    >
      <div class="about-lightbox-content">
        <button
          type="button"
          class="about-lightbox-close"
          aria-label="Close preview"
          @click="closePreview"
        >
          <i class="bi bi-x-lg"></i>
        </button>
        <img
          :src="previewImage.url"
          :alt="previewImage.title || 'Enlarged Image'"
          class="about-lightbox-img"
        >
      </div>
    </div>
  </Teleport>
</template>
