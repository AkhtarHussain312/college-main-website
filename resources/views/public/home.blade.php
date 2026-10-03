@extends('layouts.public')
@php
  $active = 'home';
  $fallbackPrograms = [
    ['name' => 'Pre-medical', 'duration' => '2 Years', 'description' => 'Focused science preparation for students aiming toward medical and healthcare careers.'],
    ['name' => 'Pre-engineering', 'duration' => '2 Years', 'description' => 'Strong mathematics and physics foundations for future engineering pathways.'],
    ['name' => 'Computer Science', 'duration' => '2 Years', 'description' => 'Modern computing fundamentals for students interested in software, data, and technology.'],
  ];
  $homePrograms = count($programs) ? array_slice($programs, 0, 3) : $fallbackPrograms;
@endphp
@section('content')
<main id="home" class="modern-home">
  <section class="home-hero" style="background-image:linear-gradient(90deg,rgba(5,22,37,.96) 0%,rgba(5,22,37,.78) 44%,rgba(5,22,37,.22) 100%),url('{{ $site['hero_image'] ?? asset('images/nursing-hero.png') }}')">
    <div class="container home-hero-inner">
      <div class="home-hero-copy" data-reveal>
        <span class="home-badge"><i class="bi bi-stars"></i> {{ $site['hero_kicker'] ?? 'Admissions Open for Fall 2026' }}</span>
        <h1>{{ $site['hero_title'] ?? 'Dir College Of Nursing & Allied Health Science' }}</h1>
        <p>{{ $site['hero_text'] ?? 'A modern college experience for ambitious students pursuing pre-medical, pre-engineering, and computer science pathways.' }}</p>
        <div class="home-actions">
          <a class="btn btn-primary btn-lg" href="{{ url('/apply') }}">{{ $site['hero_primary_cta'] ?? 'Apply Now' }} <i class="bi bi-arrow-right"></i></a>
          <a class="btn btn-outline-light btn-lg" href="{{ url('/programs') }}">{{ $site['hero_secondary_cta'] ?? 'Explore Programs' }}</a>
        </div>
        <div class="home-hero-metrics">
          <div class="hero-metric-item">
            <span class="hero-metric-val">{{ $site['metric_1_value'] ?? '3' }}</span>
            <span class="hero-metric-lbl">{{ $site['metric_1_label'] ?? 'Academic pathways' }}</span>
          </div>
          <div class="hero-metric-sep"></div>
          <div class="hero-metric-item">
            <span class="hero-metric-val">{{ $site['metric_2_value'] ?? '2026' }}</span>
            <span class="hero-metric-lbl">{{ $site['metric_2_label'] ?? 'Admissions open' }}</span>
          </div>
          <div class="hero-metric-sep"></div>
          <div class="hero-metric-item">
            <span class="hero-metric-val">{{ $site['metric_3_value'] ?? 'DCN' }}</span>
            <span class="hero-metric-lbl">{{ $site['metric_3_label'] ?? 'Nursing pathways' }}</span>
          </div>
        </div>
      </div>
      <div class="home-hero-panel" data-reveal>
        <span class="panel-badge">{{ $site['hero_panel_label'] ?? 'Next Intake' }}</span>
        <strong>{{ $site['hero_panel_title'] ?? 'Fall 2026' }}</strong>
        <p>{{ $site['hero_panel_text'] ?? 'Applications are open for Pre-medical, Pre-engineering, and Computer Science.' }}</p>
        <a class="panel-link" href="{{ url('/contact') }}">{{ $site['hero_panel_link'] ?? 'Talk to Admissions' }} <i class="bi bi-arrow-up-right"></i></a>
      </div>
    </div>
  </section>

  <section class="home-pathways">
    <div class="container">
      <div class="row g-3 g-lg-4">
        @foreach([
          ['bi-journal-check', 'Admissions', 'Start your application', url('/apply')],
          ['bi-mortarboard', 'Programs', 'Compare study paths', url('/programs')],
          ['bi-chat-dots', 'Counselling', 'Speak with our team', url('/contact')],
          ['bi-newspaper', 'News', 'Latest updates', url('/news-events')]
        ] as $card)
          <div class="col-6 col-lg-3" data-reveal>
            <a class="pathway-card" href="{{ $card[3] }}">
              <div class="pathway-card-top">
                <div class="pathway-icon"><i class="bi {{ $card[0] }}"></i></div>
                <div class="pathway-arrow"><i class="bi bi-arrow-up-right"></i></div>
              </div>
              <div class="pathway-card-body">
                <b>{{ $card[1] }}</b>
                <span>{{ $card[2] }}</span>
              </div>
            </a>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <section id="about-us" class="section home-about">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-6" data-reveal>
          <div class="home-image-frame"><img src="{{ $site['about_image'] ?? asset('images/nursing-hero.png') }}" alt="{{ $collegeName }} campus life" loading="lazy" width="600" height="430"></div>
        </div>
        <div class="col-lg-6" data-reveal>
          <span class="eyebrow">ABOUT OUR COLLEGE</span>
          <h2>{{ $site['about_title'] ?? 'Built for Students With Serious Ambition' }}</h2>
          <p>{{ $site['about_text'] ?? ($collegeName.' combines disciplined academics, supportive guidance, and a future-focused environment where students prepare for competitive fields with confidence.') }}</p>
          <div class="home-proof-grid">
            <div><i class="bi bi-person-check"></i><b>{{ $site['proof_1_title'] ?? 'Guided Learning' }}</b><span>{{ $site['proof_1_text'] ?? 'Clear support from admission to progression.' }}</span></div>
            <div><i class="bi bi-graph-up-arrow"></i><b>{{ $site['proof_2_title'] ?? 'Future Focus' }}</b><span>{{ $site['proof_2_text'] ?? 'Programs aligned with higher study goals.' }}</span></div>
          </div>
          <a href="{{ url('/about') }}" class="btn btn-dark btn-lg">Discover Our Story <i class="bi bi-arrow-right"></i></a>
        </div>
      </div>
    </div>
  </section>

  <section id="programs" class="section home-programs">
    <div class="container">
      <div class="home-section-head text-center" data-reveal>
        <span class="eyebrow">ACADEMIC PATHWAYS</span>
        <h2>{{ $site['programs_title'] ?? 'Choose the Program That Matches Your Future' }}</h2>
        <p>{{ $site['programs_text'] ?? 'Purpose-built programs for students preparing for medicine, engineering, and computing careers.' }}</p>
      </div>
      <div class="row g-4">
        @foreach($homePrograms as $program)
          <div class="col-md-6 col-xl-4" data-reveal>
            <article class="modern-program-card">
              <div class="program-card-media">
                <img src="{{ $program['image_url'] ?? asset('images/nursing-hero.png') }}" alt="{{ $program['name'] }}" loading="lazy" width="400" height="250">
                <span class="program-badge">{{ $program['duration'] ?? 'Academic Program' }}</span>
              </div>
              <div class="program-card-body">
                <h3>{{ $program['name'] }}</h3>
                <p>{{ $program['description'] }}</p>
                <a class="program-link" href="{{ url('/programs') }}">Explore Curriculum <i class="bi bi-arrow-right"></i></a>
              </div>
            </article>
          </div>
        @endforeach
      </div>
      <div class="text-center mt-5"><a href="{{ url('/programs') }}" class="btn btn-primary btn-lg">View All Programs <i class="bi bi-arrow-right"></i></a></div>
    </div>
  </section>

  <section id="clinical-training" class="home-experience">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-6" data-reveal>
          <span class="eyebrow text-info">STUDENT EXPERIENCE</span>
          <h2>{{ $site['clinical_title'] ?? 'Modern Learning With Real Direction' }}</h2>
          <p class="experience-lead">{{ $site['clinical_text'] ?? 'Students get a structured environment, focused teaching, academic counselling, and the confidence to move toward competitive higher education goals.' }}</p>
          <div class="experience-list">
            <span><i class="bi bi-check2-circle text-info"></i> {{ $site['experience_point_1'] ?? 'Academic discipline' }}</span>
            <span><i class="bi bi-check2-circle text-info"></i> {{ $site['experience_point_2'] ?? 'Personal guidance' }}</span>
            <span><i class="bi bi-check2-circle text-info"></i> {{ $site['experience_point_3'] ?? 'Leadership mindset' }}</span>
          </div>
        </div>
        <div class="col-lg-6" data-reveal>
          <div class="experience-media"><img src="{{ $site['clinical_image'] ?? asset('images/nursing-hero.png') }}" alt="{{ $collegeName }} students learning" loading="lazy" width="600" height="430"></div>
        </div>
      </div>
    </div>
  </section>

  <section id="admissions" class="section home-admissions">
    <div class="container">
      <div class="home-section-head text-center" data-reveal>
        <span class="eyebrow">ADMISSIONS</span>
        <h2>{{ $site['admissions_title'] ?? ('Simple Steps to Join '.($site['college_short_name'] ?? 'DCN')) }}</h2>
        <p>{{ $site['admissions_text'] ?? 'Our admissions team keeps the process clear, practical, and student friendly.' }}</p>
      </div>
      <div class="modern-steps">
        @forelse($milestones as $i => $step)
          <div class="step-card" data-reveal>
            <div class="step-num">0{{ $i + 1 }}</div>
            <i class="bi {{ $step->icon ?: 'bi-check2-circle' }}"></i>
            <span>{{ $step->title }}</span>
          </div>
        @empty
          @foreach(['Choose Your Program','Check Eligibility','Submit Application','Begin Your Journey'] as $i => $step)
            <div class="step-card" data-reveal>
              <div class="step-num">0{{ $i + 1 }}</div>
              <i class="bi bi-check2-circle"></i>
              <span>{{ $step }}</span>
            </div>
          @endforeach
        @endforelse
      </div>
      <div class="text-center mt-5"><a href="{{ url('/apply') }}" class="btn btn-primary btn-lg">Start Your Application <i class="bi bi-arrow-right"></i></a></div>
    </div>
  </section>

  <section id="news-and-events" class="section home-news">
    <div class="container">
      <div class="home-section-head text-center" data-reveal>
        <span class="eyebrow">NEWS & EVENTS</span>
        <h2>{{ $site['news_title'] ?? 'Latest From Campus' }}</h2>
        <p>{{ $site['news_text'] ?? 'Stay connected with announcements, admissions updates, and college highlights.' }}</p>
      </div>
      <div class="row g-4">
        @forelse($events as $event)
          <div class="col-md-4" data-reveal>
            <a class="news-card-link" href="{{ url('/news-events/'.$event->slug) }}">
              <article class="news-card modern-news-card">
                <div class="news-card-media">
                  <img src="{{ $event->image_path ? asset('storage/'.$event->image_path) : asset('images/nursing-hero.png') }}" alt="{{ $event->title }}" loading="lazy" width="400" height="240">
                  <small class="news-date-badge">{{ optional($event->event_date)->format('F j, Y') ?? 'Latest update' }}</small>
                </div>
                <div class="news-card-body p-4">
                  <h3>{{ $event->title }}</h3>
                  <p>{{ $event->excerpt }}</p>
                  <span class="news-read-more">Read Full Story <i class="bi bi-arrow-right"></i></span>
                </div>
              </article>
            </a>
          </div>
        @empty
          <div class="col-12 text-center text-muted py-5">News will appear here once published from the admin dashboard.</div>
        @endforelse
      </div>
      <div class="text-center mt-5"><a href="{{ url('/news-events') }}" class="link-arrow">View All Campus News <i class="bi bi-arrow-right"></i></a></div>
    </div>
  </section>

  <section class="home-final-cta">
    <div class="container text-center" data-reveal>
      <span class="eyebrow text-info">READY TO START?</span>
      <h2>{{ $site['cta_title'] ?? 'Your Next Academic Step Starts Here' }}</h2>
      <p class="final-cta-desc">{{ $site['cta_text'] ?? 'Apply for admission or speak with our team to choose the right program for your goals.' }}</p>
      <div class="home-actions justify-content-center">
        <a href="{{ url('/apply') }}" class="btn btn-primary btn-lg">Apply Online Now</a>
        <a href="{{ url('/contact') }}" class="btn btn-outline-light btn-lg">Contact Admissions</a>
      </div>
    </div>
  </section>
</main>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var items = document.querySelectorAll('.modern-home [data-reveal]');

    if (!('IntersectionObserver' in window)) {
      items.forEach(function (item) {
        item.classList.add('is-visible');
      });
      return;
    }

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, {
      threshold: 0.16,
      rootMargin: '0px 0px -40px 0px'
    });

    items.forEach(function (item) {
      observer.observe(item);
    });
  });
</script>
@endsection
