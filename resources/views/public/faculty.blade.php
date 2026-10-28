@extends('layouts.public')
@php($active = 'faculty')
@section('content')
<main class="faculty-page">
  <section class="section faculty-directory pt-4 pt-md-5">
    <div class="container">
      <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb custom-breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door me-1"></i>Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Faculty</li>
        </ol>
      </nav>
      <div class="faculty-intro mb-4">
        <div>
          <span class="eyebrow">FACULTY DIRECTORY</span>
          <h1 class="page-title mb-1">Meet the Academic Team</h1>
        </div>
        <p>Dedicated faculty members guiding students with subject expertise, discipline, and clinical supervision.</p>
      </div>

      @if($faculty->count())
        <div class="row g-4">
          @foreach($faculty as $member)
            <div class="col-sm-6 col-lg-4 col-xl-3">
              <article class="faculty-profile-card">
                <div class="faculty-profile-photo">
                  <img src="{{ $member->image_path ? asset('storage/'.$member->image_path) : asset('images/nursing-hero.png') }}" alt="{{ $member->name }}">
                </div>
                <div class="faculty-profile-body">
                  <h2>{{ $member->name }}</h2>
                  <b>{{ $member->title }}</b>
                  @if($member->qualification)
                    <span>{{ $member->qualification }}</span>
                  @endif
                </div>
              </article>
            </div>
          @endforeach
        </div>
      @else
        <div class="faculty-empty">
          <i class="bi bi-people"></i>
          <h2>No faculty members are published yet.</h2>
          <p>Add faculty members from the admin dashboard to display them here.</p>
        </div>
      @endif
    </div>
  </section>

  <section class="faculty-cta">
    <div class="container text-center">
      <span class="eyebrow text-info">ACADEMIC GUIDANCE</span>
      <h2>Need Help Choosing the Right Program?</h2>
      <p>Our team can guide you through programs, eligibility, and the admission process.</p>
      <a href="{{ url('/contact') }}" class="btn btn-primary btn-lg">Contact Admissions</a>
    </div>
  </section>
</main>
@endsection
