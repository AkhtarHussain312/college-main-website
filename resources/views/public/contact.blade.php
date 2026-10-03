@extends('layouts.public')
@php
  $active = 'contact';
  $phone = $site['college_phone'] ?? '+92 000 0000000';
  $whatsapp = $site['college_whatsapp'] ?? $phone;
  $phoneHref = 'tel:'.preg_replace('/[^+\d]/', '', $phone);
  $whatsappHref = 'https://wa.me/'.preg_replace('/\D/', '', $whatsapp).'?text='.rawurlencode('Hello, I would like information about the college.');
  $departments = [
    ['icon' => 'bi-building', 'name' => 'Main Office', 'detail' => 'General enquiries', 'phone' => $phone],
    ['icon' => 'bi-person-heart', 'name' => 'Admissions Department', 'detail' => 'Programs and applications', 'phone' => $site['admissions_phone'] ?? $phone],
    ['icon' => 'bi-headset', 'name' => 'Student Support', 'detail' => 'Current student assistance', 'phone' => $site['support_phone'] ?? $phone],
    ['icon' => 'bi-receipt', 'name' => 'Accounts & Fees', 'detail' => 'Fees and payment enquiries', 'phone' => $site['accounts_phone'] ?? $phone],
  ];
  $faqs = [
    [$site['contact_faq_1_question'] ?? 'How quickly will you respond?', $site['contact_faq_1_answer'] ?? 'Our team normally responds within two working days.'],
    [$site['contact_faq_2_question'] ?? 'Which number should I use for admissions?', $site['contact_faq_2_answer'] ?? 'Call our admissions office for program and application guidance.'],
    [$site['contact_faq_3_question'] ?? 'Can I contact you through WhatsApp?', $site['contact_faq_3_answer'] ?? 'Yes. Use the WhatsApp buttons on this page to begin a conversation with our team.'],
    [$site['contact_faq_4_question'] ?? 'What are your office hours?', $site['contact_faq_4_answer'] ?? ($site['contact_office_hours'] ?? 'Monday-Friday, 8:30 AM-5:00 PM.')],
  ];
@endphp
@section('content')
<main class="contact-page">
  <section class="contact-conversation">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-6">
          <span class="contact-kicker">{{ $site['contact_eyebrow'] ?? 'GET IN TOUCH' }}</span>
          <h1>{{ $site['contact_title'] ?? 'Let us Start a Conversation' }}</h1>
          <p>{{ $site['contact_description'] ?? 'Get clear guidance from our admissions and student support teams whenever you need it.' }}</p>
          <div class="hero-contact-actions"><a href="{{ $phoneHref }}" class="btn btn-primary">Call Us</a><a href="{{ $whatsappHref }}" target="_blank" rel="noopener" class="btn btn-outline-dark"><i class="bi bi-whatsapp"></i> Chat on WhatsApp</a></div>
          <div class="contact-promises"><span>Fast response</span><span>Friendly support</span><span>Multiple contact options</span></div>
        </div>
        <div class="col-lg-6">
          <div class="contact-phone-art"><div class="orbit orbit-one"></div><div class="orbit orbit-two"></div><div class="phone"><div class="phone-screen"><i class="bi bi-whatsapp"></i></div></div><span class="floating-contact mail"><i class="bi bi-envelope-fill"></i></span><span class="floating-contact pin"><i class="bi bi-geo-alt-fill"></i></span><span class="floating-contact chat"><i class="bi bi-chat-left-text-fill"></i></span></div>
        </div>
      </div>
      <div class="row g-3 contact-quick-grid">
        <div class="col-sm-6 col-lg-3"><a class="quick-contact-card" href="{{ $phoneHref }}"><i class="bi bi-telephone-fill"></i><h2>Call Us</h2><b>{{ $phone }}</b><span>Call Now <i class="bi bi-arrow-up-right"></i></span></a></div>
        <div class="col-sm-6 col-lg-3"><a class="quick-contact-card whatsapp" href="{{ $whatsappHref }}" target="_blank" rel="noopener"><i class="bi bi-whatsapp"></i><h2>WhatsApp</h2><b>{{ $whatsapp }}</b><span>Start Chat <i class="bi bi-arrow-up-right"></i></span></a></div>
        <div class="col-sm-6 col-lg-3"><a class="quick-contact-card" href="mailto:{{ $site['college_email'] ?? 'admissions@example.edu' }}"><i class="bi bi-envelope-fill"></i><h2>Email</h2><b>{{ $site['college_email'] ?? 'admissions@example.edu' }}</b><span>Send Email <i class="bi bi-arrow-up-right"></i></span></a></div>
        <div class="col-sm-6 col-lg-3"><a class="quick-contact-card" href="https://www.google.com/maps/search/?api=1&query={{ rawurlencode($site['college_address'] ?? ($collegeName ?? 'Dir College Of Nursing & Allied Health Science')) }}" target="_blank" rel="noopener"><i class="bi bi-geo-alt-fill"></i><h2>Visit Us</h2><b>{{ $site['college_address'] ?? 'College campus' }}</b><span>Get Directions <i class="bi bi-arrow-up-right"></i></span></a></div>
      </div>
    </div>
  </section>

  <section class="contact-departments">
    <div class="container">
      <div class="contact-heading"><span>DIRECT CONTACT</span><h2>Choose the Right Number</h2><p>Connect directly with the team best placed to help.</p></div>
      <div class="row g-3">
        @foreach($departments as $department)
          <div class="col-md-6"><article class="department-card"><i class="bi {{ $department['icon'] }}"></i><div><h3>{{ $department['name'] }}</h3><p>{{ $department['detail'] }}</p><b>{{ $department['phone'] }}</b><a href="tel:{{ preg_replace('/[^+\d]/', '', $department['phone']) }}" class="btn btn-primary btn-sm">Call Now</a></div></article></div>
        @endforeach
      </div>
    </div>
  </section>

  <section class="contact-message-section">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-8">
          <form class="reference-contact-form" method="post" action="{{ url('/contact') }}">
            @csrf
            <span class="contact-kicker">WRITE TO US</span>
            <h2>{{ $site['contact_form_title'] ?? 'Send Us a Message' }}</h2>
            <p>{{ $site['contact_form_text'] ?? 'Complete the form and a member of our team will respond shortly.' }}</p>
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if(isset($errors) && $errors->any())<div class="alert alert-danger">Please check the highlighted fields and try again.</div>@endif
            <div class="row g-3">
              <div class="col-md-6"><label>Full name *</label><input name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
              <div class="col-md-6"><label>Email address *</label><input name="email" value="{{ old('email') }}" type="email" class="form-control @error('email') is-invalid @enderror" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
              <div class="col-md-6"><label>Phone number</label><input name="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror">@error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
              <div class="col-md-6"><label>Department / enquiry type *</label><select name="subject" class="form-select @error('subject') is-invalid @enderror" required><option value="" disabled {{ old('subject') ? '' : 'selected' }}>Select a department</option>@foreach(['Admissions','Programs and Courses','Fee Structure','Scholarships','Student Support','Campus Visit','General Inquiry'] as $subject)<option {{ old('subject') === $subject ? 'selected' : '' }}>{{ $subject }}</option>@endforeach</select>@error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
              <div class="col-12"><label>Message *</label><textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="6" required>{{ old('message') }}</textarea>@error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            </div>
            <button class="btn btn-primary w-100 mt-3">Send Message <i class="bi bi-send"></i></button>
            <small class="secure-note"><i class="bi bi-lock"></i> Your information is sent securely and used only to respond to your enquiry.</small>
          </form>
        </div>
        <div class="col-lg-4"><aside class="direct-panel"><h2>Prefer to Talk Directly?</h2><div><i class="bi bi-telephone"></i><span>Phone<b>{{ $phone }}</b></span></div><div><i class="bi bi-whatsapp"></i><span>WhatsApp<b>{{ $whatsapp }}</b></span></div><div><i class="bi bi-envelope"></i><span>Email<b>{{ $site['college_email'] ?? 'admissions@example.edu' }}</b></span></div><div><i class="bi bi-clock"></i><span>Office Hours<b>{{ $site['contact_office_hours'] ?? 'Monday-Friday, 8:30 AM-5:00 PM' }}</b></span></div><small><i></i> Open during office hours</small></aside></div>
      </div>
    </div>
  </section>

  <section class="contact-faq">
    <div class="container">
      <div class="contact-heading text-center"><span>FAQ</span><h2>Before You Contact Us</h2></div>
      <div class="accordion" id="contactFaq">
        @foreach($faqs as $index => $faq)
          <div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button {{ $index ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq-{{ $index }}">{{ $faq[0] }}</button></h3><div id="faq-{{ $index }}" class="accordion-collapse collapse {{ $index ? '' : 'show' }}" data-bs-parent="#contactFaq"><div class="accordion-body">{{ $faq[1] }}</div></div></div>
        @endforeach
      </div>
    </div>
  </section>
</main>
@endsection
