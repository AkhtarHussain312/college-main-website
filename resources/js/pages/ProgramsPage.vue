<script setup>
import { computed, onMounted, ref } from 'vue';
import FeeBreakdown from '../components/FeeBreakdown.vue';
import SiteFooter from '../components/SiteFooter.vue';
import SiteHeader from '../components/SiteHeader.vue';
import { getCollegeContent } from '../services/api';

const data = ref({ site: {}, programs: [] });
const loading = ref(true);
const error = ref('');

const fallbackPrograms = [
  { id: 1, name: 'BS Nursing', duration: '4 Years', description: 'Build the clinical judgment and leadership skills to become a registered nurse.' },
  { id: 2, name: 'LHV (Lady Health Visitor)', duration: '2 Years', description: 'Essential training for maternal and child healthcare, community midwifery, and primary healthcare services.' },
  { id: 3, name: 'Post RN BSN', duration: '2 Years', description: 'Advance your practice with a flexible degree for registered nurses.' },
  { id: 4, name: 'General Nursing', duration: '3 Years', description: 'Comprehensive foundational nursing education with practical training.' },
  { id: 5, name: 'Nursing Diploma', duration: '2 Years', description: 'Focused professional training for specialized healthcare roles.' },
];

const programsList = computed(() => data.value.programs?.length ? data.value.programs : fallbackPrograms);

onMounted(async () => {
  try {
    const res = await getCollegeContent();
    data.value = res.data.data;
    document.title = `Programs | ${data.value.site?.college_name || 'College of Nursing'}`;
  } catch {
    error.value = 'Programs could not be loaded. Please try again.';
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <SiteHeader :site="data.site" active="programs" />
  <main class="page-content-wrapper">
    <div class="container py-4 py-md-5">
      <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb custom-breadcrumb">
          <li class="breadcrumb-item"><router-link to="/"><i class="bi bi-house-door me-1"></i>Home</router-link></li>
          <li class="breadcrumb-item active" aria-current="page">Programs</li>
        </ol>
      </nav>
      <div class="programs-page-heading mb-4">
        <div>
          <span class="eyebrow">OUR PROGRAMS</span>
          <h1 class="page-title mb-1">Academic Programs</h1>
          <p class="text-muted mb-0">Career-focused education supported by modern laboratories and supervised clinical experience.</p>
        </div>
        <router-link to="/contact" class="btn btn-outline-primary btn-sm"><i class="bi bi-headset me-1"></i> Talk to Admissions</router-link>
      </div>

        <div v-if="error" class="alert alert-danger">{{ error }}</div>
        <div v-else-if="!programsList.length" class="empty-programs">
          <i class="bi bi-journal-medical"></i>
          <h3>No programs are currently published.</h3>
          <p>Please contact admissions for current course information.</p>
        </div>
        <div v-else class="row g-4">
          <div class="col-md-6 col-xl-4 d-flex" v-for="program in programsList" :key="program.id || program.name">
            <article class="program-list-card w-100">
              <div class="program-card-image-wrap">
                <img :src="program.image_url || '/images/nursing-hero.png'" :alt="program.name" loading="lazy">
                <span class="program-duration-badge"><i class="bi bi-clock-history me-1"></i> {{ program.duration }}</span>
              </div>
              <div class="program-list-body">
                <h2>{{ program.name }}</h2>
                <p class="program-description">{{ program.description }}</p>
                <FeeBreakdown v-if="program.fees && program.fees.length" :fees="program.fees" />
                <div v-else class="fee-breakdown fee-breakdown-contact">
                  <div class="fee-heading">
                    <span><i class="bi bi-info-circle"></i> Fee Schedule</span>
                    <small>Admissions</small>
                  </div>
                  <div class="p-3 text-center">
                    <small class="text-muted d-block">Contact admissions office for current semester fee schedule and scholarship opportunities.</small>
                  </div>
                </div>
                <div class="program-actions">
                  <router-link to="/apply" class="btn btn-primary">Apply Now</router-link>
                  <a :href="`mailto:${data.site?.college_email || 'admissions@example.edu'}?subject=Inquiry regarding ${encodeURIComponent(program.name)}`" class="details-link">
                    Request details <i class="bi bi-arrow-right"></i>
                  </a>
                </div>
              </div>
            </article>
          </div>
        </div>
      </div>

    <section class="programs-cta">
      <div class="container text-center">
        <span class="eyebrow text-info">NEED GUIDANCE?</span>
        <h2>Not Sure Which Program Is Right for You?</h2>
        <p>Our admissions team can help you compare programs, entry requirements, and career pathways.</p>
        <router-link to="/#apply" class="btn btn-primary btn-lg">Speak With Admissions</router-link>
      </div>
    </section>
  </main>
  <SiteFooter :site="data.site" />
</template>
