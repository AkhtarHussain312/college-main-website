@extends('layouts.public')
@php($active = 'about')
@section('content')
<main>
  <section class="about-hero"><div class="container"><span class="eyebrow text-info">ABOUT OUR COLLEGE</span><h1>{{ $collegeName }}</h1><p>{{ $site['about_text'] ?? 'Learn about our mission, values, and academic environment.' }}</p></div></section>
  <section class="section about-dynamic">
    <div class="container">
      <div class="about-section-stack">
        @forelse($sections as $index => $section)
          <article class="about-story-block {{ $section->image_position === 'right' ? 'is-reversed' : '' }}">
            <div class="about-story-media"><img class="about-page-image" src="{{ $section->image_path ? asset('storage/'.$section->image_path) : ($site['about_image'] ?? asset('images/nursing-hero.png')) }}" alt="{{ $section->title }}"></div>
            <div class="about-story-copy"><span class="eyebrow">{{ $index === 0 ? 'OUR STORY' : 'ABOUT US' }}</span><h2>{{ $section->title }}</h2><p>{{ $section->body }}</p></div>
          </article>
        @empty
          <article class="about-story-block">
            <div class="about-story-media"><img class="about-page-image" src="{{ $site['about_image'] ?? asset('images/nursing-hero.png') }}" alt="{{ $collegeName }}"></div>
            <div class="about-story-copy"><span class="eyebrow">OUR STORY</span><h2>{{ $site['about_title'] ?? 'Preparing Future Leaders' }}</h2><p>{{ $site['about_text'] ?? 'Pakistan Leadership College provides students with focused academic preparation and a supportive campus environment.' }}</p></div>
          </article>
        @endforelse
      </div>
    </div>
  </section>
</main>
@endsection
