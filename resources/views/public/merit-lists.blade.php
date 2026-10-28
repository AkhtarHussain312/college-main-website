@extends('layouts.public')
@php
  $active = 'merit-lists';
  $categories = [
      '' => 'All Documents',
      'merit_list' => 'Merit Lists',
      'prospectus' => 'Prospectus',
      'form' => 'Challan & Forms',
      'roll_number_slip' => 'Roll Number Slips',
  ];
@endphp

@section('content')
<main class="page-content-wrapper">
  <!-- Filter & Listing Section -->
  <div class="container py-4 py-md-5">
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb custom-breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door me-1"></i>Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Merit Lists & Downloads</li>
      </ol>
    </nav>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
      <div>
        <span class="eyebrow">ACADEMIC NOTICES & SELECTION</span>
        <h1 class="page-title mb-1">Official Merit Lists & Downloads</h1>
        <p class="text-muted mb-0">Access verified merit lists, intake selection notifications, fee challans, and the official prospectus.</p>
      </div>
      <a href="{{ url('/track') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-search me-1"></i> Track Your Form</a>
    </div>
      
      <!-- Filter Bar -->
      <div class="card shadow-sm border-0 rounded-4 mb-4 p-3 p-md-4" style="background: #ffffff;">
        <div class="row g-3 align-items-center justify-content-between">
          
          <!-- Category Filter Pills -->
          <div class="col-12 col-lg-auto">
            <div class="d-flex flex-wrap gap-2">
              @foreach($categories as $key => $label)
                <a
                  href="{{ url('/merit-lists'.($key ? '?category='.$key : '')) }}"
                  class="btn btn-sm rounded-pill px-3 py-2 {{ $activeCategory === $key ? 'btn-primary' : 'btn-outline-secondary' }}"
                >
                  {{ $label }}
                </a>
              @endforeach
            </div>
          </div>

          <!-- Search Input -->
          <div class="col-12 col-lg-4">
            <form action="{{ url('/merit-lists') }}" method="GET" class="d-flex gap-2">
              @if($activeCategory)
                <input type="hidden" name="category" value="{{ $activeCategory }}">
              @endif
              <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input
                  type="text"
                  name="search"
                  value="{{ $search }}"
                  class="form-control border-start-0 ps-0"
                  placeholder="Search list title..."
                >
              </div>
              <button type="submit" class="btn btn-outline-primary px-3">Filter</button>
            </form>
          </div>

        </div>
      </div>

      <!-- Document Cards Grid -->
      <div class="row g-4 mb-5">
        @forelse($documents as $doc)
          <div class="col-md-6 col-lg-6">
            <article class="card h-100 border-0 shadow-sm rounded-4 p-4 transition-hover" style="background: #ffffff; border: 1px solid #e9ecef !important;">
              <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                <div class="d-flex align-items-center gap-2">
                  <div class="rounded-3 p-2.5 bg-primary-subtle text-primary d-inline-flex">
                    <i class="bi {{ $doc->category === 'prospectus' ? 'bi-journal-richtext' : ($doc->category === 'form' ? 'bi-cash-coin' : 'bi-file-earmark-pdf-fill') }} fs-4"></i>
                  </div>
                  <div>
                    <span class="badge {{ $doc->category === 'merit_list' ? 'bg-primary' : ($doc->category === 'prospectus' ? 'bg-success' : 'bg-secondary') }} text-uppercase" style="font-size: 10px;">
                      {{ str_replace('_', ' ', $doc->category) }}
                    </span>
                    @if($doc->session_year)
                      <span class="badge bg-light text-dark border ms-1" style="font-size: 10px;">
                        Session {{ $doc->session_year }}
                      </span>
                    @endif
                  </div>
                </div>

                @if($doc->file_size)
                  <span class="text-muted small fw-semibold"><i class="bi bi-paperclip"></i> {{ $doc->file_size }}</span>
                @endif
              </div>

              <h2 class="h5 fw-bold text-dark mb-2" style="font-size: 1.15rem; line-height: 1.4;">
                {{ $doc->title }}
              </h2>

              @if($doc->program)
                <div class="mb-2">
                  <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">
                    <i class="bi bi-mortarboard-fill me-1"></i> {{ $doc->program->name }}
                  </span>
                </div>
              @endif

              @if($doc->description)
                <p class="text-muted small mb-4 flex-grow-1" style="line-height: 1.6;">
                  {{ $doc->description }}
                </p>
              @endif

              <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                <span class="text-muted" style="font-size: 12px;">
                  <i class="bi bi-calendar3 me-1"></i> Published: {{ $doc->published_at ? $doc->published_at->format('M d, Y') : $doc->created_at->format('M d, Y') }}
                </span>

                <div class="d-flex gap-2">
                  <a
                    href="{{ asset('storage/'.$doc->file_path) }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="btn btn-sm btn-outline-primary px-3 rounded-pill"
                  >
                    <i class="bi bi-eye me-1"></i> View
                  </a>
                  <a
                    href="{{ asset('storage/'.$doc->file_path) }}"
                    download
                    class="btn btn-sm btn-primary px-3 rounded-pill"
                  >
                    <i class="bi bi-download me-1"></i> Download
                  </a>
                </div>
              </div>
            </article>
          </div>
        @empty
          <div class="col-12">
            <div class="p-5 text-center bg-white rounded-4 border">
              <i class="bi bi-folder2-open text-muted fs-1 d-block mb-3"></i>
              <h3 class="h5 fw-bold text-dark">No documents found</h3>
              <p class="text-muted small mx-auto" style="max-width: 480px;">
                No merit lists or documents matched your search filter. Please choose another category or check back shortly as admissions announcements are uploaded regularly.
              </p>
              <a href="{{ url('/merit-lists') }}" class="btn btn-outline-secondary rounded-pill px-4 mt-2">
                Clear Filters
              </a>
            </div>
          </div>
        @endforelse
      </div>

      <!-- Pagination -->
      @if($documents->hasPages())
        <div class="d-flex justify-content-center mb-5">
          {{ $documents->links() }}
        </div>
      @endif

      <!-- Admission Notice Callout Box -->
      <div class="card shadow-sm border-0 rounded-4 p-4 p-md-5" style="background: linear-gradient(135deg, #071f33 0%, #0d3859 100%); color: #ffffff;">
        <div class="row align-items-center g-4">
          <div class="col-lg-8">
            <span class="badge bg-info text-dark px-3 py-1.5 mb-2 fw-bold text-uppercase">OFFICIAL MERIT VERIFICATION</span>
            <h3 class="h4 fw-bold text-white mb-2">Selected in a Merit List? Secure Your Seat.</h3>
            <p class="text-light-50 small mb-0" style="color: #cbd5e1; line-height: 1.6;">
              Shortlisted candidates must visit the college admissions office with their original Matriculation and F.Sc Pre-Medical DMCs, CNIC/B-Form, and paid bank deposit receipt within the designated enrollment window.
            </p>
          </div>
          <div class="col-lg-4 text-lg-end">
            <a href="{{ url('/track') }}" class="btn btn-light btn-lg px-4 me-2">
              <i class="bi bi-search me-1"></i> Track Status
            </a>
            <a href="{{ url('/contact') }}" class="btn btn-outline-light btn-lg px-4">
              Contact Desk
            </a>
          </div>
        </div>
      </div>

    </div>
</main>
@endsection

