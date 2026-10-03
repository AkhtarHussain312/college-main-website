@extends('layouts.public')
@php($active = 'apply')

@section('content')
<main>
  <section class="apply-hero" style="background-image:linear-gradient(90deg,rgba(5,22,37,.96) 0%,rgba(5,22,37,.78) 50%,rgba(5,22,37,.25) 100%),url('{{ $site['hero_image'] ?? asset('images/nursing-hero.png') }}')">
    <div class="container">
      <span class="eyebrow text-info">ONLINE ADMISSIONS</span>
      <h1>Apply to {{ $collegeName }}</h1>
      <p>Complete the application below. Your information and documents are securely submitted to the {{ $collegeName }} admissions team.</p>
    </div>
  </section>

  <section class="section application-bg">
    <div class="container">
      @if(session('success_reference'))
        <div class="application-success">
          <i class="bi bi-check-circle-fill text-success" style="font-size: 3.5rem;"></i>
          <h2 class="mt-3">Application Submitted Successfully</h2>
          <p class="text-muted">Your application to {{ $collegeName }} has been received. Please save your reference number for future communication.</p>
          <div class="alert alert-info py-3 px-4 d-inline-block my-3">
            <span class="d-block small text-muted">Your Reference Number:</span>
            <strong class="fs-4">{{ session('success_reference') }}</strong>
          </div>
          <div class="mt-4">
            <a href="{{ url('/') }}" class="btn btn-primary"><i class="bi bi-arrow-left"></i> Return to Home</a>
          </div>
        </div>
      @else
        <form class="online-application" method="POST" action="{{ url('/apply') }}" enctype="multipart/form-data" novalidate>
          @csrf

          @if($errors->any())
            <div class="alert alert-danger mb-4">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>
              <strong>Please review the highlighted fields below to submit your application.</strong>
            </div>
          @endif

          <div class="application-intro">
            <div>
              <h2>{{ $collegeName }} Online Application</h2>
              <p>Fields marked with * are required.</p>
            </div>
            <span><i class="bi bi-shield-check"></i> Secure submission</span>
          </div>

          <!-- 1. Program Selection -->
          <fieldset>
            <legend><b>1</b> Program Selection</legend>
            <div class="row">
              <div class="col-lg-8">
                <label for="program_id">Program *</label>
                <select id="program_id" name="program_id" class="form-select @error('program_id') is-invalid @enderror" required>
                  <option value="" disabled {{ old('program_id') ? '' : 'selected' }}>Select the program you want to apply for</option>
                  @foreach($programs as $p)
                    <option value="{{ $p->id }}" {{ old('program_id') == $p->id ? 'selected' : '' }}>
                      {{ $p->name }} — {{ $p->duration }}
                    </option>
                  @endforeach
                </select>
                @error('program_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <!-- Dynamic Fee Breakdown Boxes -->
                @foreach($programs as $p)
                  @if($p->fees->isNotEmpty())
                    <div id="fee-box-{{ $p->id }}" class="program-fee-panel fee-breakdown mt-3" style="display: {{ old('program_id') == $p->id ? 'block' : 'none' }};">
                      <div class="fee-heading">
                        <span><i class="bi bi-cash-stack"></i> Fee Structure — {{ $p->name }}</span>
                        <small>{{ $p->fees->count() }} {{ $p->fees->count() === 1 ? 'item' : 'items' }}</small>
                      </div>
                      <div class="fee-rows">
                        @foreach($p->fees as $fee)
                          <div>
                            <span>{{ $fee->fee_type }} <small>({{ $fee->billing_basis }})</small></span>
                            <b>{{ $fee->currency ?: 'PKR' }} {{ number_format($fee->amount) }}</b>
                          </div>
                        @endforeach
                      </div>
                      @if($p->fees->whereNotNull('notes')->first())
                        <p>{{ $p->fees->whereNotNull('notes')->first()->notes }}</p>
                      @endif
                    </div>
                  @endif
                @endforeach
              </div>
            </div>
          </fieldset>

          <!-- 2. Personal Information -->
          <fieldset>
            <legend><b>2</b> Personal Information</legend>
            <div class="row g-3">
              <div class="col-md-6">
                <label for="first_name">First Name *</label>
                <input id="first_name" name="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required>
                @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-6">
                <label for="last_name">Last Name *</label>
                <input id="last_name" name="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required>
                @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-6">
                <label for="date_of_birth">Date of Birth *</label>
                <input id="date_of_birth" name="date_of_birth" type="date" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth') }}" required>
                @error('date_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-6">
                <label for="gender">Gender *</label>
                <select id="gender" name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                  <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select Gender</option>
                  <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                  <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                  <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                  <option value="prefer_not_to_say" {{ old('gender') === 'prefer_not_to_say' ? 'selected' : '' }}>Prefer not to say</option>
                </select>
                @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-6">
                <label for="email">Email Address *</label>
                <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-6">
                <label for="phone">Phone Number *</label>
                <input id="phone" name="phone" type="tel" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="+92 300 0000000" required>
                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-6">
                <label for="national_id">National ID / Passport</label>
                <input id="national_id" name="national_id" type="text" class="form-control @error('national_id') is-invalid @enderror" value="{{ old('national_id') }}" placeholder="CNIC or Passport Number">
                @error('national_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-6">
                <label for="city">City *</label>
                <input id="city" name="city" type="text" class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}" required>
                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-6">
                <label for="country">Country *</label>
                <input id="country" name="country" type="text" class="form-control @error('country') is-invalid @enderror" value="{{ old('country', 'Pakistan') }}" required>
                @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-12">
                <label for="address">Full Residential Address *</label>
                <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror" rows="3" required>{{ old('address') }}</textarea>
                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>
          </fieldset>

          <!-- 3. Education -->
          <fieldset>
            <legend><b>3</b> Educational Background</legend>
            <div class="row g-3">
              <div class="col-md-6">
                <label for="highest_qualification">Highest Qualification *</label>
                <input id="highest_qualification" name="highest_qualification" type="text" class="form-control @error('highest_qualification') is-invalid @enderror" value="{{ old('highest_qualification') }}" placeholder="e.g. Matric / FSc / Intermediate" required>
                @error('highest_qualification') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-6">
                <label for="institution">Institution / School / Board *</label>
                <input id="institution" name="institution" type="text" class="form-control @error('institution') is-invalid @enderror" value="{{ old('institution') }}" placeholder="Name of school or college attended" required>
                @error('institution') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-6">
                <label for="graduation_year">Graduation Year *</label>
                <input id="graduation_year" name="graduation_year" type="number" min="1950" max="{{ date('Y') + 1 }}" class="form-control @error('graduation_year') is-invalid @enderror" value="{{ old('graduation_year', date('Y')) }}" required>
                @error('graduation_year') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-6">
                <label for="grade_percentage">Grade / Percentage</label>
                <input id="grade_percentage" name="grade_percentage" type="number" step="0.01" min="0" max="100" class="form-control @error('grade_percentage') is-invalid @enderror" value="{{ old('grade_percentage') }}" placeholder="e.g. 78.5">
                @error('grade_percentage') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-12">
                <label for="statement">Personal Statement</label>
                <textarea id="statement" name="statement" class="form-control @error('statement') is-invalid @enderror" rows="4" placeholder="Briefly describe why you wish to pursue your studies at {{ $collegeName }} (maximum 3,000 characters).">{{ old('statement') }}</textarea>
                @error('statement') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>
          </fieldset>

          <!-- 4. Supporting Documents -->
          <fieldset>
            <legend><b>4</b> Supporting Documents</legend>
            <p class="upload-help">Upload PDF, JPG, JPEG or PNG files. Maximum 5 MB per document.</p>
            <div class="row g-3">
              <div class="col-md-6">
                <label for="transcript">Academic Transcript / Certificate *</label>
                <input id="transcript" name="transcript" type="file" accept=".pdf,.jpg,.jpeg,.png" class="form-control @error('transcript') is-invalid @enderror" required>
                @error('transcript') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-6">
                <label for="identity_document">National ID / B-Form / Passport *</label>
                <input id="identity_document" name="identity_document" type="file" accept=".pdf,.jpg,.jpeg,.png" class="form-control @error('identity_document') is-invalid @enderror" required>
                @error('identity_document') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>
          </fieldset>

          <!-- 5. Declaration & Submission -->
          <div class="application-confirm">
            <div class="form-check">
              <input id="confirmation" name="confirmation" type="checkbox" class="form-check-input @error('confirmation') is-invalid @enderror" value="1" {{ old('confirmation') ? 'checked' : '' }} required>
              <label for="confirmation" class="form-check-label">
                I declare that all information provided in this application is accurate and complete. I authorize {{ $collegeName }} to verify my academic records. *
              </label>
              @error('confirmation') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
          </div>

          <div class="mt-4">
            <button type="submit" class="btn btn-primary btn-lg">
              Submit Application <i class="bi bi-arrow-right ms-2"></i>
            </button>
          </div>
        </form>
      @endif
    </div>
  </section>
</main>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var programSelect = document.getElementById('program_id');
    if (!programSelect) return;

    function updateFeeDisplay() {
      var selectedId = programSelect.value;
      var feePanels = document.querySelectorAll('.program-fee-panel');
      feePanels.forEach(function (panel) {
        panel.style.display = 'none';
      });
      if (selectedId) {
        var activePanel = document.getElementById('fee-box-' + selectedId);
        if (activePanel) {
          activePanel.style.display = 'block';
        }
      }
    }

    programSelect.addEventListener('change', updateFeeDisplay);
    updateFeeDisplay();
  });
</script>
@endpush
