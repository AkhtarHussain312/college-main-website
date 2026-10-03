@extends('layouts.public')
@php($active = 'news')
@section('content')
<main class="event-page">
  <section class="event-hero"><div class="container"><a href="{{ url('/news-events') }}" class="event-back"><i class="bi bi-arrow-left"></i> All News & Events</a><span>NEWS & EVENTS</span><h1>{{ $event->title }}</h1><div class="event-meta"><i class="bi bi-calendar3"></i> {{ optional($event->event_date)->format('F j, Y') ?? 'Latest update' }} <b>-</b> {{ $site['college_short_name'] ?? $collegeName }}</div></div></section>
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
