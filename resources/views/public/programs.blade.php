@extends('layouts.public')
@php
  $active = 'programs';
@endphp
@section('content')
<main class="page-content-wrapper">
  <div class="container py-4 py-md-5">
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb custom-breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door me-1"></i>Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Programs</li>
      </ol>
    </nav>
    <div class="programs-page-heading mb-4">
      <div>
        <span class="eyebrow">ACADEMIC PROGRAMS</span>
        <h1 class="page-title mb-1">All Programs</h1>
        <p class="text-muted mb-0">Career-focused healthcare education supported by certified faculty and modern clinical laboratories.</p>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ url('/contact') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-headset me-1"></i> Speak with Admissions</a>
      </div>
    </div>
    <div class="row g-4">
        @forelse($programs as $program)
          <div class="col-md-6 col-xl-4 d-flex">
            <article class="program-list-card w-100">
              <div class="program-card-image-wrap">
                <img src="{{ $program['image_url'] ?? asset('images/nursing-hero.png') }}" alt="{{ $program['name'] }}" loading="lazy">
                <span class="program-duration-badge"><i class="bi bi-clock-history me-1"></i> {{ $program['duration'] }}</span>
              </div>
              <div class="program-list-body">
                <h2>{{ $program['name'] }}</h2>
                <p class="program-description">{{ $program['description'] }}</p>
                @if(! empty($program['fees']) && count($program['fees']) > 0)
                  <div class="fee-breakdown">
                    <div class="fee-heading">
                      <span><i class="bi bi-cash-stack"></i> Fee Structure</span>
                      <small>{{ count($program['fees']) }} {{ count($program['fees']) === 1 ? 'Charge' : 'Charges' }}</small>
                    </div>
                    <div class="fee-rows">
                      @foreach($program['fees'] as $fee)
                        @php
                          $feeTitle = $fee['fee_type'] ?? $fee['type'] ?? 'Tuition Fee';
                          $basis = $fee['billing_basis'] ?? $fee['basis'] ?? '';
                          $basisLabel = match(strtolower(trim((string)$basis))) {
                            'one time', 'one-time' => 'One-time',
                            'per year', 'annual' => 'Per Year',
                            'per semester', 'semester' => 'Per Semester',
                            default => $basis ? ucfirst((string)$basis) : ''
                          };
                          $currency = $fee['currency'] ?? 'PKR';
                          $amount = number_format((float) ($fee['amount'] ?? 0));
                        @endphp
                        <div class="fee-row">
                          <span class="fee-row-label">
                            {{ $feeTitle }}
                            @if($basisLabel)
                              <small class="fee-row-basis">{{ $basisLabel }}</small>
                            @endif
                          </span>
                          <b class="fee-row-amount">{{ $currency }} {{ $amount }}</b>
                        </div>
                      @endforeach
                    </div>
                  </div>
                @else
                  <div class="fee-breakdown fee-breakdown-contact">
                    <div class="fee-heading">
                      <span><i class="bi bi-info-circle"></i> Fee Schedule</span>
                      <small>Admissions</small>
                    </div>
                    <div class="p-3 text-center">
                      <small class="text-muted d-block">Contact admissions office for current semester fee schedule and scholarship opportunities.</small>
                    </div>
                  </div>
                @endif
                <div class="program-actions">
                  <a href="{{ url('/apply') }}" class="btn btn-primary">Apply Now</a>
                  <a href="mailto:{{ $site['college_email'] ?? 'admissions@example.edu' }}?subject={{ rawurlencode('Inquiry regarding ' . $program['name']) }}" class="details-link">
                    Request details <i class="bi bi-arrow-right"></i>
                  </a>
                </div>
              </div>
            </article>
          </div>
        @empty
          <div class="empty-programs"><i class="bi bi-journal-medical"></i><h3>No programs are currently published.</h3><p>Please contact admissions for current course information.</p></div>
        @endforelse
      </div>
    </div>
</main>
@endsection
