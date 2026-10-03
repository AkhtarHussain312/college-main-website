@extends('layouts.public')
@php($active = 'programs')
@section('content')
<main>
  <section class="programs-hero"><div class="container"><span class="eyebrow text-info">ACADEMIC PROGRAMS</span><h1>Find Your Path</h1><p>Explore programs offered by {{ $collegeName }} and choose the path that matches your future goals.</p><a href="{{ url('/contact') }}" class="btn btn-primary">Talk to Admissions</a></div></section>
  <section class="section section-soft">
    <div class="container">
      <div class="programs-page-heading"><div><span class="eyebrow">OUR PROGRAMS</span><h2>All Programs</h2><p>Career-focused education supported by strong academic guidance.</p></div><a href="{{ url('/') }}" class="back-home"><i class="bi bi-arrow-left"></i> Back to home</a></div>
      <div class="row g-4">
        @forelse($programs as $program)
          <div class="col-md-6 col-xl-4">
            <article class="program-list-card">
              <img src="{{ $program['image_url'] ?? asset('images/nursing-hero.png') }}" alt="{{ $program['name'] }}">
              <div class="program-list-body">
                <span class="program-duration"><i class="bi bi-clock"></i> {{ $program['duration'] }}</span>
                <h2>{{ $program['name'] }}</h2>
                <p>{{ $program['description'] }}</p>
                @if(! empty($program['fees']))
                  <div class="fee-breakdown">
                    @foreach($program['fees'] as $fee)
                      <div class="fee-row"><span>{{ $fee['fee_type'] ?? 'Fee' }}</span><b>{{ $fee['currency'] ?? 'PKR' }} {{ number_format((float) ($fee['amount'] ?? 0)) }}</b></div>
                    @endforeach
                  </div>
                @endif
                <div class="program-actions"><a href="{{ url('/apply') }}" class="btn btn-primary">Apply Now</a><a href="mailto:{{ $site['college_email'] ?? 'admissions@example.edu' }}" class="details-link">Request details <i class="bi bi-arrow-right"></i></a></div>
              </div>
            </article>
          </div>
        @empty
          <div class="empty-programs"><i class="bi bi-journal-medical"></i><h3>No programs are currently published.</h3><p>Please contact admissions for current course information.</p></div>
        @endforelse
      </div>
    </div>
  </section>
</main>
@endsection
