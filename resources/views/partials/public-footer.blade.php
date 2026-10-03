<footer class="site-footer">
  <div class="container">
    <div class="row g-4 g-lg-5 footer-top py-5">
      <div class="col-lg-4 col-md-6">
        <div class="footer-brand d-flex align-items-center gap-2">
          <i class="bi bi-mortarboard-fill text-info"></i>
          <span>{{ $collegeName }}</span>
        </div>
        <p class="footer-desc">{{ $site['footer_description'] ?? 'Quality education that prepares ambitious students for confident academic and professional futures.' }}</p>
        <div class="footer-badge">
          <i class="bi bi-patch-check-fill text-info"></i>
          <span>Recognized & Accredited Institution</span>
        </div>
      </div>

      <div class="col-6 col-md-3 col-lg-2">
        <h6 class="footer-heading">College</h6>
        <ul class="footer-nav list-unstyled">
          <li><a href="{{ url('/about') }}">About Us</a></li>
          <li><a href="{{ url('/#about-us') }}">Leadership</a></li>
          <li><a href="{{ url('/faculty') }}">Faculty Directory</a></li>
          <li><a href="{{ url('/#clinical-training') }}">Campus Life</a></li>
          <li><a href="{{ url('/contact') }}">Direct Inquiries</a></li>
        </ul>
      </div>

      <div class="col-6 col-md-3 col-lg-3">
        <h6 class="footer-heading">Academics & Admissions</h6>
        <ul class="footer-nav list-unstyled">
          <li><a href="{{ url('/programs') }}">Academic Pathways</a></li>
          <li><a href="{{ url('/apply') }}">Online Application</a></li>
          <li><a href="{{ url('/#admissions') }}">Admission Steps</a></li>
          <li><a href="{{ url('/news-events') }}">News & Campus Events</a></li>
          <li><a href="{{ url('/contact') }}">Talk to Admissions</a></li>
        </ul>
      </div>

      <div class="col-lg-3 col-md-6">
        <h6 class="footer-heading">Contact & Campus</h6>
        <ul class="footer-contact list-unstyled">
          <li class="d-flex align-items-start gap-2">
            <i class="bi bi-geo-alt-fill text-info mt-1"></i>
            <span>{{ $site['college_address'] ?? 'College Campus, Pakistan' }}</span>
          </li>
          <li class="d-flex align-items-center gap-2">
            <i class="bi bi-envelope-fill text-info"></i>
            <a href="mailto:{{ $site['college_email'] ?? 'admissions@example.edu' }}">{{ $site['college_email'] ?? 'admissions@example.edu' }}</a>
          </li>
          <li class="d-flex align-items-center gap-2">
            <i class="bi bi-telephone-fill text-info"></i>
            <a href="tel:{{ $site['college_phone'] ?? '+92000000000' }}">{{ $site['college_phone'] ?? '+92 000 0000000' }}</a>
          </li>
          <li class="d-flex align-items-center gap-2">
            <i class="bi bi-clock-fill text-info"></i>
            <span>{{ $site['contact_office_hours'] ?? 'Mon–Fri: 8:30 AM – 5:00 PM' }}</span>
          </li>
        </ul>
      </div>
    </div>

    <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 py-4">
      <div class="footer-copy">
        &copy; {{ date('Y') }} {{ $site['college_short_name'] ?? $collegeName }}. All rights reserved.
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

