<script setup>
import { computed, onMounted, ref } from 'vue';
import SectionTitle from '../components/SectionTitle.vue';
import SiteFooter from '../components/SiteFooter.vue';
import SiteHeader from '../components/SiteHeader.vue';
import { getCollegeContent, sendInquiry } from '../services/api';

const content = ref({ site: {}, programs: [], faculty: [], events: [], milestones: [] });
const s = (key, fallback) => content.value.site?.[key] || fallback;
const loading = ref(true);
const form = ref({ name: '', email: '', phone: '', program: '' });
const submitting = ref(false);
const success = ref('');
const errors = ref({});

const fallback = {
  programs: [
    { name: 'BS Nursing', duration: '4 Years', description: 'Build the clinical judgment and leadership skills to become a registered nurse.' },
    { name: 'Post RN BSN', duration: '2 Years', description: 'Advance your practice with a flexible degree for registered nurses.' },
    { name: 'General Nursing', duration: '3 Years', description: 'Comprehensive foundational nursing education with practical training.' },
    { name: 'Nursing Diploma', duration: '2 Years', description: 'Focused professional training for specialized healthcare roles.' },
  ],
  faculty: [],
  events: [],
  milestones: ['Choose Your Program', 'Check Eligibility', 'Submit Application', 'Begin Your Journey'],
};

const features = [
  ['bi-person-badge', 'Experienced Nursing Faculty', 'Learn from qualified healthcare educators and experienced professionals.'],
  ['bi-hospital', 'Modern Nursing Labs', 'Practice essential clinical skills in realistic simulation environments.'],
  ['bi-clipboard2-pulse', 'Hospital Clinical Training', 'Gain hands-on experience in professional healthcare environments.'],
  ['bi-mortarboard', 'Career-Focused Education', 'Develop practical skills that prepare you for the healthcare workforce.'],
];

const heroBackgroundStyle = computed(() => {
  const img = content.value.site?.hero_image;
  return img ? { backgroundImage: `linear-gradient(90deg, rgba(4,25,42,.97), rgba(4,25,42,.15)), url('${img}')` } : {};
});

onMounted(async () => {
  try {
    const res = await getCollegeContent();
    content.value = res.data.data;
    if (content.value.site?.college_name) {
      document.title = content.value.site.college_name;
    }
  } catch {
    content.value = fallback;
  } finally {
    loading.value = false;
  }
});

async function submit() {
  submitting.value = true;
  errors.value = {};
  success.value = '';
  try {
    await sendInquiry(form.value);
    success.value = 'Thank you! An admissions advisor will contact you shortly.';
    form.value = { name: '', email: '', phone: '', program: '' };
  } catch (e) {
    errors.value = e.response?.data?.errors || { form: ['Unable to submit. Please try again.'] };
  } finally {
    submitting.value = false;
  }
}
</script>

<template>
  <SiteHeader :site="content.site" />
  <main id="home">
    <section class="hero" :style="heroBackgroundStyle">
      <div class="container hero-inner">
        <div class="hero-copy">
          <div class="hero-kicker">{{ s('hero_kicker', 'Compassion. Knowledge. Excellence.') }}</div>
          <h1 v-html="s('hero_title', 'Build Your Future<br>in Nursing')"></h1>
          <p>{{ s('hero_text', "Gain the knowledge, clinical skills, and confidence to make a meaningful difference in people's lives.") }}</p>
          <div class="d-flex gap-3 flex-wrap">
            <router-link class="btn btn-primary btn-lg" to="/apply">{{ s('hero_primary_cta', 'Apply Now') }}</router-link>
            <a class="btn btn-outline-light btn-lg" href="#programs">{{ s('hero_secondary_cta', 'Explore Nursing Programs') }}</a>
          </div>
          <div class="hero-meta">
            <span><i class="bi bi-check-circle-fill"></i> Professional Nursing Education</span>
            <span>• Clinical Training</span>
            <span>• Career Development</span>
          </div>
        </div>
        <div class="hero-stats">
          <div><strong>20+</strong><span>Years of Excellence</span></div>
          <div><strong>2,500+</strong><span>Graduates</span></div>
          <div><strong>95%</strong><span>Employment Success</span></div>
          <div><strong>25+</strong><span>Clinical Faculty</span></div>
        </div>
      </div>
    </section>

    <section class="quick-links">
      <div class="container">
        <div class="row g-3">
          <div class="col-6 col-lg-3" v-for="(x, i) in [['Admissions', 'Start your application'], ['Nursing Programs', 'Explore our academic programs'], ['Scholarships', 'Discover financial support'], ['Clinical Training', 'Learn about hands-on training']]" :key="x[0]">
            <router-link v-if="i === 0" class="quick-card" to="/apply">
              <i :class="['bi', features[i][0]]"></i>
              <b>{{ x[0] }}</b>
              <span>{{ x[1] }}</span>
              <em>0{{ i + 1 }}</em>
            </router-link>
            <a v-else class="quick-card" :href="i === 1 ? '#programs' : i === 2 ? '#admissions' : '#clinical-training'">
              <i :class="['bi', features[i][0]]"></i>
              <b>{{ x[0] }}</b>
              <span>{{ x[1] }}</span>
              <em>0{{ i + 1 }}</em>
            </a>
          </div>
        </div>
      </div>
    </section>

    <section id="about-us" class="section">
      <div class="container">
        <div class="row align-items-center g-5">
          <div class="col-lg-6">
            <div class="about-image">
              <img :src="s('about_image', '/images/nursing-hero.png')" alt="Nursing students practicing in a modern clinical lab">
            </div>
          </div>
          <div class="col-lg-6">
            <span class="eyebrow">ABOUT OUR COLLEGE</span>
            <h2 v-html="s('about_title', 'Preparing Compassionate<br>Healthcare Professionals')"></h2>
            <p>{{ s('about_text', 'Our nursing college combines academic excellence, hands-on clinical education, modern facilities, and compassionate mentorship to prepare students for meaningful careers in healthcare.') }}</p>
            <div class="mini-stats">
              <div><b>20+</b><span>Years of Excellence</span></div>
              <div><b>2,500+</b><span>Graduates</span></div>
              <div><b>25+</b><span>Qualified Faculty</span></div>
            </div>
            <router-link to="/about" class="btn btn-dark">Discover Our Story <i class="bi bi-arrow-right"></i></router-link>
          </div>
        </div>
      </div>
    </section>

    <section id="programs" class="section section-soft">
      <div class="container">
        <SectionTitle
          :title="s('programs_title', 'Explore Our Nursing Programs')"
          :text="s('programs_text', 'Choose a program designed to build the knowledge, clinical skills, and professional confidence needed for a successful nursing career.')"
        />
        <div class="row g-4">
          <div class="col-md-6 col-xl-3" v-for="(p, i) in (content.programs.length ? content.programs : fallback.programs).slice(0, 4)" :key="p.name">
            <article class="program-card">
              <div class="program-photo" :style="{ backgroundPosition: `${40 + i * 12}% center` }">
                <img :src="p.image_url || '/images/nursing-hero.png'" :alt="p.name + ' clinical training'">
              </div>
              <div class="p-4">
                <div class="d-flex justify-content-between">
                  <h3>{{ p.name }}</h3>
                  <small>{{ p.duration }}</small>
                </div>
                <p>{{ p.description }}</p>
                <router-link to="/programs">View Program <i class="bi bi-arrow-right"></i></router-link>
              </div>
            </article>
          </div>
        </div>
        <div class="text-center mt-4">
          <router-link to="/programs" class="link-arrow">View All Programs <i class="bi bi-arrow-right"></i></router-link>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="container">
        <SectionTitle title="Why Choose Us?" />
        <div class="row g-4">
          <div class="col-md-6 col-lg-3" v-for="f in features" :key="f[1]">
            <div class="feature">
              <i :class="['bi', f[0]]"></i>
              <h3>{{ f[1] }}</h3>
              <p>{{ f[2] }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="clinical-training" class="clinical">
      <div class="container">
        <div class="row align-items-center g-5">
          <div class="col-lg-6">
            <span class="eyebrow text-info">CLINICAL TRAINING</span>
            <h2 v-html="s('clinical_title', 'Learn Beyond<br>the Classroom')"></h2>
            <h6>Real Clinical Experience. Real Confidence.</h6>
            <p>{{ s('clinical_text', 'Our students develop practical nursing skills through supervised clinical training, simulation exercises, patient-care practice, and real healthcare environments.') }}</p>
            <div class="tags">
              <span>Clinical Simulation</span>
              <span>Patient Care</span>
              <span>Emergency Response</span>
            </div>
            <a class="btn btn-primary mt-4" href="#apply">Explore Clinical Training <i class="bi bi-arrow-right"></i></a>
          </div>
          <div class="col-lg-6">
            <img class="rounded-4 shadow" :src="s('clinical_image', '/images/nursing-hero.png')" alt="Students learning in a clinical simulation laboratory">
          </div>
        </div>
      </div>
    </section>

    <section class="section section-soft">
      <div class="container">
        <div class="row align-items-center g-5">
          <div class="col-lg-5 order-lg-2">
            <span class="eyebrow">MODERN NURSING LABORATORIES</span>
            <h2>Practice. Learn. Master.</h2>
            <p>Our modern simulation laboratories provide students with a safe environment to develop essential clinical skills before entering real healthcare settings.</p>
            <a href="#campus-life" class="btn btn-primary">Explore Our Facilities</a>
          </div>
          <div class="col-lg-7">
            <div class="facility-grid">
              <img v-for="i in 4" :key="i" src="/images/nursing-hero.png" alt="Modern nursing laboratory">
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="admissions" class="section admission">
      <div class="container">
        <SectionTitle
          :title="s('admissions_title', 'Admissions Process')"
          :text="s('admissions_text', 'Our admissions team is ready to help you take the next step toward a rewarding nursing career.')"
        />
        <div class="steps">
          <div v-for="(step, i) in (content.milestones.length ? content.milestones : fallback.milestones)" :key="typeof step === 'string' ? step : step.title" class="step">
            <div class="step-icon">
              <i :class="['bi', ['bi-search-heart', 'bi-person-check', 'bi-file-earmark-text', 'bi-heart-pulse'][i] || 'bi-check-circle']"></i>
            </div>
            <b>0{{ i + 1 }}</b>
            <span>{{ typeof step === 'string' ? step : step.title }}</span>
          </div>
        </div>
        <div class="text-center">
          <router-link to="/apply" class="btn btn-primary">Start Your Application <i class="bi bi-arrow-right"></i></router-link>
        </div>
      </div>
    </section>

    <section class="section scholarship">
      <div class="container">
        <div class="row align-items-center g-5">
          <div class="col-lg-4">
            <img :src="s('scholarship_image', '/images/student-nurse.png')" alt="Smiling nursing student">
          </div>
          <div class="col-lg-8">
            <span class="eyebrow">SCHOLARSHIPS</span>
            <h2 v-html="s('scholarships_title', 'Invest in Your Future')"></h2>
            <p>{{ s('scholarships_text', 'We believe talented students should have the opportunity to pursue quality nursing education.') }}</p>
            <div class="row g-3 checks">
              <div class="col-md-6" v-for="x in ['Merit Scholarships', 'Need-Based Support', 'Student Assistance', 'Academic Excellence Awards']" :key="x">
                <i class="bi bi-check-circle-fill"></i> {{ x }}
              </div>
            </div>
            <a href="#apply" class="btn btn-primary mt-4">Explore Scholarships</a>
          </div>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="container">
        <SectionTitle title="Meet Our Nursing Faculty" />
        <div class="row g-4">
          <div class="col-6 col-lg-3" v-for="(f, i) in (content.faculty.length ? content.faculty : [{ name: 'Dr. Sarah Bennett', title: 'Dean of Nursing' }, { name: 'Prof. Michael Chen', title: 'Clinical Educator' }, { name: 'Dr. Amina Brooks', title: 'Simulation Director' }, { name: 'Prof. Elena Rossi', title: 'Nursing Faculty' }])" :key="f.name">
            <article class="faculty-card">
              <div class="faculty-avatar">
                <img :src="f.image_url || (i === 2 ? '/images/student-nurse.png' : '/images/nursing-hero.png')" :alt="f.name">
              </div>
              <h3>{{ f.name }}</h3>
              <b>{{ f.title }}</b>
              <span>{{ f.qualification || 'RN, MSN • 10+ years' }}</span>
            </article>
          </div>
        </div>
      </div>
    </section>

    <section class="outcomes">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-7">
            <h2 v-html="s('outcomes_title', 'Career Outcomes')"></h2>
            <h5>Your Nursing Career Starts Here</h5>
            <div class="row">
              <div class="col-6" v-for="x in ['Registered Nurse', 'Emergency Care Nurse', 'Clinical Nurse', 'Nursing Educator', 'Hospital Nurse', 'Healthcare Coordinator']" :key="x">
                <i class="bi bi-check2"></i> {{ x }}
              </div>
            </div>
          </div>
          <div class="col-lg-5 text-lg-end success-stat">
            <i class="bi bi-graph-up-arrow"></i>
            <b>{{ s('outcomes_stat', '95%') }}</b>
            <span>Graduate Career Success</span>
          </div>
        </div>
      </div>
    </section>

    <section id="news-and-events" class="section">
      <div class="container">
        <SectionTitle title="Latest News & Events" />
        <div class="row g-4">
          <div class="col-md-4" v-for="e in (content.events.length ? content.events : [{ slug: 'admissions-open-2026', title: 'Admissions Open for 2026', excerpt: 'Start your application and begin your nursing journey.' }, { slug: 'nursing-skills-competition', title: 'Annual Nursing Skills Competition', excerpt: 'Celebrating practical excellence and teamwork.' }, { slug: 'clinical-excellence-awards', title: 'Student Clinical Excellence Awards', excerpt: 'Recognizing outstanding achievement in clinical education.' }]).slice(0, 3)" :key="e.title">
            <router-link class="news-card-link" :to="`/news-events/${e.slug}`" :aria-label="`Read ${e.title}`">
              <article class="news-card">
                <img :src="e.image_url || '/images/nursing-hero.png'" :alt="e.title">
                <div class="p-4">
                  <small>{{ content.site.college_short_name || content.site.college_name || 'College' }} · {{ e.event_date ? String(e.event_date).slice(0, 4) : '2026' }}</small>
                  <h3>{{ e.title }}</h3>
                  <p>{{ e.excerpt }}</p>
                  <span class="news-read-more">Read More <i class="bi bi-arrow-right"></i></span>
                </div>
              </article>
            </router-link>
          </div>
        </div>
        <div class="text-center mt-4">
          <router-link to="/news-events" class="link-arrow">View All News <i class="bi bi-arrow-right"></i></router-link>
        </div>
      </div>
    </section>

    <section id="campus-life" class="campus">
      <div class="container">
        <div class="row align-items-center g-4">
          <div class="col-lg-7">
            <div class="video-card">
              <img :src="s('campus_image', '/images/nursing-hero.png')" alt="Nursing campus">
              <button aria-label="Play virtual campus tour"><i class="bi bi-play-fill"></i></button>
            </div>
          </div>
          <div class="col-lg-5">
            <span class="eyebrow text-info">VIRTUAL CAMPUS TOUR</span>
            <h2>Experience<br>Our Campus</h2>
            <p>Take a closer look at classrooms, laboratories, facilities, and student life.</p>
            <a href="#" class="btn btn-primary">Take a Virtual Tour</a>
          </div>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="container">
        <SectionTitle title="Student Testimonials" />
        <div class="testimonial">
          <img src="/images/student-nurse.png" alt="Nursing graduate">
          <div>
            <i class="bi bi-quote"></i>
            <blockquote>{{ s('testimonial_quote', 'At this college, I learned much more than nursing skills. I learned how to care, communicate, and make a difference.') }}</blockquote>
            <b>{{ s('testimonial_name', 'Rachel Williams') }}</b>
            <span>BS Nursing, Class of 2026</span>
          </div>
        </div>
      </div>
    </section>

    <section id="apply" class="apply-section">
      <div class="container">
        <div class="row g-5 align-items-center">
          <div class="col-lg-5">
            <span class="eyebrow text-info">START YOUR JOURNEY</span>
            <h2 v-html="s('cta_title', 'Your Future in<br>Healthcare Starts Here')"></h2>
            <p>{{ s('cta_text', 'Take the first step toward becoming a skilled, compassionate healthcare professional.') }}</p>
          </div>
          <div class="col-lg-7">
            <form @submit.prevent="submit" class="apply-form">
              <h3>Request Admissions Information</h3>
              <div v-if="success" class="alert alert-success">{{ success }}</div>
              <div v-if="errors.form" class="alert alert-danger">{{ errors.form[0] }}</div>
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="name">Full name</label>
                  <input id="name" v-model="form.name" class="form-control" :class="{ 'is-invalid': errors.name }" required>
                  <div class="invalid-feedback">{{ errors.name?.[0] }}</div>
                </div>
                <div class="col-md-6">
                  <label for="email">Email address</label>
                  <input id="email" v-model="form.email" type="email" class="form-control" :class="{ 'is-invalid': errors.email }" required>
                  <div class="invalid-feedback">{{ errors.email?.[0] }}</div>
                </div>
                <div class="col-md-6">
                  <label for="phone">Phone</label>
                  <input id="phone" v-model="form.phone" class="form-control">
                </div>
                <div class="col-md-6">
                  <label for="program">Program of interest</label>
                  <select id="program" v-model="form.program" class="form-select" required>
                    <option value="" disabled>Select a program</option>
                    <option v-for="p in (content.programs.length ? content.programs : fallback.programs)" :key="p.name">{{ p.name }}</option>
                  </select>
                </div>
              </div>
              <button class="btn btn-primary mt-4" :disabled="submitting">
                <span v-if="submitting" class="spinner-border spinner-border-sm me-2"></span>
                {{ submitting ? 'Submitting…' : 'Talk to Admissions' }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </section>
  </main>
  <SiteFooter :site="content.site" />
</template>
