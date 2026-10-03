@extends('layouts.public')
@php($active = 'faculty')
@section('content')
<main class="faculty-page">
  <section class="faculty-hero">
    <div class="container">
      <span class="eyebrow text-info">OUR FACULTY</span>
      <h1>Meet the Academic Team</h1>
      <p>Dedicated faculty members guiding students with subject expertise, discipline, and personal academic support.</p>
    </div>
  </section>

  <section class="section faculty-directory">
    <div class="container">
      <div class="faculty-intro">
        <div>
          <span class="eyebrow">FACULTY DIRECTORY</span>
          <h2>Teachers Who Shape Confident Students</h2>
        </div>
        <p>Faculty profiles are managed from the admin dashboard, so this page stays updated as the academic team grows.</p>
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
