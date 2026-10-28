<script setup>
import { computed, onMounted, ref } from 'vue';
import SiteHeader from '../components/SiteHeader.vue';
import SiteFooter from '../components/SiteFooter.vue';
import FeeBreakdown from '../components/FeeBreakdown.vue';
import { getCollegeContent, submitApplication } from '../services/api';

const data = ref({ site: {}, programs: [] });
const currentStep = ref(1);
const totalSteps = 4;
const busy = ref(false);
const reference = ref('');
const errors = ref({});

const form = ref({
  program_id: '',
  first_name: '',
  last_name: '',
  date_of_birth: '',
  gender: '',
  email: '',
  phone: '',
  national_id: '',
  city: '',
  country: 'Pakistan',
  address: '',
  highest_qualification: '',
  institution: '',
  graduation_year: new Date().getFullYear(),
  grade_percentage: '',
  statement: '',
  transcript: null,
  identity_document: null,
  confirmation: false,
});

const transcriptMeta = ref(null);
const identityMeta = ref(null);
const isTranscriptDragging = ref(false);
const isIdentityDragging = ref(false);

const selectedProgram = computed(() =>
  data.value.programs.find((p) => String(p.id) === String(form.value.program_id))
);

const progressPercentage = computed(() =>
  Math.round((currentStep.value / totalSteps) * 100)
);

const statementCharCount = computed(() => form.value.statement?.length || 0);
const statementProgress = computed(() =>
  Math.min((statementCharCount.value / 3000) * 100, 100)
);

onMounted(async () => {
  try {
    const res = await getCollegeContent();
    data.value = res.data.data;
  } catch {}
  document.title = `Online Application | ${data.value.site?.college_name || 'Dir College Of Nursing'}`;
});

function selectProgram(prog) {
  form.value.program_id = prog.id;
  delete errors.value.program_id;
}

function handleFileDrop(field, event) {
  event.preventDefault();
  if (field === 'transcript') isTranscriptDragging.value = false;
  if (field === 'identity_document') isIdentityDragging.value = false;

  const file = event.dataTransfer?.files?.[0];
  if (file) processFile(field, file);
}

function handleFileInput(field, event) {
  const file = event.target.files?.[0];
  if (file) processFile(field, file);
}

function processFile(field, file) {
  const validTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
  const isPdf = file.name.toLowerCase().endsWith('.pdf');
  const isImg = /\.(jpg|jpeg|png)$/i.test(file.name);

  if (!isPdf && !isImg && !validTypes.includes(file.type)) {
    errors.value[field] = ['Only PDF, JPG, and PNG files are allowed.'];
    return;
  }

  if (file.size > 5 * 1024 * 1024) {
    errors.value[field] = ['File exceeds the 5 MB maximum limit.'];
    return;
  }

  delete errors.value[field];
  form.value[field] = file;

  let formattedSize = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
  if (file.size < 1024 * 1024) {
    formattedSize = Math.round(file.size / 1024) + ' KB';
  }

  const meta = { name: file.name, size: formattedSize };
  if (field === 'transcript') transcriptMeta.value = meta;
  if (field === 'identity_document') identityMeta.value = meta;
}

function removeFile(field) {
  form.value[field] = null;
  if (field === 'transcript') transcriptMeta.value = null;
  if (field === 'identity_document') identityMeta.value = null;
}

function validateStep(step) {
  errors.value = {};
  if (step === 1) {
    if (!form.value.program_id) {
      errors.value.program_id = ['Please select an academic program.'];
      return false;
    }
  } else if (step === 2) {
    let valid = true;
    if (!form.value.first_name?.trim()) {
      errors.value.first_name = ['First name is required.'];
      valid = false;
    }
    if (!form.value.last_name?.trim()) {
      errors.value.last_name = ['Last name is required.'];
      valid = false;
    }
    if (!form.value.date_of_birth) {
      errors.value.date_of_birth = ['Date of birth is required.'];
      valid = false;
    }
    if (!form.value.gender) {
      errors.value.gender = ['Please select your gender.'];
      valid = false;
    }
    if (!form.value.email?.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email)) {
      errors.value.email = ['Valid email is required.'];
      valid = false;
    }
    if (!form.value.phone?.trim()) {
      errors.value.phone = ['Phone number is required.'];
      valid = false;
    }
    if (!form.value.city?.trim()) {
      errors.value.city = ['City is required.'];
      valid = false;
    }
    if (!form.value.country?.trim()) {
      errors.value.country = ['Country is required.'];
      valid = false;
    }
    if (!form.value.address?.trim()) {
      errors.value.address = ['Residential address is required.'];
      valid = false;
    }
    return valid;
  } else if (step === 3) {
    let valid = true;
    if (!form.value.highest_qualification?.trim()) {
      errors.value.highest_qualification = ['Highest qualification is required.'];
      valid = false;
    }
    if (!form.value.institution?.trim()) {
      errors.value.institution = ['Institution name is required.'];
      valid = false;
    }
    if (!form.value.graduation_year) {
      errors.value.graduation_year = ['Graduation year is required.'];
      valid = false;
    }
    return valid;
  } else if (step === 4) {
    let valid = true;
    if (!form.value.transcript) {
      errors.value.transcript = ['Please upload your academic transcript.'];
      valid = false;
    }
    if (!form.value.identity_document) {
      errors.value.identity_document = ['Please upload your ID or Form-B.'];
      valid = false;
    }
    if (!form.value.confirmation) {
      errors.value.confirmation = ['You must accept the legal declaration.'];
      valid = false;
    }
    return valid;
  }
  return true;
}

function nextStep() {
  if (validateStep(currentStep.value)) {
    if (currentStep.value < totalSteps) {
      currentStep.value++;
      window.scrollTo({ top: 120, behavior: 'smooth' });
    }
  }
}

function prevStep() {
  if (currentStep.value > 1) {
    currentStep.value--;
    window.scrollTo({ top: 120, behavior: 'smooth' });
  }
}

async function submit() {
  if (!validateStep(4)) return;

  busy.value = true;
  errors.value = {};
  try {
    const body = new FormData();
    Object.entries(form.value).forEach(([k, v]) => {
      if (v !== null && v !== '') {
        body.append(k, typeof v === 'boolean' ? (v ? '1' : '0') : v);
      }
    });

    const res = await submitApplication(body);
    reference.value = res.data.data.reference;
    window.scrollTo({ top: 0, behavior: 'smooth' });
  } catch (e) {
    errors.value =
      e.response?.data?.errors || {
        form: [e.response?.data?.message || 'Your application could not be submitted. Please check the fields.'],
      };
    // Jump back to step with error
    if (errors.value.program_id) currentStep.value = 1;
    else if (errors.value.first_name || errors.value.email || errors.value.phone) currentStep.value = 2;
    else if (errors.value.highest_qualification || errors.value.statement) currentStep.value = 3;
    else if (errors.value.transcript || errors.value.identity_document || errors.value.confirmation) currentStep.value = 4;
  } finally {
    busy.value = false;
  }
}
</script>

<template>
  <SiteHeader :site="data.site" active="apply" />
  <main>
    <section class="section application-bg pt-4">
      <div class="container">
        <nav aria-label="breadcrumb" class="mb-3">
          <ol class="breadcrumb custom-breadcrumb">
            <li class="breadcrumb-item"><router-link to="/"><i class="bi bi-house-door me-1"></i>Home</router-link></li>
            <li class="breadcrumb-item"><router-link to="/admissions">Admissions</router-link></li>
            <li class="breadcrumb-item active" aria-current="page">Online Application</li>
          </ol>
        </nav>
        <!-- Success State -->
        <div v-if="reference" class="application-success animate__animated animate__fadeIn">
          <div class="application-success-icon">
            <i class="bi bi-check-lg"></i>
          </div>
          <h2>Application Submitted Successfully!</h2>
          <p class="mx-auto" style="max-width: 520px;">
            Thank you for applying to <strong>{{ data.site.college_name || 'Dir College of Nursing' }}</strong>. Your submission is now officially registered in our admissions system.
          </p>

          <div class="success-reference-box">
            <span class="success-reference-label">Your Application Reference Number</span>
            <span class="success-reference-num">{{ reference }}</span>
          </div>

          <div class="alert alert-light border text-start mx-auto p-3 mb-4 rounded-3" style="max-width: 540px;">
            <div class="d-flex gap-3 align-items-start">
              <i class="bi bi-info-circle-fill text-primary fs-5 mt-1"></i>
              <div class="small text-muted">
                <strong class="d-block text-dark mb-1">What Happens Next?</strong>
                Our admissions committee will verify your academic transcripts. You will receive an interview schedule notification via email and SMS within 3 to 5 working days.
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-center gap-3 flex-wrap">
            <router-link to="/" class="btn btn-primary px-4 py-2"><i class="bi bi-house-door-fill me-2"></i>Return to Home</router-link>
            <a :href="'/track?query=' + reference" class="btn btn-outline-primary px-4 py-2"><i class="bi bi-search me-2"></i>Track Application Live</a>
            <button type="button" class="btn btn-outline-secondary px-4 py-2" onclick="window.print()"><i class="bi bi-printer me-2"></i>Print Confirmation</button>
          </div>
        </div>

        <!-- Multi-Step Form Wizard -->
        <div v-else class="wizard-card" id="applicationWizard">
          
          <!-- Wizard Top Bar -->
          <div class="wizard-top-header">
            <div class="wizard-title-wrap">
              <div>
                <h2>Admission Application Portal</h2>
                <p>Complete all 4 steps to submit your formal application for review.</p>
              </div>
              <div class="wizard-security-badge">
                <i class="bi bi-shield-lock-fill"></i> 256-Bit SSL Encrypted
              </div>
            </div>
          </div>

          <!-- Sticky Stepper & Progress Bar -->
          <div class="wizard-progress-section">
            <div class="wizard-progress-bar-track">
              <div class="wizard-progress-bar-fill" :style="{ width: progressPercentage + '%' }"></div>
            </div>

            <div class="wizard-stepper" role="tablist">
              <button
                type="button"
                class="stepper-tab"
                :class="{ 'is-active': currentStep === 1, 'is-completed': currentStep > 1 }"
                @click="currentStep > 1 ? currentStep = 1 : null"
              >
                <span class="stepper-badge"><i v-if="currentStep > 1" class="bi bi-check-lg"></i><template v-else>1</template></span>
                <div class="stepper-info">
                  <span class="stepper-step-label">Step 1</span>
                  <span class="stepper-step-title">Program Selection</span>
                </div>
              </button>

              <button
                type="button"
                class="stepper-tab"
                :class="{ 'is-active': currentStep === 2, 'is-completed': currentStep > 2 }"
                @click="currentStep > 2 ? currentStep = 2 : null"
              >
                <span class="stepper-badge"><i v-if="currentStep > 2" class="bi bi-check-lg"></i><template v-else>2</template></span>
                <div class="stepper-info">
                  <span class="stepper-step-label">Step 2</span>
                  <span class="stepper-step-title">Personal Details</span>
                </div>
              </button>

              <button
                type="button"
                class="stepper-tab"
                :class="{ 'is-active': currentStep === 3, 'is-completed': currentStep > 3 }"
                @click="currentStep > 3 ? currentStep = 3 : null"
              >
                <span class="stepper-badge"><i v-if="currentStep > 3" class="bi bi-check-lg"></i><template v-else>3</template></span>
                <div class="stepper-info">
                  <span class="stepper-step-label">Step 3</span>
                  <span class="stepper-step-title">Education</span>
                </div>
              </button>

              <button
                type="button"
                class="stepper-tab"
                :class="{ 'is-active': currentStep === 4, 'is-completed': currentStep > 4 }"
              >
                <span class="stepper-badge"><i v-if="currentStep > 4" class="bi bi-check-lg"></i><template v-else>4</template></span>
                <div class="stepper-info">
                  <span class="stepper-step-label">Step 4</span>
                  <span class="stepper-step-title">Documents & Review</span>
                </div>
              </button>
            </div>
          </div>

          <form class="wizard-form" @submit.prevent="submit" novalidate>
            <div v-if="errors.form" class="p-3 mx-4 mt-4 alert alert-danger rounded-3">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ errors.form[0] }}
            </div>

            <div class="wizard-body">

              <!-- STEP 1: PROGRAM SELECTION -->
              <div v-show="currentStep === 1" class="wizard-step-pane is-active">
                <div class="step-section-heading">
                  <span class="eyebrow text-primary">SELECT YOUR PATHWAY</span>
                  <h3>Choose an Academic Program</h3>
                  <p>Select the accredited degree or diploma program you wish to enroll in.</p>
                </div>

                <div class="program-cards-grid">
                  <div
                    v-for="p in data.programs"
                    :key="p.id"
                    class="program-card-option"
                    :class="{ 'is-selected': String(form.program_id) === String(p.id) }"
                    @click="selectProgram(p)"
                  >
                    <div>
                      <div class="program-card-header">
                        <span class="program-card-badge"><i class="bi bi-clock-history"></i> {{ p.duration }}</span>
                        <span class="program-card-check"><i class="bi bi-check-lg"></i></span>
                      </div>
                      <h4 class="program-card-title">{{ p.name }}</h4>
                      <p class="program-card-desc">{{ p.description || 'Rigorous clinical and laboratory training meeting national statutory council standards.' }}</p>
                    </div>
                    <div class="program-card-meta">
                      <span><i class="bi bi-mortarboard-fill me-1 text-primary"></i> Accredited Program</span>
                      <span class="text-primary fw-bold">Select <i class="bi bi-arrow-right-short"></i></span>
                    </div>
                  </div>
                </div>

                <div v-if="errors.program_id" class="field-error-message text-danger mb-3" style="display: flex;">
                  <i class="bi bi-exclamation-circle-fill"></i>
                  <span>{{ errors.program_id[0] }}</span>
                </div>

                <FeeBreakdown v-if="selectedProgram" :fees="selectedProgram.fees" />
              </div>

              <!-- STEP 2: PERSONAL DETAILS -->
              <div v-show="currentStep === 2" class="wizard-step-pane is-active">
                <div class="step-section-heading">
                  <span class="eyebrow text-primary">APPLICANT IDENTIFICATION</span>
                  <h3>Personal Information</h3>
                  <p>Provide accurate legal details as stated on your government identity card (CNIC) or Form-B.</p>
                </div>

                <div class="row g-3">
                  <div class="col-md-6">
                    <div class="form-field-group" :class="{ 'is-invalid': errors.first_name }">
                      <label class="form-label-custom">First Name <span class="req">*</span></label>
                      <div class="input-icon-wrap" :class="{ 'is-valid': form.first_name, 'is-invalid': errors.first_name }">
                        <input v-model="form.first_name" type="text" class="form-control-custom" placeholder="e.g. Ayesha" required />
                        <i class="bi bi-check-circle-fill field-validation-icon icon-valid"></i>
                        <i class="bi bi-exclamation-circle-fill field-validation-icon icon-invalid"></i>
                      </div>
                      <div v-if="errors.first_name" class="field-error-message">{{ errors.first_name[0] }}</div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-field-group" :class="{ 'is-invalid': errors.last_name }">
                      <label class="form-label-custom">Last Name <span class="req">*</span></label>
                      <div class="input-icon-wrap" :class="{ 'is-valid': form.last_name, 'is-invalid': errors.last_name }">
                        <input v-model="form.last_name" type="text" class="form-control-custom" placeholder="e.g. Khan" required />
                        <i class="bi bi-check-circle-fill field-validation-icon icon-valid"></i>
                        <i class="bi bi-exclamation-circle-fill field-validation-icon icon-invalid"></i>
                      </div>
                      <div v-if="errors.last_name" class="field-error-message">{{ errors.last_name[0] }}</div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-field-group" :class="{ 'is-invalid': errors.date_of_birth }">
                      <label class="form-label-custom">Date of Birth <span class="req">*</span></label>
                      <div class="input-icon-wrap" :class="{ 'is-valid': form.date_of_birth, 'is-invalid': errors.date_of_birth }">
                        <input v-model="form.date_of_birth" type="date" class="form-control-custom" required />
                        <i class="bi bi-check-circle-fill field-validation-icon icon-valid"></i>
                        <i class="bi bi-exclamation-circle-fill field-validation-icon icon-invalid"></i>
                      </div>
                      <div v-if="errors.date_of_birth" class="field-error-message">{{ errors.date_of_birth[0] }}</div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-field-group" :class="{ 'is-invalid': errors.gender }">
                      <label class="form-label-custom">Gender <span class="req">*</span></label>
                      <div class="input-icon-wrap" :class="{ 'is-valid': form.gender, 'is-invalid': errors.gender }">
                        <select v-model="form.gender" class="form-select-custom" required>
                          <option value="" disabled>Select Gender</option>
                          <option value="female">Female</option>
                          <option value="male">Male</option>
                          <option value="other">Other</option>
                          <option value="prefer_not_to_say">Prefer not to say</option>
                        </select>
                        <i class="bi bi-check-circle-fill field-validation-icon icon-valid"></i>
                        <i class="bi bi-exclamation-circle-fill field-validation-icon icon-invalid"></i>
                      </div>
                      <div v-if="errors.gender" class="field-error-message">{{ errors.gender[0] }}</div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-field-group" :class="{ 'is-invalid': errors.email }">
                      <label class="form-label-custom">Email Address <span class="req">*</span></label>
                      <div class="input-icon-wrap" :class="{ 'is-valid': form.email && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email), 'is-invalid': errors.email }">
                        <input v-model="form.email" type="email" class="form-control-custom" placeholder="applicant@example.com" required />
                        <i class="bi bi-check-circle-fill field-validation-icon icon-valid"></i>
                        <i class="bi bi-exclamation-circle-fill field-validation-icon icon-invalid"></i>
                      </div>
                      <div v-if="errors.email" class="field-error-message">{{ errors.email[0] }}</div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-field-group" :class="{ 'is-invalid': errors.phone }">
                      <label class="form-label-custom">Phone Number <span class="req">*</span></label>
                      <div class="input-icon-wrap" :class="{ 'is-valid': form.phone, 'is-invalid': errors.phone }">
                        <input v-model="form.phone" type="tel" class="form-control-custom" placeholder="+92 300 1234567" required />
                        <i class="bi bi-check-circle-fill field-validation-icon icon-valid"></i>
                        <i class="bi bi-exclamation-circle-fill field-validation-icon icon-invalid"></i>
                      </div>
                      <div v-if="errors.phone" class="field-error-message">{{ errors.phone[0] }}</div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-field-group">
                      <label class="form-label-custom">National ID / Passport</label>
                      <div class="input-icon-wrap">
                        <input v-model="form.national_id" type="text" class="form-control-custom" placeholder="CNIC or Passport Number" />
                      </div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-field-group" :class="{ 'is-invalid': errors.city }">
                      <label class="form-label-custom">City / District <span class="req">*</span></label>
                      <div class="input-icon-wrap" :class="{ 'is-valid': form.city, 'is-invalid': errors.city }">
                        <input v-model="form.city" type="text" class="form-control-custom" placeholder="e.g. Timergara, Dir Lower" required />
                      </div>
                      <div v-if="errors.city" class="field-error-message">{{ errors.city[0] }}</div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-field-group" :class="{ 'is-invalid': errors.country }">
                      <label class="form-label-custom">Country <span class="req">*</span></label>
                      <div class="input-icon-wrap" :class="{ 'is-valid': form.country, 'is-invalid': errors.country }">
                        <input v-model="form.country" type="text" class="form-control-custom" required />
                      </div>
                      <div v-if="errors.country" class="field-error-message">{{ errors.country[0] }}</div>
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="form-field-group" :class="{ 'is-invalid': errors.address }">
                      <label class="form-label-custom">Residential Address <span class="req">*</span></label>
                      <div class="input-icon-wrap" :class="{ 'is-valid': form.address, 'is-invalid': errors.address }">
                        <textarea v-model="form.address" class="form-control-custom" rows="3" placeholder="Village, Tehsil, District" required></textarea>
                      </div>
                      <div v-if="errors.address" class="field-error-message">{{ errors.address[0] }}</div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- STEP 3: EDUCATIONAL BACKGROUND -->
              <div v-show="currentStep === 3" class="wizard-step-pane is-active">
                <div class="step-section-heading">
                  <span class="eyebrow text-primary">ACADEMIC RECORD</span>
                  <h3>Educational Background</h3>
                  <p>Provide details of your most recent verified educational degree or diploma.</p>
                </div>

                <div class="row g-3">
                  <div class="col-md-6">
                    <div class="form-field-group" :class="{ 'is-invalid': errors.highest_qualification }">
                      <label class="form-label-custom">Highest Qualification <span class="req">*</span></label>
                      <input v-model="form.highest_qualification" class="form-control-custom" placeholder="e.g. FSc Pre-Medical" required />
                      <div v-if="errors.highest_qualification" class="field-error-message">{{ errors.highest_qualification[0] }}</div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-field-group" :class="{ 'is-invalid': errors.institution }">
                      <label class="form-label-custom">Institution / Board <span class="req">*</span></label>
                      <input v-model="form.institution" class="form-control-custom" placeholder="e.g. BISE Malakand" required />
                      <div v-if="errors.institution" class="field-error-message">{{ errors.institution[0] }}</div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-field-group" :class="{ 'is-invalid': errors.graduation_year }">
                      <label class="form-label-custom">Graduation Year <span class="req">*</span></label>
                      <input v-model="form.graduation_year" type="number" class="form-control-custom" required />
                      <div v-if="errors.graduation_year" class="field-error-message">{{ errors.graduation_year[0] }}</div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-field-group" :class="{ 'is-invalid': errors.grade_percentage }">
                      <label class="form-label-custom">Marks Percentage or CGPA</label>
                      <input v-model="form.grade_percentage" type="number" step="0.01" min="0" max="100" class="form-control-custom" placeholder="e.g. 78.50" />
                      <div v-if="errors.grade_percentage" class="field-error-message">{{ errors.grade_percentage[0] }}</div>
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="form-field-group">
                      <label class="form-label-custom">Personal Statement (Optional)</label>
                      <textarea v-model="form.statement" class="form-control-custom" rows="4" maxlength="3000" placeholder="Briefly describe why you wish to pursue your studies at Dir College Of Nursing."></textarea>
                      <div class="statement-counter-bar">
                        <span><strong>{{ statementCharCount }}</strong> / 3,000 characters</span>
                        <div class="char-meter-wrap">
                          <div class="char-meter-fill" :style="{ width: statementProgress + '%' }" :class="{ 'is-warning': statementProgress >= 70, 'is-danger': statementProgress >= 90 }"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- STEP 4: SUPPORTING DOCUMENTS & REVIEW -->
              <div v-show="currentStep === 4" class="wizard-step-pane is-active">
                <div class="step-section-heading">
                  <span class="eyebrow text-primary">OFFICIAL VERIFICATION</span>
                  <h3>Supporting Documents & Declaration</h3>
                  <p>Upload clear scanned copies of your academic transcript and identity document (PDF, JPG, or PNG up to 5 MB each).</p>
                </div>

                <div class="row g-4 mb-4">
                  <!-- Transcript Zone -->
                  <div class="col-lg-6">
                    <label class="form-label-custom">Academic Transcript / DMC <span class="req">*</span></label>
                    
                    <div
                      v-if="!transcriptMeta"
                      class="dropzone-container"
                      :class="{ 'is-dragover': isTranscriptDragging }"
                      @dragover.prevent="isTranscriptDragging = true"
                      @dragleave.prevent="isTranscriptDragging = false"
                      @drop="handleFileDrop('transcript', $event)"
                      @click="$refs.transcriptFile.click()"
                    >
                      <input ref="transcriptFile" type="file" accept=".pdf,.jpg,.jpeg,.png" class="d-none" @change="handleFileInput('transcript', $event)" />
                      <div class="dropzone-icon-circle"><i class="bi bi-cloud-arrow-up-fill"></i></div>
                      <div class="dropzone-prompt-text">Drag and drop transcript, or <span>browse</span></div>
                      <p class="dropzone-subtext">PDF, JPG, JPEG or PNG (Max size: 5 MB)</p>
                    </div>

                    <div v-else class="dropzone-preview-card is-active mt-2">
                      <div class="dropzone-preview-left">
                        <div class="preview-file-icon"><i class="bi bi-file-earmark-check-fill"></i></div>
                        <div class="preview-file-details">
                          <div class="preview-file-name">{{ transcriptMeta.name }}</div>
                          <div class="preview-file-meta"><i class="bi bi-check-circle-fill"></i> {{ transcriptMeta.size }} • Verified</div>
                        </div>
                      </div>
                      <button type="button" class="btn-remove-file" @click="removeFile('transcript')"><i class="bi bi-x-lg"></i></button>
                    </div>
                    <div v-if="errors.transcript" class="field-error-message text-danger mt-1" style="display: flex;">{{ errors.transcript[0] }}</div>
                  </div>

                  <!-- Identity Zone -->
                  <div class="col-lg-6">
                    <label class="form-label-custom">National ID / Form-B / Passport <span class="req">*</span></label>
                    
                    <div
                      v-if="!identityMeta"
                      class="dropzone-container"
                      :class="{ 'is-dragover': isIdentityDragging }"
                      @dragover.prevent="isIdentityDragging = true"
                      @dragleave.prevent="isIdentityDragging = false"
                      @drop="handleFileDrop('identity_document', $event)"
                      @click="$refs.identityFile.click()"
                    >
                      <input ref="identityFile" type="file" accept=".pdf,.jpg,.jpeg,.png" class="d-none" @change="handleFileInput('identity_document', $event)" />
                      <div class="dropzone-icon-circle"><i class="bi bi-person-badge-fill"></i></div>
                      <div class="dropzone-prompt-text">Drag and drop ID/Passport, or <span>browse</span></div>
                      <p class="dropzone-subtext">PDF, JPG, JPEG or PNG (Max size: 5 MB)</p>
                    </div>

                    <div v-else class="dropzone-preview-card is-active mt-2">
                      <div class="dropzone-preview-left">
                        <div class="preview-file-icon"><i class="bi bi-file-earmark-check-fill"></i></div>
                        <div class="preview-file-details">
                          <div class="preview-file-name">{{ identityMeta.name }}</div>
                          <div class="preview-file-meta"><i class="bi bi-check-circle-fill"></i> {{ identityMeta.size }} • Verified</div>
                        </div>
                      </div>
                      <button type="button" class="btn-remove-file" @click="removeFile('identity_document')"><i class="bi bi-x-lg"></i></button>
                    </div>
                    <div v-if="errors.identity_document" class="field-error-message text-danger mt-1" style="display: flex;">{{ errors.identity_document[0] }}</div>
                  </div>
                </div>

                <!-- Review Summary -->
                <div class="card border-0 bg-light rounded-4 p-3 p-md-4 mb-4">
                  <h6 class="fw-bold text-dark mb-3"><i class="bi bi-card-checklist text-primary me-2"></i>Quick Application Summary</h6>
                  <div class="row g-2 small text-muted">
                    <div class="col-sm-6"><strong>Target Program:</strong> <span class="text-dark">{{ selectedProgram?.name || 'Not Selected' }}</span></div>
                    <div class="col-sm-6"><strong>Applicant Name:</strong> <span class="text-dark">{{ form.first_name }} {{ form.last_name }}</span></div>
                    <div class="col-sm-6"><strong>Contact Email:</strong> <span class="text-dark">{{ form.email }}</span></div>
                    <div class="col-sm-6"><strong>City / District:</strong> <span class="text-dark">{{ form.city }}</span></div>
                  </div>
                </div>

                <!-- Legal Declaration -->
                <div class="application-confirm-box">
                  <div class="confirm-checkbox-wrap">
                    <input id="confirmationVue" v-model="form.confirmation" type="checkbox" class="form-check-input" />
                    <label for="confirmationVue">
                      I solemnly affirm and declare that all information, certificates, and credentials submitted in this application are authentic, accurate, and complete. I authorize <strong>{{ data.site.college_name || 'Dir College of Nursing' }}</strong> to conduct verification. <span class="text-danger">*</span>
                    </label>
                  </div>
                  <div v-if="errors.confirmation" class="field-error-message text-danger mt-2" style="display: flex;">{{ errors.confirmation[0] }}</div>
                </div>
              </div>

            </div>

            <!-- Wizard Footer Action Bar -->
            <div class="wizard-footer">
              <div>
                <button v-if="currentStep > 1" type="button" class="btn wizard-btn-prev" @click="prevStep">
                  <i class="bi bi-arrow-left"></i> Previous Step
                </button>
              </div>

              <div class="d-flex align-items-center gap-3">
                <span class="small text-muted d-none d-sm-inline">Step {{ currentStep }} of {{ totalSteps }} — {{ progressPercentage }}%</span>

                <button v-if="currentStep < totalSteps" type="button" class="btn wizard-btn-next" @click="nextStep">
                  Next Step <i class="bi bi-arrow-right"></i>
                </button>

                <button v-else type="submit" class="btn wizard-btn-submit" :disabled="busy">
                  <span v-if="busy" class="spinner-border spinner-border-sm me-2"></span>
                  <span v-if="!busy"><i class="bi bi-send-check-fill me-2"></i>Submit Application</span>
                  <span v-else>Submitting Application…</span>
                </button>
              </div>
            </div>
          </form>
        </div>

      </div>
    </section>
  </main>
  <SiteFooter :site="data.site" />
</template>
