@extends('layouts.public')
@php($active = 'campus-life')
@section('content')
<main>
  <!-- Key Campus Highlights Overview -->
  <section class="section section-compact pt-4 pt-md-5">
    <div class="container">
      <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb custom-breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door me-1"></i>Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Campus Life</li>
        </ol>
      </nav>
      <div class="text-center mb-5">
        <span class="eyebrow">EXPERIENCE & CULTURE</span>
        <h1 class="page-title mb-2">Life Beyond the Classroom</h1>
        <p class="text-muted mx-auto" style="max-width: 620px;">At {{ $collegeName }}, we believe professional healthcare training happens through hands-on practice, peer collaboration, and guided patient care.</p>
      </div>

      <div class="row g-4">
        <div class="col-md-6 col-lg-3">
          <div class="card h-100 border-0 shadow-sm rounded-4 p-4 text-center bg-white">
            <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mx-auto mb-3" style="width: 58px; height: 58px; font-size: 26px;">
              <i class="bi bi-hospital"></i>
            </div>
            <h5 class="fw-bold text-dark">Hospital Clinical Rotations</h5>
            <p class="text-muted small mb-0">Supervised bedside clinical practice in accredited affiliated teaching hospitals under experienced instructors.</p>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="card h-100 border-0 shadow-sm rounded-4 p-4 text-center bg-white">
            <div class="d-inline-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info rounded-circle mx-auto mb-3" style="width: 58px; height: 58px; font-size: 26px;">
              <i class="bi bi-heart-pulse"></i>
            </div>
            <h5 class="fw-bold text-dark">High-Fidelity Simulation</h5>
            <p class="text-muted small mb-0">Advanced medical simulation labs allowing students to master emergency procedures and patient care in realistic environments.</p>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="card h-100 border-0 shadow-sm rounded-4 p-4 text-center bg-white">
            <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle mx-auto mb-3" style="width: 58px; height: 58px; font-size: 26px;">
              <i class="bi bi-book-half"></i>
            </div>
            <h5 class="fw-bold text-dark">Digital Library & Research</h5>
            <p class="text-muted small mb-0">Extensive collections of medical literature, international nursing journals, high-speed computer terminals, and quiet study rooms.</p>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="card h-100 border-0 shadow-sm rounded-4 p-4 text-center bg-white">
            <div class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning rounded-circle mx-auto mb-3" style="width: 58px; height: 58px; font-size: 26px;">
              <i class="bi bi-shield-check"></i>
            </div>
            <h5 class="fw-bold text-dark">Secure Student Hostels</h5>
            <p class="text-muted small mb-0">Dedicated hostel accommodations with round-the-clock security, meal plans, study halls, and transport services.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Detailed Clinical Facilities -->
  <section class="section section-soft">
    <div class="container">
      <div class="row align-items-center g-5 mb-5">
        <div class="col-lg-6">
          <span class="eyebrow text-info">SIMULATION LABORATORIES</span>
          <h2>Practice. Refine. Master.</h2>
          <p class="text-muted">Our simulation laboratories are engineered to mirror actual hospital wards and critical care units. Students develop procedural muscle memory and critical clinical judgment before working directly with hospital patients.</p>
          
          <div class="d-flex flex-column gap-3 mt-4">
            <div class="d-flex align-items-start gap-3">
              <i class="bi bi-check-circle-fill text-primary fs-5 mt-1"></i>
              <div>
                <strong class="text-dark d-block">Fundamental Nursing Skills Lab</strong>
                <span class="text-muted small">Equipped with patient care models, injection pads, CPR training stations, and sterile procedure setups.</span>
              </div>
            </div>
            <div class="d-flex align-items-start gap-3">
              <i class="bi bi-check-circle-fill text-primary fs-5 mt-1"></i>
              <div>
                <strong class="text-dark d-block">Maternal & Child Health Lab</strong>
                <span class="text-muted small">Specialized training mannequins for obstetric care, neonatal resuscitation, and pediatric nursing.</span>
              </div>
            </div>
            <div class="d-flex align-items-start gap-3">
              <i class="bi bi-check-circle-fill text-primary fs-5 mt-1"></i>
              <div>
                <strong class="text-dark d-block">Anatomy & Physiology Science Lab</strong>
                <span class="text-muted small">Detailed anatomical models, skeleton replicas, histology slides, and microscopy workstations.</span>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="position-relative">
            <img class="img-fluid rounded-4 shadow-lg w-100" src="{{ $site['clinical_image'] ?? asset('images/nursing-hero.png') }}" alt="Nursing students practicing in simulation lab">
            <div class="position-absolute bottom-0 start-0 m-3 p-3 bg-white bg-opacity-90 backdrop-blur rounded-3 shadow-sm d-flex align-items-center gap-3">
              <i class="bi bi-patch-check-fill text-success fs-3"></i>
              <div>
                <strong class="d-block text-dark small">Supervised by Clinical Mentors</strong>
                <small class="text-muted">Over 1,200+ hours of lab & clinical practice</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Hospital Clinical Rotations Section -->
  <section class="section">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-6 order-lg-2">
          <span class="eyebrow">HOSPITAL ATTACHMENTS</span>
          <h2>Real Bedside Exposure in Leading Teaching Hospitals</h2>
          <p class="text-muted">Clinical rotations bridge classroom theory with direct patient care. Under the supervision of clinical educators, nursing students rotate through major medical departments:</p>
          
          <div class="row g-3 my-3">
            <div class="col-sm-6">
              <div class="p-3 bg-light rounded-3 border-start border-4 border-primary">
                <strong class="d-block text-dark">Emergency & Trauma</strong>
                <small class="text-muted">Triage and critical emergency care.</small>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="p-3 bg-light rounded-3 border-start border-4 border-primary">
                <strong class="d-block text-dark">Intensive Care Units (ICU)</strong>
                <small class="text-muted">Cardiac and respiratory monitoring.</small>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="p-3 bg-light rounded-3 border-start border-4 border-primary">
                <strong class="d-block text-dark">Pediatric & Maternity</strong>
                <small class="text-muted">Child health and maternal care.</small>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="p-3 bg-light rounded-3 border-start border-4 border-primary">
                <strong class="d-block text-dark">General Medical & Surgical</strong>
                <small class="text-muted">Pre/post-operative care and recovery.</small>
              </div>
            </div>
          </div>

          <p class="text-muted small">Every student maintains a clinical logbook verified weekly by clinical preceptors, guaranteeing rigorous exposure to all required nursing competencies.</p>
        </div>

        <div class="col-lg-6 order-lg-1">
          <div class="p-4 p-lg-5 bg-navy rounded-4 text-white shadow-lg" style="background: #071f33;">
            <span class="eyebrow text-info">STUDENT SUCCESS</span>
            <h3 class="fw-bold mb-4">Hands-On Clinical Highlights</h3>
            
            <div class="d-flex flex-column gap-4">
              <div class="d-flex gap-3">
                <div class="fs-2 text-info fw-bold">01</div>
                <div>
                  <h6 class="fw-bold mb-1">Direct Bedside Mentorship</h6>
                  <small class="text-light opacity-75">1:8 clinical instructor-to-student ratio during hospital ward duties.</small>
                </div>
              </div>

              <div class="d-flex gap-3">
                <div class="fs-2 text-info fw-bold">02</div>
                <div>
                  <h6 class="fw-bold mb-1">Community Health Outreaches</h6>
                  <small class="text-light opacity-75">Students actively lead vaccination campaigns, blood donation drives, and public hygiene seminars.</small>
                </div>
              </div>

              <div class="d-flex gap-3">
                <div class="fs-2 text-info fw-bold">03</div>
                <div>
                  <h6 class="fw-bold mb-1">Graduate Employment Readiness</h6>
                  <small class="text-light opacity-75">Over 95% of our graduates secure positions in major medical facilities within months of graduation.</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Student Life & Campus Community -->
  <section class="section section-soft">
    <div class="container">
      <div class="text-center mb-5">
        <span class="eyebrow">COMMUNITY & WELFARE</span>
        <h2>Student Life & Extracurricular Activities</h2>
        <p class="text-muted mx-auto" style="max-width: 600px;">A balanced educational journey nurtures both academic brilliance and personal growth.</p>
      </div>

      <div class="row g-4">
        <div class="col-md-4">
          <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <img class="w-100" style="height: 200px; object-fit: cover;" src="{{ asset('images/student-nurse.png') }}" alt="Student Nursing Society">
            <div class="p-4">
              <h5 class="fw-bold text-dark">Student Nursing Council</h5>
              <p class="text-muted small">Fosters leadership, peer mentoring, academic debates, and organizes health awareness symposiums on campus.</p>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <img class="w-100" style="height: 200px; object-fit: cover;" src="{{ asset('images/nursing-hero.png') }}" alt="Free community health clinic">
            <div class="p-4">
              <h5 class="fw-bold text-dark">Community Health Camps</h5>
              <p class="text-muted small">Volunteer healthcare camps in surrounding communities, providing basic health screenings and health education.</p>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <img class="w-100" style="height: 200px; object-fit: cover;" src="{{ $site['campus_image'] ?? asset('images/nursing-hero.png') }}" alt="Hostel and recreation">
            <div class="p-4">
              <h5 class="fw-bold text-dark">Sports & Cultural Events</h5>
              <p class="text-muted small">Annual sports gala, badminton, table tennis tournaments, and celebratory cultural days designed to foster camaraderie.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Campus Visit Call to Action -->
  <section class="py-5 bg-navy text-white text-center" style="background: #071f33;">
    <div class="container py-4">
      <span class="eyebrow text-info">VISIT OUR CAMPUS</span>
      <h2 class="fw-bold mb-3">See Our Laboratories and Facilities in Person</h2>
      <p class="text-light opacity-75 mx-auto mb-4" style="max-width: 580px;">We welcome prospective students and parents to tour our campus, meet our faculty, and discover the nursing learning environment.</p>
      <div class="d-flex justify-content-center gap-3 flex-wrap">
        <a href="{{ url('/contact') }}" class="btn btn-primary btn-lg px-4">Book a Campus Tour</a>
        <a href="{{ url('/apply') }}" class="btn btn-outline-light btn-lg px-4">Apply Online Now</a>
      </div>
    </div>
  </section>
</main>
@endsection

