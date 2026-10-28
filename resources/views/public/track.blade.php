@extends('layouts.public')
@php
  $active = 'track';

  $statusMeta = [
      'submitted' => [
          'step' => 1,
          'title' => 'Application Received',
          'badge' => 'bg-primary text-white',
          'icon' => 'bi-file-earmark-check-fill',
          'desc' => 'Your application and uploaded academic documents have been safely received in our central admissions repository.',
      ],
      'under_review' => [
          'step' => 2,
          'title' => 'Under Academic Review',
          'badge' => 'bg-warning text-dark',
          'icon' => 'bi-hourglass-split',
          'desc' => 'The admissions evaluation committee is currently verifying your marks certificates, prerequisite qualifications, and eligibility.',
      ],
      'shortlisted' => [
          'step' => 3,
          'title' => 'Shortlisted for Interview / Test',
          'badge' => 'bg-info text-dark',
          'icon' => 'bi-person-check-fill',
          'desc' => 'Congratulations! Your profile has been shortlisted. Please check committee instructions below and bring your original DMCs.',
      ],
      'accepted' => [
          'step' => 4,
          'title' => 'Admission Granted',
          'badge' => 'bg-success text-white',
          'icon' => 'bi-mortarboard-fill',
          'desc' => 'Formal admission offer granted! Please deposit your admission dues and complete physical document submission before the deadline.',
      ],
      'rejected' => [
          'step' => 2,
          'title' => 'Application Not Accepted',
          'badge' => 'bg-danger text-white',
          'icon' => 'bi-x-circle-fill',
          'desc' => 'Your application did not meet the required merit cutoff or institutional eligibility criteria for the current intake.',
      ],
  ];

  $currentStatus = $application ? ($statusMeta[$application->status] ?? $statusMeta['submitted']) : null;
@endphp

@section('content')
<main class="page-content-wrapper">
  <!-- Search & Tracking Section -->
  <div class="container py-4 py-md-5">
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb custom-breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door me-1"></i>Home</a></li>
        <li class="breadcrumb-item"><a href="{{ url('/admissions') }}">Admissions</a></li>
        <li class="breadcrumb-item active" aria-current="page">Track Application</li>
      </ol>
    </nav>

    <div class="text-center mb-4">
      <span class="eyebrow">ADMISSIONS PORTAL</span>
      <h1 class="page-title mb-2">Track Application Status</h1>
      <p class="text-muted mx-auto mb-0" style="max-width: 620px;">
        Monitor the real-time evaluation status of your admission form, document verification progress, and interview call letters.
      </p>
    </div>
      
      <!-- Lookup Form Card -->
      <div class="card shadow-sm border-0 rounded-4 mx-auto mb-5 p-4 p-md-5" style="max-width: 680px; background: #ffffff;">
        <div class="text-center mb-4">
          <div class="d-inline-flex p-3 rounded-circle bg-light text-primary mb-3">
            <i class="bi bi-search fs-3"></i>
          </div>
          <h2 class="h4 fw-bold text-dark">Find Your Admission File</h2>
          <p class="text-muted small">Enter the <strong>Application Reference Number</strong> issued upon submission, or your registered <strong>CNIC / B-Form Number</strong>.</p>
        </div>

        <form action="{{ url('/track') }}" method="GET" class="row g-2 align-items-center">
          <div class="col-12 col-sm">
            <div class="input-group input-group-lg">
              <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-upc-scan"></i></span>
              <input
                type="text"
                name="query"
                value="{{ $query }}"
                class="form-control border-start-0 ps-0"
                placeholder="e.g. DCN-2026-10482 or CNIC"
                required
                autocomplete="off"
              >
            </div>
          </div>
          <div class="col-12 col-sm-auto">
            <button type="submit" class="btn btn-primary btn-lg w-100 px-4">
              <i class="bi bi-arrow-right-circle me-1"></i> Track Status
            </button>
          </div>
        </form>

        @if($searchError)
          <div class="alert alert-danger mt-3 mb-0 d-flex align-items-center gap-2 rounded-3 small animate__animated animate__fadeIn">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div>{{ $searchError }}</div>
          </div>
        @endif
      </div>

      @if($application)
        <!-- Result Card -->
        <div class="card shadow border-0 rounded-4 mx-auto overflow-hidden animate__animated animate__fadeInUp" style="max-width: 820px; background: #ffffff;">
          
          <!-- Card Header Bar -->
          <div class="p-4 bg-dark text-white d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
              <span class="badge bg-secondary text-light text-uppercase px-2.5 py-1 mb-1" style="font-size: 11px; letter-spacing: 0.05em;">
                OFFICIAL ADMISSION RECORD
              </span>
              <h3 class="h5 fw-bold text-white mb-0">Ref: {{ $application->reference }}</h3>
            </div>
            <div>
              <span class="badge {{ $currentStatus['badge'] }} px-3 py-2 fs-6 rounded-pill">
                <i class="bi {{ $currentStatus['icon'] }} me-1"></i> {{ $currentStatus['title'] }}
              </span>
            </div>
          </div>

          <!-- Stepper Visual -->
          <div class="p-4 p-md-5 border-bottom bg-light">
            <h4 class="h6 text-uppercase fw-bold text-muted mb-4 text-center" style="letter-spacing: 0.06em;">Application Lifecycle Progress</h4>
            
            <div class="d-flex justify-content-between position-relative px-2 px-md-4">
              <!-- Stepper Connecting Line -->
              <div class="position-absolute start-0 end-0 top-50 translate-middle-y bg-secondary-subtle" style="height: 4px; z-index: 1; margin: 0 10%;"></div>
              
              <!-- Step 1 -->
              <div class="text-center position-relative" style="z-index: 2; width: 22%;">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success text-white fw-bold shadow-sm mb-2" style="width: 44px; height: 44px;">
                  <i class="bi bi-check-lg fs-5"></i>
                </div>
                <div class="small fw-bold text-dark d-none d-sm-block">Received</div>
                <div class="text-muted" style="font-size: 11px;">Registered</div>
              </div>

              <!-- Step 2 -->
              <div class="text-center position-relative" style="z-index: 2; width: 22%;">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle {{ $currentStatus['step'] >= 2 ? ($application->status === 'rejected' ? 'bg-danger text-white' : 'bg-primary text-white') : 'bg-white border text-muted' }} fw-bold shadow-sm mb-2" style="width: 44px; height: 44px;">
                  @if($currentStatus['step'] > 2)
                    <i class="bi bi-check-lg fs-5"></i>
                  @elseif($currentStatus['step'] === 2)
                    <i class="bi {{ $application->status === 'rejected' ? 'bi-x-lg' : 'bi-hourglass-split' }} fs-5"></i>
                  @else
                    2
                  @endif
                </div>
                <div class="small fw-bold text-dark d-none d-sm-block">Verification</div>
                <div class="text-muted" style="font-size: 11px;">Credentials</div>
              </div>

              <!-- Step 3 -->
              <div class="text-center position-relative" style="z-index: 2; width: 22%;">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle {{ $currentStatus['step'] >= 3 ? 'bg-primary text-white' : 'bg-white border text-muted' }} fw-bold shadow-sm mb-2" style="width: 44px; height: 44px;">
                  @if($currentStatus['step'] > 3)
                    <i class="bi bi-check-lg fs-5"></i>
                  @elseif($currentStatus['step'] === 3)
                    <i class="bi bi-mortarboard fs-5"></i>
                  @else
                    3
                  @endif
                </div>
                <div class="small fw-bold text-dark d-none d-sm-block">Shortlist</div>
                <div class="text-muted" style="font-size: 11px;">Interview</div>
              </div>

              <!-- Step 4 -->
              <div class="text-center position-relative" style="z-index: 2; width: 22%;">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle {{ $currentStatus['step'] >= 4 ? 'bg-success text-white' : 'bg-white border text-muted' }} fw-bold shadow-sm mb-2" style="width: 44px; height: 44px;">
                  @if($currentStatus['step'] >= 4)
                    <i class="bi bi-check2-all fs-4"></i>
                  @else
                    4
                  @endif
                </div>
                <div class="small fw-bold text-dark d-none d-sm-block">Enrolled</div>
                <div class="text-muted" style="font-size: 11px;">Decision</div>
              </div>
            </div>

            <!-- Current Stage Note -->
            <div class="alert alert-light border rounded-3 mt-4 mb-0 text-center text-md-start d-flex flex-column flex-md-row align-items-center gap-3">
              <i class="bi bi-info-circle-fill text-primary fs-4"></i>
              <div>
                <strong class="text-dark d-block mb-1">{{ $currentStatus['title'] }}</strong>
                <span class="text-muted small">{{ $currentStatus['desc'] }}</span>
              </div>
            </div>
          </div>

          <!-- Application Details Grid -->
          <div class="p-4 p-md-5">
            <h4 class="h6 text-uppercase fw-bold text-muted mb-4" style="letter-spacing: 0.06em;">Applicant Summary</h4>

            <div class="row g-3 g-md-4 mb-4">
              <div class="col-sm-6">
                <div class="p-3 bg-light rounded-3 border">
                  <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Applicant Name</span>
                  <strong class="text-dark fs-6">{{ $application->first_name }} {{ substr($application->last_name, 0, 1) }}***</strong>
                </div>
              </div>

              <div class="col-sm-6">
                <div class="p-3 bg-light rounded-3 border">
                  <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Selected Program</span>
                  <strong class="text-dark fs-6">{{ $application->program?->name ?? 'BS Nursing' }}</strong>
                  <span class="badge bg-secondary ms-1">{{ $application->program?->duration ?? '4 Years' }}</span>
                </div>
              </div>

              <div class="col-sm-6">
                <div class="p-3 bg-light rounded-3 border">
                  <span class="d-block text-muted small text-uppercase fw-semibold mb-1">CNIC / Identification</span>
                  <strong class="text-dark fs-6">{{ $application->national_id ? substr($application->national_id, 0, 5) . '*****' . substr($application->national_id, -2) : 'Provided' }}</strong>
                </div>
              </div>

              <div class="col-sm-6">
                <div class="p-3 bg-light rounded-3 border">
                  <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Submission Date</span>
                  <strong class="text-dark fs-6">{{ $application->submitted_at ? $application->submitted_at->format('M d, Y · h:i A') : $application->created_at->format('M d, Y') }}</strong>
                </div>
              </div>
            </div>

            @if(!empty($application->admin_notes))
              <!-- Official Committee Remarks -->
              <div class="alert alert-info border-info rounded-3 mb-4 p-3.5">
                <div class="d-flex gap-2.5 align-items-start">
                  <i class="bi bi-chat-quote-fill fs-5 mt-1 text-info"></i>
                  <div>
                    <strong class="d-block text-dark mb-1">Admissions Office Notice</strong>
                    <p class="mb-0 text-dark small" style="white-space: pre-wrap;">{{ $application->admin_notes }}</p>
                  </div>
                </div>
              </div>
            @endif

            <!-- Action Buttons -->
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 pt-3 border-top">
              <a href="{{ url('/track') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Look Up Another
              </a>
              <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-dark" onclick="window.print()">
                  <i class="bi bi-printer me-1"></i> Print Slip
                </button>
                <a href="{{ url('/contact') }}" class="btn btn-primary">
                  <i class="bi bi-headset me-1"></i> Contact Admissions
                </a>
              </div>
            </div>
          </div>
        </div>
      @else
        <!-- Information Grid for First-Time Visitors -->
        <div class="row g-4 justify-content-center" style="max-width: 900px; margin: 0 auto;">
          <div class="col-md-4">
            <div class="p-4 bg-white rounded-4 border h-100 text-center">
              <i class="bi bi-receipt-cutoff text-primary fs-1 mb-3 d-block"></i>
              <h3 class="h6 fw-bold">Where is my reference?</h3>
              <p class="text-muted small mb-0">Your reference number (e.g. <code>DCN-2026-XXXXX</code>) was displayed on the screen immediately after submitting your online application.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="p-4 bg-white rounded-4 border h-100 text-center">
              <i class="bi bi-clock-history text-primary fs-1 mb-3 d-block"></i>
              <h3 class="h6 fw-bold">Verification Timeline</h3>
              <p class="text-muted small mb-0">Document scrutiny and initial eligibility screening usually takes <strong>3 to 5 business days</strong> following submission.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="p-4 bg-white rounded-4 border h-100 text-center">
              <i class="bi bi-patch-question text-primary fs-1 mb-3 d-block"></i>
              <h3 class="h6 fw-bold">Need Help?</h3>
              <p class="text-muted small mb-0">If you lost your reference or need to amend uploaded documents, reach out to our campus admissions desk.</p>
            </div>
          </div>
        </div>
      @endif

    </div>
</main>
@endsection

