@extends('layouts.public')
@php($active = 'news')
@section('content')
<main class="event-page pt-4 pt-md-5">
  <div class="container mb-3">
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb custom-breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door me-1"></i>Home</a></li>
        <li class="breadcrumb-item"><a href="{{ url('/news-events') }}">News & Events</a></li>
        <li class="breadcrumb-item active text-truncate" style="max-width: 320px;" aria-current="page">{{ $event->title }}</li>
      </ol>
    </nav>
    <div class="mb-4">
      <span class="eyebrow mb-2">CAMPUS UPDATE</span>
      <h1 class="page-title mb-2">{{ $event->title }}</h1>
      <div class="event-meta text-muted small"><i class="bi bi-calendar3 me-1"></i> {{ optional($event->event_date)->format('F j, Y') ?? 'Latest update' }} <span class="mx-1">•</span> {{ $site['college_short_name'] ?? $collegeName }}</div>
    </div>
  </div>
  <article class="event-article event-detail-layout">
    <div class="container">
      <img class="event-cover" src="{{ $event->image_path ? asset('storage/'.$event->image_path) : asset('images/nursing-hero.png') }}" alt="{{ $event->title }}">
      <div class="event-detail-grid">
        <div class="event-content">
          <p class="event-lead">{{ $event->excerpt }}</p>
          <div class="event-body">{!! nl2br(e($event->body ?: $event->excerpt)) !!}</div>
          <div class="event-share"><span>Have questions about this update?</span><a href="{{ url('/contact') }}" class="btn btn-primary">Talk to Admissions</a></div>
        </div>
        <aside class="related-news">
          <div class="related-news-header"><h2>More News</h2><a href="{{ url('/news-events') }}">View all</a></div>
          <form class="news-search related-search" method="get" action="{{ url('/news-events') }}"><i class="bi bi-search"></i><input name="search" type="search" class="form-control" placeholder="Search news..." aria-label="Search other news"></form>
          <div class="related-list">
            @forelse($events as $item)
              <a class="related-item" href="{{ url('/news-events/'.$item->slug) }}"><img src="{{ $item->image_path ? asset('storage/'.$item->image_path) : asset('images/nursing-hero.png') }}" alt="{{ $item->title }}"><span>{{ optional($item->event_date)->format('F j, Y') ?? 'Latest update' }}</span><b>{{ $item->title }}</b></a>
            @empty
              <div class="related-state"><i class="bi bi-search"></i><span>No other news available.</span></div>
            @endforelse
          </div>
        </aside>
      </div>
    </div>
  </article>
</main>
@endsection
