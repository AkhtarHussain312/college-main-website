@extends('layouts.public')
@php($active = 'news')
@section('content')
<main class="news-index">
  <section class="section section-compact pt-4 pt-md-5">
    <div class="container">
      <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb custom-breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door me-1"></i>Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">News & Events</li>
        </ol>
      </nav>
      <div class="news-toolbar mt-0 mb-4">
        <div>
          <span class="eyebrow">CAMPUS BULLETINS</span>
          <h1 class="page-title mb-1">Latest & Previous News</h1>
          <p class="text-muted mb-0">{{ $events->total() }} {{ $events->total() === 1 ? 'bulletin found' : 'bulletins found' }}</p>
        </div>
        <form class="news-search" method="get" action="{{ url('/news-events') }}"><i class="bi bi-search"></i><input name="search" value="{{ $search }}" type="search" class="form-control" placeholder="Search news..." aria-label="Search news and events"></form>
      </div>
      @if($events->count())
        <div class="row g-4">
          @foreach($events as $event)
            <div class="col-md-6 col-xl-4">
              <a class="news-card-link" href="{{ url('/news-events/'.$event->slug) }}">
                <article class="news-card news-list-card"><img src="{{ $event->image_path ? asset('storage/'.$event->image_path) : asset('images/nursing-hero.png') }}" alt="{{ $event->title }}"><div class="p-4"><small>{{ optional($event->event_date)->format('F j, Y') ?? 'Latest update' }}</small><h3>{{ $event->title }}</h3><p>{{ $event->excerpt }}</p><span class="news-read-more">Read Detail <i class="bi bi-arrow-right"></i></span></div></article>
              </a>
            </div>
          @endforeach
        </div>
        <div class="mt-4">{{ $events->links() }}</div>
      @else
        <div class="news-state"><i class="bi bi-search"></i><h2>No news found</h2><p>{{ $search ? 'Try another search term.' : 'No published news is available yet.' }}</p></div>
      @endif
    </div>
  </section>
</main>
@endsection
