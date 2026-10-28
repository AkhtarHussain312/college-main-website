@extends('layouts.public')

@php
  $active = 'about';
  $historyTitle = 'Our History & Mission';
  $historyBody = 'Founded with a vision to transform healthcare education in the region, Dir College of Nursing & Allied Health Sciences was established to address the critical demand for skilled, ethical, and compassionate medical personnel. Affiliated with statutory healthcare councils and accredited teaching hospitals, our institution prepares graduates for clinical excellence through rigorous academics, modern simulation laboratories, and extensive bedside patient care training.';
@endphp

@section('content')
<main class="page-content-wrapper">
  <!-- Institutional Narrative & Story -->
  <section class="section about-dynamic pt-4 pt-md-5">
    <div class="container">
      <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb custom-breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door me-1"></i>Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">About Us</li>
        </ol>
      </nav>
      <div class="about-page-header mb-5">
        <span class="eyebrow">ABOUT OUR COLLEGE</span>
        <h1 class="page-title mb-2">{{ $collegeName }}</h1>
        <p class="text-muted mb-0" style="max-width: 760px; font-size: 16px; line-height: 1.6;">{{ $site['about_text'] ?? 'Dedicated to academic rigor, practical clinical mastery, and compassionate healthcare mentorship — shaping the next generation of nursing leaders.' }}</p>
      </div>

      <div class="about-section-stack">
        @forelse($sections as $index => $section)
          @php
            $secTitle = $section->title;
            $secBody = $section->body;
            if (empty($secTitle) || stripos($secTitle, 'when we established') !== false) {
                $secTitle = $historyTitle;
            }
            if (empty($secBody) || stripos($secBody, 'some text about the college') !== false) {
                $secBody = $historyBody;
            }
          @endphp
          <article class="about-story-block {{ $section->image_position === 'right' ? 'is-reversed' : '' }}">
            <div class="about-story-media">
              @php
                $imgUrl = $section->image_path ? asset('storage/'.$section->image_path) : ($site['about_image'] ?? asset('images/nursing-hero.png'));
              @endphp
              <a href="{{ $imgUrl }}" target="_blank" rel="noopener noreferrer" class="about-story-media-link" title="Click to view full image">
                <img class="about-page-image" src="{{ $imgUrl }}" alt="{{ $secTitle }}" loading="lazy">
                <span class="about-media-badge">
                  <i class="bi bi-arrows-fullscreen"></i> View Full
                </span>
              </a>
            </div>
            <div class="about-story-copy">
              <span class="eyebrow">{{ $index === 0 ? 'OUR STORY & MISSION' : 'ABOUT OUR INSTITUTION' }}</span>
              <h2>{{ $secTitle }}</h2>
              <p>{{ $secBody }}</p>
              @if($index === 0)
                <div class="d-flex flex-wrap gap-3 mt-4">
                  <a href="{{ url('/apply') }}" class="btn btn-primary">Apply for Admission <i class="bi bi-arrow-right ms-1"></i></a>
                  <a href="{{ url('/programs') }}" class="btn btn-outline-secondary">Explore Programs</a>
                </div>
              @endif
            </div>
          </article>
        @empty
          <article class="about-story-block">
            <div class="about-story-media">
              @php
                $defaultImgUrl = $site['about_image'] ?? asset('images/nursing-hero.png');
              @endphp
              <a href="{{ $defaultImgUrl }}" target="_blank" rel="noopener noreferrer" class="about-story-media-link" title="Click to view full image">
                <img class="about-page-image" src="{{ $defaultImgUrl }}" alt="{{ $collegeName }}" loading="lazy">
                <span class="about-media-badge">
                  <i class="bi bi-arrows-fullscreen"></i> View Full
                </span>
              </a>
            </div>
            <div class="about-story-copy">
              <span class="eyebrow">OUR STORY & MISSION</span>
              <h2>{{ $historyTitle }}</h2>
              <p>{{ $historyBody }}</p>
              <div class="d-flex flex-wrap gap-3 mt-4">
                <a href="{{ url('/apply') }}" class="btn btn-primary">Apply for Admission <i class="bi bi-arrow-right ms-1"></i></a>
                <a href="{{ url('/programs') }}" class="btn btn-outline-secondary">Explore Programs</a>
              </div>
            </div>
          </article>
        @endforelse
      </div>
    </div>
  </section>

  <!-- What Makes Us Different -->
  <section class="section section-soft">
    <div class="container">
      <div class="section-title text-center mb-5">
        <span class="eyebrow">OUR DISTINCTIVE ADVANTAGES</span>
        <h2 class="display-6 fw-bold">What Makes Us Different</h2>
        <p class="text-muted mx-auto" style="max-width: 640px;">A disciplined and supportive learning environment built for students who demand academic rigor and authentic healthcare practice.</p>
      </div>
      <div class="row g-4">
        <div class="col-md-6 col-lg-3">
          <article class="about-value-card">
            <i class="bi bi-hospital-fill"></i>
            <h3>Clinical Hospital Rotations</h3>
            <p>Students gain immersive, real-world clinical experience and patient bedside care through affiliated tertiary teaching hospitals.</p>
          </article>
        </div>
        <div class="col-md-6 col-lg-3">
          <article class="about-value-card">
            <i class="bi bi-mortarboard-fill"></i>
            <h3>Accredited Academic Rigor</h3>
            <p>Structured nursing and allied health curricula aligned with national statutory council standards and medical board guidelines.</p>
          </article>
        </div>
        <div class="col-md-6 col-lg-3">
          <article class="about-value-card">
            <i class="bi bi-diagram-3-fill"></i>
            <h3>Advanced Simulation Labs</h3>
            <p>High-fidelity anatomical models, patient simulation mannequins, and medical equipment for safe hands-on procedural mastery.</p>
          </article>
        </div>
        <div class="col-md-6 col-lg-3">
          <article class="about-value-card">
            <i class="bi bi-award-fill"></i>
            <h3>Licensure & Career Pathways</h3>
            <p>Dedicated licensing examination preparation, compassionate mentorship, and strong healthcare facility placement support.</p>
          </article>
        </div>
      </div>
    </div>
  </section>

  <!-- Visit Our Campus / CTA -->
  <section class="about-cta">
    <div class="container text-center">
      <span class="eyebrow text-info">VISIT OUR CAMPUS</span>
      <h2>See the environment where your healthcare journey begins.</h2>
      <p class="mb-4">Schedule a personal campus tour, consult our admissions counselors, or start your online application today.</p>
      <div class="d-flex flex-wrap justify-content-center gap-3">
        <a href="{{ url('/contact') }}" class="btn btn-primary btn-lg">Contact Admissions</a>
        <a href="{{ url('/apply') }}" class="btn btn-outline-light btn-lg">Apply Online</a>
      </div>
    </div>
  </section>
</main>
@endsection
