<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Application {{ $application->reference }}</title>
  <!-- Unified Site Favicons -->
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('logo.png') }}?v=2">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('logo.png') }}?v=2">
  <link rel="shortcut icon" type="image/png" href="{{ asset('logo.png') }}?v=2">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('logo.png') }}?v=2">
  <style>
    *{box-sizing:border-box}
    body{font-family:Arial,sans-serif;color:#102b3d;margin:0;background:#edf2f4}
    .sheet{width:210mm;min-height:297mm;margin:20px auto;background:#fff;padding:18mm;box-shadow:0 4px 25px #0002}
    .head{display:flex;justify-content:space-between;align-items:center;border-bottom:3px solid #0b6d9f;padding-bottom:18px}
    .brand{font-size:20px;font-weight:800}
    .brand small{display:block;font-size:9px;letter-spacing:1px;color:#567}
    .status{padding:8px 13px;background:#e5f4f1;border-radius:20px;text-transform:uppercase;font-size:10px;font-weight:bold}
    .title{text-align:center;margin:27px 0}
    .title h1{font-size:24px;margin:0 0 6px}
    .title p{font-size:11px;color:#607180}
    .section{margin:25px 0}
    .section h2{font-size:13px;text-transform:uppercase;letter-spacing:.6px;background:#edf5f7;padding:9px 11px;border-left:4px solid #0b6d9f}
    .grid{display:grid;grid-template-columns:1fr 1fr;gap:15px 28px}
    .field label{display:block;font-size:8px;text-transform:uppercase;color:#71828d;font-weight:bold;margin-bottom:4px}
    .field div{font-size:12px;border-bottom:1px solid #dce4e8;padding-bottom:5px;min-height:20px}
    .wide{grid-column:1/-1}
    .statement{font-size:11px;line-height:1.6;border:1px solid #dce4e8;padding:12px;white-space:pre-wrap}
    .foot{margin-top:35px;border-top:1px solid #ccd8de;padding-top:12px;font-size:8px;color:#71828d;display:flex;justify-content:space-between}
    .actions{position:fixed;right:25px;top:25px}
    .actions button{border:0;border-radius:6px;padding:11px 18px;background:#0b6d9f;color:#fff;font-weight:bold;cursor:pointer}
    @media print{
      body{background:#fff}
      .sheet{margin:0;box-shadow:none;width:auto;min-height:auto}
      .actions{display:none}
      @page{size:A4;margin:0}
    }
  </style>
</head>
<body>
  <div class="actions">
    <button onclick="window.print()">Print Application</button>
  </div>
  <main class="sheet">
    <header class="head">
      <div class="brand">
        {{ \App\Models\SiteContent::where('key','college_name')->value('value') ?? 'Dir College Of Nursing & Allied Health Science' }}
        <small>ADMISSIONS DEPARTMENT</small>
      </div>
      <span class="status">{{ str_replace('_',' ',$application->status) }}</span>
    </header>
    <div class="title">
      <h1>Admission Application</h1>
      <p>Reference: <strong>{{ $application->reference }}</strong> · Submitted {{ $application->submitted_at->format('F j, Y \a\t g:i A') }}</p>
    </div>
    <section class="section">
      <h2>Program</h2>
      <div class="grid">
        <div class="field">
          <label>Program</label>
          <div>{{ $application->program->name }}</div>
        </div>
        <div class="field">
          <label>Duration</label>
          <div>{{ $application->program->duration }}</div>
        </div>
      </div>
    </section>
    <section class="section">
      <h2>Personal Information</h2>
      <div class="grid">
        <div class="field">
          <label>Full name</label>
          <div>{{ $application->first_name }} {{ $application->last_name }}</div>
        </div>
        <div class="field">
          <label>Date of birth</label>
          <div>{{ $application->date_of_birth->format('F j, Y') }}</div>
        </div>
        <div class="field">
          <label>Gender</label>
          <div>{{ str_replace('_',' ',$application->gender) }}</div>
        </div>
        <div class="field">
          <label>National ID / Passport</label>
          <div>{{ $application->national_id ?: 'Not provided' }}</div>
        </div>
        <div class="field">
          <label>Email</label>
          <div>{{ $application->email }}</div>
        </div>
        <div class="field">
          <label>Phone</label>
          <div>{{ $application->phone }}</div>
        </div>
        <div class="field wide">
          <label>Address</label>
          <div>{{ $application->address }}, {{ $application->city }}, {{ $application->country }}</div>
        </div>
      </div>
    </section>
    <section class="section">
      <h2>Education</h2>
      <div class="grid">
        <div class="field">
          <label>Highest qualification</label>
          <div>{{ $application->highest_qualification }}</div>
        </div>
        <div class="field">
          <label>Institution</label>
          <div>{{ $application->institution }}</div>
        </div>
        <div class="field">
          <label>Graduation year</label>
          <div>{{ $application->graduation_year }}</div>
        </div>
        <div class="field">
          <label>Grade percentage</label>
          <div>{{ $application->grade_percentage ? $application->grade_percentage.'%' : 'Not provided' }}</div>
        </div>
      </div>
    </section>
    @if($application->statement)
      <section class="section">
        <h2>Personal Statement</h2>
        <div class="statement">{{ $application->statement }}</div>
      </section>
    @endif
    @if($application->admin_notes)
      <section class="section">
        <h2>Administrative Notes</h2>
        <div class="statement">{{ $application->admin_notes }}</div>
      </section>
    @endif
    <footer class="foot">
      <span>Generated {{ now()->format('F j, Y g:i A') }}</span>
      <span>Confidential admissions record</span>
    </footer>
  </main>
</body>
</html>

