@extends('layouts.public')
@php
  $active = 'apply';
@endphp

@section('content')
<main>
  <!-- Application Wizard Section -->
  <section class="section application-bg pt-4">
    <div class="container">
      <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb custom-breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door me-1"></i>Home</a></li>
          <li class="breadcrumb-item"><a href="{{ url('/admissions') }}">Admissions</a></li>
          <li class="breadcrumb-item active" aria-current="page">Online Application</li>
        </ol>
      </nav>
      @if(session('success_reference'))
        <!-- Success Screen -->
        <div class="application-success animate__animated animate__fadeIn">
          <div class="application-success-icon">
            <i class="bi bi-check-lg"></i>
          </div>
          <h2>Application Submitted Successfully!</h2>
          <p class="mx-auto" style="max-width: 520px;">
            Thank you for applying to <strong>{{ $collegeName }}</strong>. Your submission is now officially registered in our admissions system.
          </p>

          <div class="success-reference-box">
            <span class="success-reference-label">Your Application Reference Number</span>
            <span class="success-reference-num">{{ session('success_reference') }}</span>
          </div>

          <div class="alert alert-light border text-start mx-auto p-3 mb-4 rounded-3" style="max-width: 540px;">
            <div class="d-flex gap-3 align-items-start">
              <i class="bi bi-info-circle-fill text-primary fs-5 mt-1"></i>
              <div class="small text-muted">
                <strong class="d-block text-dark mb-1">What Happens Next?</strong>
                Our admissions committee will verify your academic transcripts. You will receive an interview schedule notification via email and SMS within 3 to 5 working days.
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="{{ url('/') }}" class="btn btn-primary px-4 py-2"><i class="bi bi-house-door-fill me-2"></i>Return to Home</a>
            <a href="{{ url('/track?query='.session('success_reference')) }}" class="btn btn-outline-primary px-4 py-2"><i class="bi bi-search me-2"></i>Track Application Live</a>
            <button type="button" class="btn btn-outline-secondary px-4 py-2" onclick="window.print()"><i class="bi bi-printer me-2"></i>Print Confirmation</button>
          </div>
        </div>
      @else
        <!-- Multi-Step Application Wizard Card -->
        <div class="wizard-card" id="applicationWizard">
          
          <!-- Wizard Top Bar -->
          <div class="wizard-top-header">
            <div class="wizard-title-wrap">
              <div>
                <h2>Admission Application Portal</h2>
                <p>Complete all 4 steps to submit your formal application for review.</p>
              </div>
              <div class="wizard-security-badge">
                <i class="bi bi-shield-lock-fill"></i> 256-Bit SSL Encrypted
              </div>
            </div>
          </div>

          <!-- Sticky Stepper & Progress Bar -->
          <div class="wizard-progress-section" id="wizardStickyNav">
            <!-- Linear Progress Track -->
            <div class="wizard-progress-bar-track">
              <div class="wizard-progress-bar-fill" id="wizardProgressBar" style="width: 25%;" aria-hidden="true"></div>
            </div>

            <!-- Segmented Stepper Tabs -->
            <div class="wizard-stepper" role="tablist" aria-label="Application Progress">
              <button type="button" class="stepper-tab is-active" id="stepTab1" data-step="1" role="tab" aria-selected="true" aria-controls="stepPane1" onclick="window.goToWizardStep && window.goToWizardStep(1)">
                <span class="stepper-badge" id="stepBadge1">1</span>
                <div class="stepper-info">
                  <span class="stepper-step-label">Step 1</span>
                  <span class="stepper-step-title">Program Selection</span>
                </div>
              </button>

              <button type="button" class="stepper-tab" id="stepTab2" data-step="2" role="tab" aria-selected="false" aria-controls="stepPane2" onclick="window.goToWizardStep && window.goToWizardStep(2)">
                <span class="stepper-badge" id="stepBadge2">2</span>
                <div class="stepper-info">
                  <span class="stepper-step-label">Step 2</span>
                  <span class="stepper-step-title">Personal Details</span>
                </div>
              </button>

              <button type="button" class="stepper-tab" id="stepTab3" data-step="3" role="tab" aria-selected="false" aria-controls="stepPane3" onclick="window.goToWizardStep && window.goToWizardStep(3)">
                <span class="stepper-badge" id="stepBadge3">3</span>
                <div class="stepper-info">
                  <span class="stepper-step-label">Step 3</span>
                  <span class="stepper-step-title">Education</span>
                </div>
              </button>

              <button type="button" class="stepper-tab" id="stepTab4" data-step="4" role="tab" aria-selected="false" aria-controls="stepPane4" onclick="window.goToWizardStep && window.goToWizardStep(4)">
                <span class="stepper-badge" id="stepBadge4">4</span>
                <div class="stepper-info">
                  <span class="stepper-step-label">Step 4</span>
                  <span class="stepper-step-title">Documents & Review</span>
                </div>
              </button>
            </div>
          </div>

          <!-- Application Form -->
          <form id="wizardForm" class="wizard-form" method="POST" action="{{ url('/apply') }}" enctype="multipart/form-data" novalidate>
            @csrf

            <!-- Global Error Banner if Server Returns Errors -->
            @if($errors->any())
              <div class="p-3 mx-4 mt-4 alert alert-danger rounded-3" role="alert">
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                  <div>
                    <strong>Please review the highlighted fields in the application form.</strong>
                    <div class="small">Correct the indicated errors and proceed through the steps to submit.</div>
                  </div>
                </div>
              </div>
            @endif

            <div class="wizard-body">

              <!-- ==========================================
                   STEP 1: PROGRAM SELECTION
                   ========================================== -->
              <div class="wizard-step-pane is-active" id="stepPane1" role="tabpanel" aria-labelledby="stepTab1">
                <div class="step-section-heading">
                  <span class="eyebrow text-primary">SELECT YOUR PATHWAY</span>
                  <h3>Choose an Academic Program</h3>
                  <p>Select the accredited degree or diploma program you wish to enroll in for the upcoming academic session.</p>
                </div>

                <!-- Hidden Input bound to form post -->
                <input type="hidden" name="program_id" id="selectedProgramId" value="{{ old('program_id') }}" required>

                <!-- Visual Program Cards Grid -->
                <div class="program-cards-grid" id="programCardsContainer" role="radiogroup" aria-label="Select Academic Program">
                  @foreach($programs as $p)
                    @php
                      $isSelected = old('program_id') == $p->id;
                    @endphp
                    <div class="program-card-option {{ $isSelected ? 'is-selected' : '' }}"
                         data-program-id="{{ $p->id }}"
                         data-program-name="{{ $p->name }}"
                         data-program-duration="{{ $p->duration }}"
                         tabindex="0"
                         role="radio"
                         aria-checked="{{ $isSelected ? 'true' : 'false' }}"
                         onclick="window.selectWizardProgram && window.selectWizardProgram(this)">
                      <div>
                        <div class="program-card-header">
                          <span class="program-card-badge">
                            <i class="bi bi-clock-history"></i> {{ $p->duration }}
                          </span>
                          <span class="program-card-check">
                            <i class="bi bi-check-lg"></i>
                          </span>
                        </div>
                        <h4 class="program-card-title">{{ $p->name }}</h4>
                        <p class="program-card-desc">{{ $p->description ?: 'Rigorous clinical and laboratory training meeting national statutory healthcare council standards.' }}</p>
                      </div>

                      <div class="program-card-meta">
                        <span><i class="bi bi-mortarboard-fill me-1 text-primary"></i> Accredited Program</span>
                        <span class="text-primary fw-bold">Select <i class="bi bi-arrow-right-short"></i></span>
                      </div>
                    </div>
                  @endforeach
                </div>

                <div class="field-error-message text-danger mb-3" id="programIdError" style="{{ $errors->has('program_id') ? 'display:flex;' : 'display:none;' }}">
                  <i class="bi bi-exclamation-circle-fill"></i>
                  <span>{{ $errors->first('program_id') ?: 'Please select a program to continue.' }}</span>
                </div>

                <!-- Dynamic Fee Breakdown Panel -->
                @foreach($programs as $p)
                  @if($p->fees->isNotEmpty())
                    <div id="fee-box-{{ $p->id }}" class="program-fee-panel fee-breakdown mt-4 animate__animated animate__fadeIn" style="display: {{ old('program_id') == $p->id ? 'block' : 'none' }};">
                      <div class="fee-heading d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="bi bi-cash-stack text-primary me-2"></i>Official Fee Structure — {{ $p->name }}</span>
                        <span class="badge bg-primary bg-opacity-10 text-primary">{{ $p->fees->count() }} Fee Components</span>
                      </div>
                      <div class="fee-rows mt-3">
                        @foreach($p->fees as $fee)
                          <div class="d-flex justify-content-between py-2 border-bottom border-light">
                            <span>{{ $fee->fee_type }} <small class="text-muted">({{ $fee->billing_basis }})</small></span>
                            <strong class="text-dark">{{ $fee->currency ?: 'PKR' }} {{ number_format($fee->amount) }}</strong>
                          </div>
                        @endforeach
                      </div>
                      @if($p->fees->whereNotNull('notes')->first())
                        <div class="mt-3 p-2 bg-light rounded text-muted small">
                          <i class="bi bi-info-circle me-1 text-primary"></i> {{ $p->fees->whereNotNull('notes')->first()->notes }}
                        </div>
                      @endif
                    </div>
                  @endif
                @endforeach
              </div>

              <!-- ==========================================
                   STEP 2: PERSONAL INFORMATION
                   ========================================== -->
              <div class="wizard-step-pane" id="stepPane2" role="tabpanel" aria-labelledby="stepTab2">
                <div class="step-section-heading">
                  <span class="eyebrow text-primary">APPLICANT IDENTIFICATION</span>
                  <h3>Personal Information</h3>
                  <p>Provide accurate legal details as stated on your government identity card (CNIC) or Form-B.</p>
                </div>

                <div class="row g-3">
                  <!-- First Name -->
                  <div class="col-md-6">
                    <div class="form-field-group {{ $errors->has('first_name') ? 'is-invalid' : '' }}">
                      <label for="first_name" class="form-label-custom">First Name <span class="req">*</span></label>
                      <div class="input-icon-wrap">
                        <input id="first_name" name="first_name" type="text" class="form-control-custom @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" placeholder="e.g. Ayesha" required maxlength="80">
                        <i class="bi bi-check-circle-fill field-validation-icon icon-valid"></i>
                        <i class="bi bi-exclamation-circle-fill field-validation-icon icon-invalid"></i>
                      </div>
                      <div class="field-error-message">{{ $errors->first('first_name') ?: 'First name is required.' }}</div>
                    </div>
                  </div>

                  <!-- Last Name -->
                  <div class="col-md-6">
                    <div class="form-field-group {{ $errors->has('last_name') ? 'is-invalid' : '' }}">
                      <label for="last_name" class="form-label-custom">Last Name <span class="req">*</span></label>
                      <div class="input-icon-wrap">
                        <input id="last_name" name="last_name" type="text" class="form-control-custom @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" placeholder="e.g. Khan" required maxlength="80">
                        <i class="bi bi-check-circle-fill field-validation-icon icon-valid"></i>
                        <i class="bi bi-exclamation-circle-fill field-validation-icon icon-invalid"></i>
                      </div>
                      <div class="field-error-message">{{ $errors->first('last_name') ?: 'Last name is required.' }}</div>
                    </div>
                  </div>

                  <!-- Date of Birth -->
                  <div class="col-md-6">
                    <div class="form-field-group {{ $errors->has('date_of_birth') ? 'is-invalid' : '' }}">
                      <label for="date_of_birth" class="form-label-custom">Date of Birth <span class="req">*</span></label>
                      <div class="input-icon-wrap">
                        <input id="date_of_birth" name="date_of_birth" type="date" class="form-control-custom @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth') }}" max="{{ date('Y-m-d', strtotime('-14 years')) }}" required>
                        <i class="bi bi-check-circle-fill field-validation-icon icon-valid"></i>
                        <i class="bi bi-exclamation-circle-fill field-validation-icon icon-invalid"></i>
                      </div>
                      <div class="field-error-message">{{ $errors->first('date_of_birth') ?: 'Date of birth is required.' }}</div>
                    </div>
                  </div>

                  <!-- Gender -->
                  <div class="col-md-6">
                    <div class="form-field-group {{ $errors->has('gender') ? 'is-invalid' : '' }}">
                      <label for="gender" class="form-label-custom">Gender <span class="req">*</span></label>
                      <div class="input-icon-wrap">
                        <select id="gender" name="gender" class="form-select-custom @error('gender') is-invalid @enderror" required>
                          <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select Gender</option>
                          <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                          <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                          <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                          <option value="prefer_not_to_say" {{ old('gender') === 'prefer_not_to_say' ? 'selected' : '' }}>Prefer not to say</option>
                        </select>
                        <i class="bi bi-check-circle-fill field-validation-icon icon-valid"></i>
                        <i class="bi bi-exclamation-circle-fill field-validation-icon icon-invalid"></i>
                      </div>
                      <div class="field-error-message">{{ $errors->first('gender') ?: 'Please select your gender.' }}</div>
                    </div>
                  </div>

                  <!-- Email Address -->
                  <div class="col-md-6">
                    <div class="form-field-group {{ $errors->has('email') ? 'is-invalid' : '' }}">
                      <label for="email" class="form-label-custom">Email Address <span class="req">*</span></label>
                      <div class="input-icon-wrap">
                        <input id="email" name="email" type="email" class="form-control-custom @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="applicant@example.com" required maxlength="150">
                        <i class="bi bi-check-circle-fill field-validation-icon icon-valid"></i>
                        <i class="bi bi-exclamation-circle-fill field-validation-icon icon-invalid"></i>
                      </div>
                      <div class="field-error-message">{{ $errors->first('email') ?: 'Please enter a valid email address.' }}</div>
                    </div>
                  </div>

                  <!-- Phone Number -->
                  <div class="col-md-6">
                    <div class="form-field-group {{ $errors->has('phone') ? 'is-invalid' : '' }}">
                      <label for="phone" class="form-label-custom">Phone Number <span class="req">*</span></label>
                      <div class="input-icon-wrap">
                        <input id="phone" name="phone" type="tel" class="form-control-custom @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="+92 300 1234567" required maxlength="40">
                        <i class="bi bi-check-circle-fill field-validation-icon icon-valid"></i>
                        <i class="bi bi-exclamation-circle-fill field-validation-icon icon-invalid"></i>
                      </div>
                      <div class="field-error-message">{{ $errors->first('phone') ?: 'Valid contact phone number is required.' }}</div>
                    </div>
                  </div>

                  <!-- National ID / CNIC -->
                  <div class="col-md-6">
                    <div class="form-field-group {{ $errors->has('national_id') ? 'is-invalid' : '' }}">
                      <label for="national_id" class="form-label-custom">National ID / Form-B / Passport</label>
                      <div class="input-icon-wrap">
                        <input id="national_id" name="national_id" type="text" class="form-control-custom @error('national_id') is-invalid @enderror" value="{{ old('national_id') }}" placeholder="e.g. 15302-XXXXXXX-X" maxlength="80">
                        <i class="bi bi-check-circle-fill field-validation-icon icon-valid"></i>
                        <i class="bi bi-exclamation-circle-fill field-validation-icon icon-invalid"></i>
                      </div>
                      <div class="field-error-message">{{ $errors->first('national_id') }}</div>
                    </div>
                  </div>

                  <!-- City -->
                  <div class="col-md-6">
                    <div class="form-field-group {{ $errors->has('city') ? 'is-invalid' : '' }}">
                      <label for="city" class="form-label-custom">City / District <span class="req">*</span></label>
                      <div class="input-icon-wrap">
                        <input id="city" name="city" type="text" class="form-control-custom @error('city') is-invalid @enderror" value="{{ old('city') }}" placeholder="e.g. Timergara, Dir Lower" required maxlength="100">
                        <i class="bi bi-check-circle-fill field-validation-icon icon-valid"></i>
                        <i class="bi bi-exclamation-circle-fill field-validation-icon icon-invalid"></i>
                      </div>
                      <div class="field-error-message">{{ $errors->first('city') ?: 'City name is required.' }}</div>
                    </div>
                  </div>

                  <!-- Country -->
                  <div class="col-md-6">
                    <div class="form-field-group {{ $errors->has('country') ? 'is-invalid' : '' }}">
                      <label for="country" class="form-label-custom">Country <span class="req">*</span></label>
                      <div class="input-icon-wrap">
                        <input id="country" name="country" type="text" class="form-control-custom @error('country') is-invalid @enderror" value="{{ old('country', 'Pakistan') }}" required maxlength="100">
                        <i class="bi bi-check-circle-fill field-validation-icon icon-valid"></i>
                        <i class="bi bi-exclamation-circle-fill field-validation-icon icon-invalid"></i>
                      </div>
                      <div class="field-error-message">{{ $errors->first('country') ?: 'Country is required.' }}</div>
                    </div>
                  </div>

                  <!-- Address -->
                  <div class="col-12">
                    <div class="form-field-group {{ $errors->has('address') ? 'is-invalid' : '' }}">
                      <label for="address" class="form-label-custom">Full Residential Address <span class="req">*</span></label>
                      <div class="input-icon-wrap">
                        <textarea id="address" name="address" class="form-control-custom @error('address') is-invalid @enderror" rows="3" placeholder="House/Street, Village, Tehsil, District" required maxlength="1000">{{ old('address') }}</textarea>
                        <i class="bi bi-check-circle-fill field-validation-icon icon-valid" style="top:25px;"></i>
                        <i class="bi bi-exclamation-circle-fill field-validation-icon icon-invalid" style="top:25px;"></i>
                      </div>
                      <div class="field-error-message">{{ $errors->first('address') ?: 'Residential address is required.' }}</div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- ==========================================
                   STEP 3: EDUCATIONAL BACKGROUND
                   ========================================== -->
              <div class="wizard-step-pane" id="stepPane3" role="tabpanel" aria-labelledby="stepTab3">
                <div class="step-section-heading">
                  <span class="eyebrow text-primary">PREVIOUS ACADEMIC CREDENTIALS</span>
                  <h3>Educational Background</h3>
                  <p>Provide details of your most recent verified educational degree or diploma.</p>
                </div>

                <div class="row g-3">
                  <!-- Highest Qualification -->
                  <div class="col-md-6">
                    <div class="form-field-group {{ $errors->has('highest_qualification') ? 'is-invalid' : '' }}">
                      <label for="highest_qualification" class="form-label-custom">Highest Qualification <span class="req">*</span></label>
                      <div class="input-icon-wrap">
                        <input id="highest_qualification" name="highest_qualification" type="text" class="form-control-custom @error('highest_qualification') is-invalid @enderror" value="{{ old('highest_qualification') }}" placeholder="e.g. FSc Pre-Medical / Matric / General Nursing" required maxlength="150">
                        <i class="bi bi-check-circle-fill field-validation-icon icon-valid"></i>
                        <i class="bi bi-exclamation-circle-fill field-validation-icon icon-invalid"></i>
                      </div>
                      <div class="field-error-message">{{ $errors->first('highest_qualification') ?: 'Qualification title is required.' }}</div>
                    </div>
                  </div>

                  <!-- Institution / Board -->
                  <div class="col-md-6">
                    <div class="form-field-group {{ $errors->has('institution') ? 'is-invalid' : '' }}">
                      <label for="institution" class="form-label-custom">Institution / Board / University <span class="req">*</span></label>
                      <div class="input-icon-wrap">
                        <input id="institution" name="institution" type="text" class="form-control-custom @error('institution') is-invalid @enderror" value="{{ old('institution') }}" placeholder="e.g. BISE Malakand / BISE Peshawar" required maxlength="180">
                        <i class="bi bi-check-circle-fill field-validation-icon icon-valid"></i>
                        <i class="bi bi-exclamation-circle-fill field-validation-icon icon-invalid"></i>
                      </div>
                      <div class="field-error-message">{{ $errors->first('institution') ?: 'Institution or board name is required.' }}</div>
                    </div>
                  </div>

                  <!-- Graduation Year -->
                  <div class="col-md-6">
                    <div class="form-field-group {{ $errors->has('graduation_year') ? 'is-invalid' : '' }}">
                      <label for="graduation_year" class="form-label-custom">Graduation Year <span class="req">*</span></label>
                      <div class="input-icon-wrap">
                        <input id="graduation_year" name="graduation_year" type="number" min="1950" max="{{ date('Y') + 1 }}" class="form-control-custom @error('graduation_year') is-invalid @enderror" value="{{ old('graduation_year', date('Y')) }}" required>
                        <i class="bi bi-check-circle-fill field-validation-icon icon-valid"></i>
                        <i class="bi bi-exclamation-circle-fill field-validation-icon icon-invalid"></i>
                      </div>
                      <div class="field-error-message">{{ $errors->first('graduation_year') ?: 'Valid graduation year is required.' }}</div>
                    </div>
                  </div>

                  <!-- Grade / Percentage -->
                  <div class="col-md-6">
                    <div class="form-field-group {{ $errors->has('grade_percentage') ? 'is-invalid' : '' }}">
                      <label for="grade_percentage" class="form-label-custom">Marks Percentage or CGPA</label>
                      <div class="input-icon-wrap">
                        <input id="grade_percentage" name="grade_percentage" type="number" step="0.01" min="0" max="100" class="form-control-custom @error('grade_percentage') is-invalid @enderror" value="{{ old('grade_percentage') }}" placeholder="e.g. 78.50">
                        <i class="bi bi-check-circle-fill field-validation-icon icon-valid"></i>
                        <i class="bi bi-exclamation-circle-fill field-validation-icon icon-invalid"></i>
                      </div>
                      <div class="field-error-message">{{ $errors->first('grade_percentage') ?: 'Percentage must be between 0 and 100.' }}</div>
                    </div>
                  </div>

                  <!-- Personal Statement with Live Character Counter -->
                  <div class="col-12">
                    <div class="form-field-group {{ $errors->has('statement') ? 'is-invalid' : '' }}">
                      <label for="statement" class="form-label-custom">Personal Statement (Optional)</label>
                      <div class="input-icon-wrap">
                        <textarea id="statement" name="statement" class="form-control-custom @error('statement') is-invalid @enderror" rows="4" placeholder="Briefly describe your passion for healthcare and why you are choosing {{ $collegeName }}." maxlength="3000">{{ old('statement') }}</textarea>
                      </div>
                      
                      <!-- Live Character Counter Bar -->
                      <div class="statement-counter-bar">
                        <span id="statementCountText"><strong id="statementCharCount">0</strong> / 3,000 characters</span>
                        <div class="char-meter-wrap" aria-hidden="true">
                          <div class="char-meter-fill" id="statementCharMeter"></div>
                        </div>
                      </div>
                      <div class="field-error-message">{{ $errors->first('statement') }}</div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- ==========================================
                   STEP 4: SUPPORTING DOCUMENTS & REVIEW
                   ========================================== -->
              <div class="wizard-step-pane" id="stepPane4" role="tabpanel" aria-labelledby="stepTab4">
                <div class="step-section-heading">
                  <span class="eyebrow text-primary">OFFICIAL VERIFICATION</span>
                  <h3>Supporting Documents & Declaration</h3>
                  <p>Upload clear scanned copies of your academic transcript and identity document (PDF, JPG, JPEG, or PNG up to 5 MB each).</p>
                </div>

                <div class="row g-4 mb-4">
                  <!-- Transcript Upload Dropzone -->
                  <div class="col-lg-6">
                    <label class="form-label-custom">Academic Transcript / DMC <span class="req">*</span></label>
                    
                    <!-- Interactive Drop Area -->
                    <div class="dropzone-container" id="dropzoneTranscript" tabindex="0" role="button" aria-label="Upload Academic Transcript">
                      <input type="file" id="transcript" name="transcript" accept=".pdf,.jpg,.jpeg,.png" class="d-none">
                      <div class="dropzone-icon-circle">
                        <i class="bi bi-cloud-arrow-up-fill"></i>
                      </div>
                      <div class="dropzone-prompt-text">
                        Drag and drop your transcript, or <span>browse</span>
                      </div>
                      <p class="dropzone-subtext">PDF, JPG, JPEG or PNG (Max size: 5 MB)</p>
                    </div>

                    <!-- File Attached Preview Card -->
                    <div class="dropzone-preview-card mt-2" id="previewTranscript">
                      <div class="dropzone-preview-left">
                        <div class="preview-file-icon">
                          <i class="bi bi-file-earmark-check-fill"></i>
                        </div>
                        <div class="preview-file-details">
                          <div class="preview-file-name" id="transcriptFileName">transcript.pdf</div>
                          <div class="preview-file-meta">
                            <i class="bi bi-check-circle-fill"></i>
                            <span id="transcriptFileSize">1.2 MB</span> • Verified
                          </div>
                        </div>
                      </div>
                      <button type="button" class="btn-remove-file" id="removeTranscriptBtn" title="Remove file" aria-label="Remove transcript file">
                        <i class="bi bi-x-lg"></i>
                      </button>
                    </div>

                    <div class="field-error-message text-danger mt-1" id="transcriptError" style="{{ $errors->has('transcript') ? 'display:flex;' : 'display:none;' }}">
                      <i class="bi bi-exclamation-circle-fill"></i>
                      <span>{{ $errors->first('transcript') ?: 'Please upload your academic transcript or DMC.' }}</span>
                    </div>
                  </div>

                  <!-- Identity Document Upload Dropzone -->
                  <div class="col-lg-6">
                    <label class="form-label-custom">National ID / Form-B / Passport <span class="req">*</span></label>
                    
                    <!-- Interactive Drop Area -->
                    <div class="dropzone-container" id="dropzoneIdentity" tabindex="0" role="button" aria-label="Upload Identity Document">
                      <input type="file" id="identity_document" name="identity_document" accept=".pdf,.jpg,.jpeg,.png" class="d-none">
                      <div class="dropzone-icon-circle">
                        <i class="bi bi-person-badge-fill"></i>
                      </div>
                      <div class="dropzone-prompt-text">
                        Drag and drop your ID/Passport, or <span>browse</span>
                      </div>
                      <p class="dropzone-subtext">PDF, JPG, JPEG or PNG (Max size: 5 MB)</p>
                    </div>

                    <!-- File Attached Preview Card -->
                    <div class="dropzone-preview-card mt-2" id="previewIdentity">
                      <div class="dropzone-preview-left">
                        <div class="preview-file-icon">
                          <i class="bi bi-file-earmark-check-fill"></i>
                        </div>
                        <div class="preview-file-details">
                          <div class="preview-file-name" id="identityFileName">identity.pdf</div>
                          <div class="preview-file-meta">
                            <i class="bi bi-check-circle-fill"></i>
                            <span id="identityFileSize">850 KB</span> • Verified
                          </div>
                        </div>
                      </div>
                      <button type="button" class="btn-remove-file" id="removeIdentityBtn" title="Remove file" aria-label="Remove identity document">
                        <i class="bi bi-x-lg"></i>
                      </button>
                    </div>

                    <div class="field-error-message text-danger mt-1" id="identityError" style="{{ $errors->has('identity_document') ? 'display:flex;' : 'display:none;' }}">
                      <i class="bi bi-exclamation-circle-fill"></i>
                      <span>{{ $errors->first('identity_document') ?: 'Please upload your CNIC or Form-B copy.' }}</span>
                    </div>
                  </div>
                </div>

                <!-- Pre-submission Summary Review Box -->
                <div class="card border-0 bg-light rounded-4 p-3 p-md-4 mb-4">
                  <h6 class="fw-bold text-dark mb-3"><i class="bi bi-card-checklist text-primary me-2"></i>Quick Application Summary</h6>
                  <div class="row g-2 small text-muted">
                    <div class="col-sm-6">
                      <strong>Target Program:</strong> <span id="summaryProgram" class="text-dark">Not Selected</span>
                    </div>
                    <div class="col-sm-6">
                      <strong>Applicant Name:</strong> <span id="summaryName" class="text-dark">—</span>
                    </div>
                    <div class="col-sm-6">
                      <strong>Contact Email:</strong> <span id="summaryEmail" class="text-dark">—</span>
                    </div>
                    <div class="col-sm-6">
                      <strong>City / District:</strong> <span id="summaryCity" class="text-dark">—</span>
                    </div>
                  </div>
                </div>

                <!-- Legal Declaration Checkbox -->
                <div class="application-confirm-box">
                  <div class="confirm-checkbox-wrap">
                    <input id="confirmation" name="confirmation" type="checkbox" class="form-check-input @error('confirmation') is-invalid @enderror" value="1" {{ old('confirmation') ? 'checked' : '' }} required>
                    <label for="confirmation">
                      I solemnly affirm and declare that all information, certificates, and credentials submitted in this application are authentic, accurate, and complete. I authorize <strong>{{ $collegeName }}</strong> to conduct verification with official examining boards and healthcare councils. <span class="text-danger">*</span>
                    </label>
                  </div>
                  <div class="field-error-message text-danger mt-2" id="confirmationError" style="{{ $errors->has('confirmation') ? 'display:flex;' : 'display:none;' }}">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span>{{ $errors->first('confirmation') ?: 'You must accept the legal declaration before submitting.' }}</span>
                  </div>
                </div>
              </div>

            </div>

            <!-- Wizard Bottom Action Navigation Bar -->
            <div class="wizard-footer">
              <div>
                <button type="button" class="btn wizard-btn-prev" id="prevBtn" style="display: none;" onclick="window.prevWizardStep && window.prevWizardStep(event)">
                  <i class="bi bi-arrow-left"></i> Previous Step
                </button>
              </div>

              <div class="d-flex align-items-center gap-3">
                <span class="small text-muted d-none d-sm-inline" id="stepCounterText">Step 1 of 4 — 25%</span>

                <button type="button" class="btn wizard-btn-next" id="nextBtn" onclick="window.nextWizardStep && window.nextWizardStep(event)">
                  Next Step <i class="bi bi-arrow-right"></i>
                </button>

                <button type="submit" class="btn wizard-btn-submit" id="submitBtn" style="display: none;">
                  <span class="spinner-border spinner-border-sm me-2 d-none" id="submitSpinner" role="status" aria-hidden="true"></span>
                  <span id="submitBtnText"><i class="bi bi-send-check-fill me-2"></i>Submit Application</span>
                </button>
              </div>
            </div>

          </form>
        </div>
      @endif
    </div>
  </section>

  <!-- Inline Direct Script Execution (Guaranteed to run in document) -->
  <script>
  (function () {
    var currentStep = 1;
    var totalSteps = 4;

    function getEl(id) {
      return document.getElementById(id);
    }

    // =========================================================================
    // Program Selection
    // =========================================================================
    window.selectWizardProgram = function (card) {
      if (!card) return;
      var programId = card.getAttribute('data-program-id');
      var programName = card.getAttribute('data-program-name') || '';

      var cards = document.querySelectorAll('.program-card-option');
      cards.forEach(function (c) {
        c.classList.remove('is-selected');
        c.setAttribute('aria-checked', 'false');
      });

      card.classList.add('is-selected');
      card.setAttribute('aria-checked', 'true');

      var input = getEl('selectedProgramId');
      if (input) input.value = programId;

      // Toggle fee breakdowns
      var feePanels = document.querySelectorAll('.program-fee-panel');
      feePanels.forEach(function (p) { p.style.display = 'none'; });
      var activeFee = getEl('fee-box-' + programId);
      if (activeFee) activeFee.style.display = 'block';

      // Clear error
      var programError = getEl('programIdError');
      if (programError) programError.style.display = 'none';

      // Update summary preview
      var summaryProgram = getEl('summaryProgram');
      if (summaryProgram) summaryProgram.textContent = programName;
    };

    // =========================================================================
    // Field Validation
    // =========================================================================
    function validateField(field) {
      if (!field) return true;
      var wrap = field.closest('.input-icon-wrap') || field.parentElement;
      var group = field.closest('.form-field-group');
      var val = (field.value || '').trim();
      var isValid = true;

      if (field.hasAttribute('required') && !val) {
        isValid = false;
      }

      if (isValid && field.type === 'email' && val) {
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(val)) isValid = false;
      }

      if (isValid && field.type === 'tel' && val) {
        if (val.length < 8) isValid = false;
      }

      if (isValid && field.id === 'graduation_year' && val) {
        var yr = parseInt(val, 10);
        var currentYear = new Date().getFullYear();
        if (isNaN(yr) || yr < 1950 || yr > currentYear + 1) isValid = false;
      }

      if (isValid && field.id === 'grade_percentage' && val) {
        var grade = parseFloat(val);
        if (isNaN(grade) || grade < 0 || grade > 100) isValid = false;
      }

      if (val !== '') {
        if (isValid) {
          field.classList.remove('is-invalid');
          field.classList.add('is-valid');
          if (wrap) {
            wrap.classList.remove('is-invalid');
            wrap.classList.add('is-valid');
          }
          if (group) group.classList.remove('is-invalid');
        } else {
          field.classList.remove('is-valid');
          field.classList.add('is-invalid');
          if (wrap) {
            wrap.classList.remove('is-valid');
            wrap.classList.add('is-invalid');
          }
          if (group) group.classList.add('is-invalid');
        }
      } else {
        if (field.hasAttribute('required') && field.dataset.touched === 'true') {
          field.classList.add('is-invalid');
          if (wrap) wrap.classList.add('is-invalid');
          if (group) group.classList.add('is-invalid');
        } else {
          field.classList.remove('is-valid', 'is-invalid');
          if (wrap) wrap.classList.remove('is-valid', 'is-invalid');
          if (group) group.classList.remove('is-invalid');
        }
      }

      return isValid;
    }

    // =========================================================================
    // Step Validation
    // =========================================================================
    function validateCurrentStep(step) {
      if (step === 1) {
        var input = getEl('selectedProgramId');
        var val = input ? (input.value || '').trim() : '';
        if (!val) {
          var programError = getEl('programIdError');
          if (programError) programError.style.display = 'flex';
          var container = getEl('programCardsContainer');
          if (container) container.scrollIntoView({ behavior: 'smooth', block: 'center' });
          return false;
        }
        return true;
      }

      if (step === 2) {
        var step2Fields = document.querySelectorAll('#stepPane2 [required]');
        var valid2 = true;
        var firstInvalid2 = null;
        step2Fields.forEach(function (f) {
          f.dataset.touched = 'true';
          if (!validateField(f)) {
            valid2 = false;
            if (!firstInvalid2) firstInvalid2 = f;
          }
        });
        if (firstInvalid2) {
          firstInvalid2.focus();
          firstInvalid2.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return valid2;
      }

      if (step === 3) {
        var step3Fields = document.querySelectorAll('#stepPane3 [required]');
        var valid3 = true;
        var firstInvalid3 = null;
        step3Fields.forEach(function (f) {
          f.dataset.touched = 'true';
          if (!validateField(f)) {
            valid3 = false;
            if (!firstInvalid3) firstInvalid3 = f;
          }
        });
        if (firstInvalid3) {
          firstInvalid3.focus();
          firstInvalid3.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return valid3;
      }

      if (step === 4) {
        var transcriptInput = getEl('transcript');
        var identityInput = getEl('identity_document');
        var confirmationInput = getEl('confirmation');
        var valid4 = true;

        if (!transcriptInput || !transcriptInput.files || transcriptInput.files.length === 0) {
          var tErr = getEl('transcriptError');
          if (tErr) tErr.style.display = 'flex';
          valid4 = false;
        }
        if (!identityInput || !identityInput.files || identityInput.files.length === 0) {
          var iErr = getEl('identityError');
          if (iErr) iErr.style.display = 'flex';
          valid4 = false;
        }
        if (!confirmationInput || !confirmationInput.checked) {
          var cErr = getEl('confirmationError');
          if (cErr) cErr.style.display = 'flex';
          valid4 = false;
        }
        return valid4;
      }

      return true;
    }

    // =========================================================================
    // Summary Update
    // =========================================================================
    function updateSummary() {
      var firstName = (getEl('first_name')?.value || '').trim();
      var lastName = (getEl('last_name')?.value || '').trim();
      var email = (getEl('email')?.value || '').trim();
      var city = (getEl('city')?.value || '').trim();

      if (getEl('summaryName')) {
        getEl('summaryName').textContent = (firstName || lastName) ? (firstName + ' ' + lastName) : '—';
      }
      if (getEl('summaryEmail')) {
        getEl('summaryEmail').textContent = email || '—';
      }
      if (getEl('summaryCity')) {
        getEl('summaryCity').textContent = city || '—';
      }
    }

    // =========================================================================
    // Go To Step
    // =========================================================================
    window.goToWizardStep = function (step) {
      if (step < 1 || step > totalSteps) return;

      for (var i = 1; i <= totalSteps; i++) {
        var pane = getEl('stepPane' + i);
        var tab = getEl('stepTab' + i);
        var badge = getEl('stepBadge' + i);

        if (pane) {
          if (i === step) {
            pane.classList.add('is-active');
            pane.style.display = 'block';
          } else {
            pane.classList.remove('is-active');
            pane.style.display = 'none';
          }
        }

        if (tab) {
          tab.classList.remove('is-active', 'is-completed');
          tab.setAttribute('aria-selected', i === step ? 'true' : 'false');

          if (i < step) {
            tab.classList.add('is-completed');
            if (badge) badge.innerHTML = '<i class="bi bi-check-lg"></i>';
          } else if (i === step) {
            tab.classList.add('is-active');
            if (badge) badge.textContent = i;
          } else {
            if (badge) badge.textContent = i;
          }
        }
      }

      var progressBar = getEl('wizardProgressBar');
      var stepCounterText = getEl('stepCounterText');
      var pct = Math.round((step / totalSteps) * 100);
      if (progressBar) progressBar.style.width = pct + '%';
      if (stepCounterText) stepCounterText.textContent = 'Step ' + step + ' of ' + totalSteps + ' — ' + pct + '%';

      var prevBtn = getEl('prevBtn');
      var nextBtn = getEl('nextBtn');
      var submitBtn = getEl('submitBtn');

      if (step === 1) {
        if (prevBtn) prevBtn.style.display = 'none';
        if (nextBtn) nextBtn.style.display = 'inline-flex';
        if (submitBtn) submitBtn.style.display = 'none';
      } else if (step === totalSteps) {
        if (prevBtn) prevBtn.style.display = 'inline-flex';
        if (nextBtn) nextBtn.style.display = 'none';
        if (submitBtn) submitBtn.style.display = 'inline-flex';
        updateSummary();
      } else {
        if (prevBtn) prevBtn.style.display = 'inline-flex';
        if (nextBtn) nextBtn.style.display = 'inline-flex';
        if (submitBtn) submitBtn.style.display = 'none';
      }

      currentStep = step;

      var wizardCard = getEl('applicationWizard');
      if (wizardCard && wizardCard.getBoundingClientRect().top < 0) {
        wizardCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    };

    window.nextWizardStep = function (e) {
      if (e && e.preventDefault) e.preventDefault();
      if (validateCurrentStep(currentStep)) {
        window.goToWizardStep(currentStep + 1);
      }
    };

    window.prevWizardStep = function (e) {
      if (e && e.preventDefault) e.preventDefault();
      window.goToWizardStep(currentStep - 1);
    };

    // =========================================================================
    // Initialization Engine
    // =========================================================================
    function initWizard() {
      var wizardForm = getEl('wizardForm');
      if (!wizardForm) return;

      // Pre-select if value exists
      var input = getEl('selectedProgramId');
      if (input && input.value) {
        var card = document.querySelector('.program-card-option[data-program-id="' + input.value + '"]');
        if (card) window.selectWizardProgram(card);
      }

      // Live Statement Counter
      var statement = getEl('statement');
      var charCountEl = getEl('statementCharCount');
      var charMeterEl = getEl('statementCharMeter');
      if (statement && charCountEl && charMeterEl) {
        function updateCount() {
          var len = statement.value.length;
          charCountEl.textContent = len.toLocaleString();
          var pct = Math.min((len / 3000) * 100, 100);
          charMeterEl.style.width = pct + '%';
          charMeterEl.classList.remove('is-warning', 'is-danger');
          if (pct >= 90) charMeterEl.classList.add('is-danger');
          else if (pct >= 70) charMeterEl.classList.add('is-warning');
        }
        statement.addEventListener('input', updateCount);
        updateCount();
      }

      // Input field real-time validation listeners
      var formInputs = wizardForm.querySelectorAll('.form-control-custom, .form-select-custom');
      formInputs.forEach(function (inp) {
        inp.addEventListener('input', function () {
          inp.dataset.touched = 'true';
          validateField(inp);
        });
        inp.addEventListener('blur', function () {
          inp.dataset.touched = 'true';
          validateField(inp);
        });
      });

      // Dropzone Handlers
      function setupDropzone(zoneId, inputId, previewId, nameId, sizeId, removeBtnId, errorId) {
        var zone = getEl(zoneId);
        var fileInp = getEl(inputId);
        var preview = getEl(previewId);
        var nameEl = getEl(nameId);
        var sizeEl = getEl(sizeId);
        var removeBtn = getEl(removeBtnId);
        var errorEl = getEl(errorId);

        if (!zone || !fileInp) return;

        function handleFile(file) {
          if (!file) return;
          var validTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
          var isPdf = file.name.toLowerCase().endsWith('.pdf');
          var isImg = /\.(jpg|jpeg|png)$/i.test(file.name);

          if (!isPdf && !isImg && !validTypes.includes(file.type)) {
            if (errorEl) {
              errorEl.querySelector('span').textContent = 'Invalid format. Only PDF, JPG, and PNG are allowed.';
              errorEl.style.display = 'flex';
            }
            fileInp.value = '';
            return;
          }

          if (file.size > 5 * 1024 * 1024) {
            if (errorEl) {
              errorEl.querySelector('span').textContent = 'File exceeds maximum limit of 5 MB.';
              errorEl.style.display = 'flex';
            }
            fileInp.value = '';
            return;
          }

          if (errorEl) errorEl.style.display = 'none';
          var formattedSize = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
          if (file.size < 1024 * 1024) {
            formattedSize = Math.round(file.size / 1024) + ' KB';
          }

          if (nameEl) nameEl.textContent = file.name;
          if (sizeEl) sizeEl.textContent = formattedSize;
          zone.style.display = 'none';
          if (preview) preview.classList.add('is-active');
        }

        zone.addEventListener('click', function () { fileInp.click(); });
        zone.addEventListener('dragover', function (e) { e.preventDefault(); zone.classList.add('is-dragover'); });
        zone.addEventListener('dragleave', function () { zone.classList.remove('is-dragover'); });
        zone.addEventListener('drop', function (e) {
          e.preventDefault();
          zone.classList.remove('is-dragover');
          if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            fileInp.files = e.dataTransfer.files;
            handleFile(e.dataTransfer.files[0]);
          }
        });

        fileInp.addEventListener('change', function () {
          if (fileInp.files && fileInp.files.length > 0) {
            handleFile(fileInp.files[0]);
          }
        });

        if (removeBtn) {
          removeBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            fileInp.value = '';
            if (preview) preview.classList.remove('is-active');
            zone.style.display = 'flex';
          });
        }
      }

      setupDropzone('dropzoneTranscript', 'transcript', 'previewTranscript', 'transcriptFileName', 'transcriptFileSize', 'removeTranscriptBtn', 'transcriptError');
      setupDropzone('dropzoneIdentity', 'identity_document', 'previewIdentity', 'identityFileName', 'identityFileSize', 'removeIdentityBtn', 'identityError');

      // Form Submit Spinner
      wizardForm.addEventListener('submit', function (e) {
        if (!validateCurrentStep(4)) {
          e.preventDefault();
          var invalidConfirm = getEl('confirmation');
          if (invalidConfirm && !invalidConfirm.checked) {
            invalidConfirm.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
          return false;
        }

        var submitBtn = getEl('submitBtn');
        var submitSpinner = getEl('submitSpinner');
        var submitBtnText = getEl('submitBtnText');
        if (submitBtn) submitBtn.disabled = true;
        if (submitSpinner) submitSpinner.classList.remove('d-none');
        if (submitBtnText) submitBtnText.innerHTML = 'Submitting Application…';
        return true;
      });

      // Jump to step if server validation returned errors
      var serverErrors = @json($errors->keys());
      if (serverErrors && serverErrors.length > 0) {
        if (serverErrors.includes('program_id')) {
          currentStep = 1;
        } else if (serverErrors.some(function (k) { return ['first_name', 'last_name', 'date_of_birth', 'gender', 'email', 'phone', 'national_id', 'city', 'country', 'address'].includes(k); })) {
          currentStep = 2;
        } else if (serverErrors.some(function (k) { return ['highest_qualification', 'institution', 'graduation_year', 'grade_percentage', 'statement'].includes(k); })) {
          currentStep = 3;
        } else if (serverErrors.some(function (k) { return ['transcript', 'identity_document', 'confirmation'].includes(k); })) {
          currentStep = 4;
        }
      }

      window.goToWizardStep(currentStep);
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initWizard);
    } else {
      initWizard();
    }
  })();
  </script>
</main>
@endsection
