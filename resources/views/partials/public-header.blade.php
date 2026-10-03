@php($active = $active ?? '')
<div class="topbar">
  <div class="container d-flex justify-content-between align-items-center">
    <div class="topbar-announcement text-truncate">
      <span class="topbar-badge"><i class="bi bi-megaphone-fill"></i> UPDATE</span>
      <span class="topbar-text">{{ $site['admissions_banner'] ?? 'Admissions Open for Fall 2026 - Apply Today' }}</span>
    </div>
    <div class="topbar-actions d-none d-md-flex align-items-center gap-3">
      @if(!empty($site['college_phone']))
        <a href="tel:{{ $site['college_phone'] }}" class="topbar-link"><i class="bi bi-telephone"></i> {{ $site['college_phone'] }}</a>
      @endif
      <a href="{{ url('/apply') }}" class="topbar-link topbar-cta">Apply Online <i class="bi bi-arrow-right"></i></a>
    </div>
  </div>
</div>

<header class="sticky-top site-header">
  <nav class="navbar site-navbar" aria-label="Main Navigation">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
        <span class="brand-mark logo-mark"><img class="brand-logo" src="{{ asset('logo.png') }}" alt="{{ $collegeName }} logo" width="40" height="40"></span>
        <span class="brand-text">
          <strong class="brand-title">{{ $site['nav_brand_title'] ?? $site['college_short_name'] ?? $collegeName }}</strong>
          <small class="brand-sub">{{ $site['nav_brand_subtitle'] ?? $site['college_name'] ?? $collegeName }}</small>
        </span>
      </a>

      <button class="navbar-toggler custom-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div id="nav" class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto align-items-lg-center">
          <li class="nav-item"><a class="nav-link {{ $active === 'home' ? 'active' : '' }}" href="{{ url('/') }}">Home</a></li>
          <li class="nav-item"><a class="nav-link {{ $active === 'about' ? 'active' : '' }}" href="{{ url('/about') }}">About Us</a></li>
          <li class="nav-item"><a class="nav-link {{ $active === 'programs' ? 'active' : '' }}" href="{{ url('/programs') }}">Programs</a></li>
          <li class="nav-item"><a class="nav-link {{ $active === 'faculty' ? 'active' : '' }}" href="{{ url('/faculty') }}">Faculty</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ url('/#admissions') }}">Admissions</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ url('/#clinical-training') }}">Campus Life</a></li>
          <li class="nav-item"><a class="nav-link {{ $active === 'news' ? 'active' : '' }}" href="{{ url('/news-events') }}">News & Events</a></li>
          <li class="nav-item"><a class="nav-link {{ $active === 'contact' ? 'active' : '' }}" href="{{ url('/contact') }}">Contact</a></li>
          <li class="nav-item nav-btn-item"><a class="btn btn-primary nav-cta-btn {{ $active === 'apply' ? 'active' : '' }}" href="{{ url('/apply') }}">Apply Now</a></li>
        </ul>
      </div>
    </div>
  </nav>
</header>

