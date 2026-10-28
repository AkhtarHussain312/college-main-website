@extends('layouts.public')
@php
  $active = 'admissions';
@endphp
@section('content')
<main class="page-content-wrapper">
  <!-- In-Page Header -->
  <div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb custom-breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door me-1"></i>Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Admissions</li>
      </ol>
    </nav>
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
      <div>
        <span class="eyebrow">ADMISSIONS & ENROLLMENT</span>
        <h1 class="page-title mb-1">Join Our Healthcare Community</h1>
        <p class="text-muted mb-0">Transparent admissions process, program eligibility criteria, and fee structures for accredited courses.</p>
      </div>
      <div class="d-flex gap-2 flex-wrap">
        <a href="{{ url('/apply') }}" class="btn btn-primary btn-sm"><i class="bi bi-arrow-right me-1"></i> Apply Online Now</a>
        <a href="{{ url('/contact') }}" class="btn btn-outline-secondary btn-sm">Speak With Advisor</a>
      </div>
    </div>
  </div>

  <!-- Status Announcement Bar -->
  <section class="py-2 bg-light border-bottom">
    <div class="container">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
        <div class="d-flex align-items-center gap-2">
          <span class="badge bg-primary px-3 py-1.5"><i class="bi bi-megaphone-fill"></i> INTAKE STATUS</span>
          <strong class="text-dark">{{ $site['admissions_banner'] ?? 'Admissions Open for Fall 2026 - Apply Today' }}</strong>
        </div>
        <div class="d-flex gap-3 text-muted small">
          <span><i class="bi bi-shield-check text-success"></i> 100% Online Verification</span>
          <span><i class="bi bi-award text-primary"></i> Merit Scholarships Available</span>
        </div>
      </div>
    </div>
  </section>

  <!-- Step-by-Step Admissions Process -->
  <section class="section section-compact">
    <div class="container">
      <div class="text-center mb-4">
        <span class="eyebrow">STEP-BY-STEP ROADMAP</span>
        <h2>How to Apply for Admission</h2>
        <p class="text-muted mx-auto" style="max-width: 620px;">Our streamlined application process makes it simple and straightforward to submit your credentials and track your enrollment status.</p>
      </div>

      <div class="row g-4">
        @foreach($steps as $index => $step)
          @php
            $title = is_array($step) ? $step['title'] : $step->title;
            $desc = is_array($step) ? $step['desc'] : ($step->description ?? 'Follow the step instructions to proceed with your admission registration.');
            $icon = is_array($step) ? $step['icon'] : ($step->icon ?? 'bi-check-circle');
          @endphp
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-4 position-relative">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-3" style="width: 48px; height: 48px; font-size: 22px;">
                  <i class="bi {{ $icon }}"></i>
                </div>
                <span class="fw-bold text-muted fs-5">0{{ $index + 1 }}</span>
              </div>
              <h5 class="fw-bold text-dark mb-2">{{ $title }}</h5>
              <p class="text-muted small mb-0">{{ $desc }}</p>
            </div>
          </div>
        @endforeach
      </div>

      <div class="text-center mt-5">
        <a href="{{ url('/apply') }}" class="btn btn-primary btn-lg px-4">Start Your Application <i class="bi bi-arrow-right"></i></a>
      </div>
    </div>
  </section>

  <!-- Eligibility Criteria Matrix -->
  <section class="section section-soft">
    <div class="container">
      <div class="text-center mb-5">
        <span class="eyebrow">ACADEMIC CRITERIA</span>
        <h2>Eligibility Requirements</h2>
        <p class="text-muted mx-auto" style="max-width: 620px;">Review the minimum educational qualifications required for our undergraduate degrees and professional nursing diplomas.</p>
      </div>

      <div class="row g-4">
        <div class="col-lg-4 col-md-6">
          <div class="card h-100 border-0 shadow-sm rounded-4 p-4 bg-white">
            <div class="badge bg-info bg-opacity-10 text-info text-uppercase fw-bold align-self-start mb-3 px-3 py-2">4-Year Degree</div>
            <h4 class="fw-bold text-dark">BS Nursing (Generic)</h4>
            <p class="text-muted small">Comprehensive degree program preparing students for registered nursing licensure and hospital clinical practice.</p>
            <hr class="my-3 opacity-25">
            <h6 class="fw-bold text-dark small text-uppercase mb-2">Requirements:</h6>
            <ul class="list-unstyled small text-muted d-flex flex-column gap-2 mb-0">
              <li><i class="bi bi-check-circle-fill text-primary me-2"></i><strong>FSc Pre-Medical</strong> with minimum 50% marks (Physics, Chemistry, Biology).</li>
              <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Age limit: 14 to 35 years at time of application.</li>
              <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Open to both male and female candidates.</li>
              <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Passing the college admission test / interview.</li>
            </ul>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="card h-100 border-0 shadow-sm rounded-4 p-4 bg-white">
            <div class="badge bg-primary bg-opacity-10 text-primary text-uppercase fw-bold align-self-start mb-3 px-3 py-2">2-Year Degree</div>
            <h4 class="fw-bold text-dark">Post RN BSN</h4>
            <p class="text-muted small">Specialized post-basic degree enabling practicing registered nurses to advance into clinical leadership and education.</p>
            <hr class="my-3 opacity-25">
            <h6 class="fw-bold text-dark small text-uppercase mb-2">Requirements:</h6>
            <ul class="list-unstyled small text-muted d-flex flex-column gap-2 mb-0">
              <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Diploma in General Nursing (3 Years) with minimum 50% marks.</li>
              <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Diploma in Midwifery (1 Year for females) or specialized post-basic.</li>
              <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Valid registration with the Nursing Council (PNC).</li>
              <li><i class="bi bi-check-circle-fill text-primary me-2"></i>At least 1 year of post-qualification clinical experience.</li>
            </ul>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="card h-100 border-0 shadow-sm rounded-4 p-4 bg-white">
            <div class="badge bg-success bg-opacity-10 text-success text-uppercase fw-bold align-self-start mb-3 px-3 py-2">Allied Health & Diplomas</div>
            <h4 class="fw-bold text-dark">Nursing & Health Diplomas</h4>
            <p class="text-muted small">Focused foundational training designed for rapid entry into vital medical care, laboratory, and hospital technical roles.</p>
            <hr class="my-3 opacity-25">
            <h6 class="fw-bold text-dark small text-uppercase mb-2">Requirements:</h6>
            <ul class="list-unstyled small text-muted d-flex flex-column gap-2 mb-0">
              <li><i class="bi bi-check-circle-fill text-primary me-2"></i><strong>Matriculation with Science</strong> (Biology) with minimum 45% marks.</li>
              <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Or FSc Pre-Medical for advanced technician diplomas.</li>
              <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Character certificate from the institution last attended.</li>
              <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Physical fitness certified by a registered medical practitioner.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Required Documents Checklist -->
  <section class="section">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-6">
          <span class="eyebrow">DOCUMENTATION CHECKLIST</span>
          <h2>Required Documents for Submission</h2>
          <p class="text-muted">Ensure you have scanned digital copies ready before starting your online application. Original documents must be presented during the in-person interview verification.</p>
          
          <div class="d-flex flex-column gap-3 mt-4">
            <div class="d-flex align-items-start gap-3 p-3 bg-light rounded-3">
              <i class="bi bi-file-earmark-pdf-fill text-primary fs-3"></i>
              <div>
                <strong class="d-block text-dark">Matric / SSC Certificate & DMC</strong>
                <small class="text-muted">Detailed Marks Certificate verified by the relevant Board of Intermediate and Secondary Education.</small>
              </div>
            </div>

            <div class="d-flex align-items-start gap-3 p-3 bg-light rounded-3">
              <i class="bi bi-file-earmark-pdf-fill text-primary fs-3"></i>
              <div>
                <strong class="d-block text-dark">FSc / HSSC Certificate & DMC</strong>
                <small class="text-muted">Pre-Medical transcript or equivalent qualification certificate (Equivalence certificate from IBCC if applicable).</small>
              </div>
            </div>

            <div class="d-flex align-items-start gap-3 p-3 bg-light rounded-3">
              <i class="bi bi-person-vcard-fill text-primary fs-3"></i>
              <div>
                <strong class="d-block text-dark">CNIC or B-Form</strong>
                <small class="text-muted">National Identity Card or Computerized B-Form of applicant and Guardian's CNIC copy.</small>
              </div>
            </div>

            <div class="d-flex align-items-start gap-3 p-3 bg-light rounded-3">
              <i class="bi bi-images text-primary fs-3"></i>
              <div>
                <strong class="d-block text-dark">Passport-Size Photographs & Domicile</strong>
                <small class="text-muted">Recent passport-sized photographs with white/blue background and verified District Domicile Certificate.</small>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5 bg-navy text-white" style="background: linear-gradient(135deg, #071f33 0%, #0d3859 100%);">
            <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-10 text-info rounded-circle mb-4" style="width: 60px; height: 60px; font-size: 28px;">
              <i class="bi bi-award-fill"></i>
            </div>
            <h3 class="fw-bold mb-3">Scholarships & Financial Aid</h3>
            <p class="text-light opacity-75">We believe that dedicated students with a passion for healthcare should not face financial barriers to high-quality education.</p>
            
            <ul class="list-unstyled text-light opacity-90 d-flex flex-column gap-3 my-4">
              <li class="d-flex align-items-center gap-2">
                <i class="bi bi-check2-circle text-info fs-5"></i>
                <span><strong>Merit Scholarships:</strong> Awarded to top scorers in FSc and entrance assessments.</span>
              </li>
              <li class="d-flex align-items-center gap-2">
                <i class="bi bi-check2-circle text-info fs-5"></i>
                <span><strong>Need-Based Financial Assistance:</strong> Tuition concessions for deserving students.</span>
              </li>
              <li class="d-flex align-items-center gap-2">
                <i class="bi bi-check2-circle text-info fs-5"></i>
                <span><strong>Installment Options:</strong> Flexible semester installment plans available upon request.</span>
              </li>
            </ul>

            <div class="pt-3 border-top border-white border-opacity-10 d-flex flex-wrap gap-3">
              <a href="{{ url('/programs') }}" class="btn btn-primary">View Fee Breakdown</a>
              <a href="{{ url('/contact') }}" class="btn btn-outline-light">Contact Accounts</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Admissions FAQ Accordion -->
  <section class="section section-soft">
    <div class="container">
      <div class="text-center mb-5">
        <span class="eyebrow">COMMON QUESTIONS</span>
        <h2>Frequently Asked Questions</h2>
        <p class="text-muted mx-auto" style="max-width: 600px;">Find answers to common questions regarding admissions policies, deadlines, and requirements.</p>
      </div>

      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="accordion" id="admissionsFaq">
            <div class="accordion-item border-0 mb-3 rounded-3 shadow-sm overflow-hidden">
              <h2 class="accordion-header" id="faqOne">
                <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                  How can I apply for admission?
                </button>
              </h2>
              <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="faqOne" data-bs-parent="#admissionsFaq">
                <div class="accordion-body text-muted">
                  You can apply directly through our <strong><a href="{{ url('/apply') }}" class="text-primary">Online Application Portal</a></strong> by filling out the online form and uploading scanned copies of your educational transcripts, CNIC/B-Form, and passport-size photo. You can also visit our campus admissions office in person during working hours.
                </div>
              </div>
            </div>

            <div class="accordion-item border-0 mb-3 rounded-3 shadow-sm overflow-hidden">
              <h2 class="accordion-header" id="faqTwo">
                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                  What is the fee payment structure? Can I pay in installments?
                </button>
              </h2>
              <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="faqTwo" data-bs-parent="#admissionsFaq">
                <div class="accordion-body text-muted">
                  Yes, tuition fees can be paid in structured semester installments. Detailed program-by-program fee breakdowns can be reviewed on our <a href="{{ url('/programs') }}" class="text-primary">Programs page</a> or requested directly from our accounts desk.
                </div>
              </div>
            </div>

            <div class="accordion-item border-0 mb-3 rounded-3 shadow-sm overflow-hidden">
              <h2 class="accordion-header" id="faqThree">
                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                  Are hostel and transport facilities provided?
                </button>
              </h2>
              <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="faqThree" data-bs-parent="#admissionsFaq">
                <div class="accordion-body text-muted">
                  Yes. Dedicated, secure hostel accommodations are available for outstation male and female nursing students, with 24/7 security, dining facilities, and reliable transport routes connecting the campus to affiliated clinical hospitals.
                </div>
              </div>
            </div>

            <div class="accordion-item border-0 mb-3 rounded-3 shadow-sm overflow-hidden">
              <h2 class="accordion-header" id="faqFour">
                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                  Are the programs accredited and recognized?
                </button>
              </h2>
              <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="faqFour" data-bs-parent="#admissionsFaq">
                <div class="accordion-body text-muted">
                  All nursing and healthcare programs offered at {{ $collegeName }} are fully recognized and accredited by the statutory healthcare regulatory authorities and affiliated with recognized universities and nursing examination boards.
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Help & Contact CTA Section -->
  <section class="py-5 bg-navy text-white text-center" style="background: #071f33;">
    <div class="container py-4">
      <span class="eyebrow text-info">READY TO BEGIN?</span>
      <h2 class="fw-bold mb-3">Take the First Step Toward Your Healthcare Future</h2>
      <p class="text-light opacity-75 mx-auto mb-4" style="max-width: 580px;">Our admissions counselors are available Monday through Friday to guide you through enrollment, eligibility checks, and career opportunities.</p>
      <div class="d-flex justify-content-center gap-3 flex-wrap">
        <a href="{{ url('/apply') }}" class="btn btn-primary btn-lg px-4">Submit Application Now</a>
        @if(!empty($site['admissions_phone']))
          <a href="tel:{{ $site['admissions_phone'] }}" class="btn btn-outline-light btn-lg px-4"><i class="bi bi-telephone-fill"></i> {{ $site['admissions_phone'] }}</a>
        @else
          <a href="{{ url('/contact') }}" class="btn btn-outline-light btn-lg px-4">Contact Admissions Office</a>
        @endif
      </div>
    </div>
  </section>
</main>
@endsection
