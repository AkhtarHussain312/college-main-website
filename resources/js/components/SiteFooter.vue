<script setup>
defineProps({
  site: {
    type: Object,
    default: () => ({}),
  },
});
</script>

<template>
  <footer class="site-footer">
    <div class="container">
      <div class="row g-4 g-lg-5 footer-top py-5">
        <div class="col-lg-4 col-md-6">
          <div class="footer-brand d-flex align-items-center gap-2">
            <i class="bi bi-mortarboard-fill text-info"></i>
            <span>{{ site.college_name || 'Dir College Of Nursing & Allied Health Science' }}</span>
          </div>
          <p class="footer-desc">
            {{ site.footer_description || 'Professional nursing and allied health sciences education that prepares students for compassionate healthcare careers.' }}
          </p>
          <div class="footer-badge">
            <i class="bi bi-patch-check-fill text-info"></i>
            <span>Recognized & Accredited Institution</span>
          </div>
        </div>

        <div class="col-6 col-md-3 col-lg-2">
          <h6 class="footer-heading">College</h6>
          <ul class="footer-nav list-unstyled">
            <li><router-link to="/about">About Us</router-link></li>
            <li><router-link to="/faculty">Faculty Directory</router-link></li>
            <li><router-link to="/#campus-life">Campus Life</router-link></li>
            <li><router-link to="/contact">Direct Inquiries</router-link></li>
          </ul>
        </div>

        <div class="col-6 col-md-3 col-lg-3">
          <h6 class="footer-heading">Academics & Admissions</h6>
          <ul class="footer-nav list-unstyled">
            <li><router-link to="/programs">Academic Pathways</router-link></li>
            <li><router-link to="/apply">Online Application</router-link></li>
            <li><router-link to="/news-events">News & Campus Events</router-link></li>
            <li><router-link to="/contact">Talk to Admissions</router-link></li>
          </ul>
        </div>

        <div class="col-lg-3 col-md-6">
          <h6 class="footer-heading">Contact & Campus</h6>
          <ul class="footer-contact list-unstyled">
            <li class="d-flex align-items-start gap-2">
              <i class="bi bi-geo-alt-fill text-info mt-1"></i>
              <span>{{ site.college_address || 'Dir, Khyber Pakhtunkhwa, Pakistan' }}</span>
            </li>
            <li class="d-flex align-items-center gap-2">
              <i class="bi bi-envelope-fill text-info"></i>
              <a :href="`mailto:${site.college_email || 'admissions@dcn.edu.pk'}`">{{ site.college_email || 'admissions@dcn.edu.pk' }}</a>
            </li>
            <li class="d-flex align-items-center gap-2">
              <i class="bi bi-telephone-fill text-info"></i>
              <a :href="`tel:${site.college_phone || '+92 300 1234567'}`">{{ site.college_phone || '+92 300 1234567' }}</a>
            </li>
          </ul>
        </div>
      </div>

      <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 py-4">
        <div class="footer-copy">
          &copy; {{ new Date().getFullYear() }} {{ site.college_short_name || 'DCN' }}. All rights reserved.
        </div>
        <div class="footer-socials d-flex align-items-center gap-2">
          <a class="social-link" href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
          <a class="social-link" href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
          <a class="social-link" href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
          <a class="social-link" href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
        </div>
      </div>
    </div>
  </footer>
</template>

