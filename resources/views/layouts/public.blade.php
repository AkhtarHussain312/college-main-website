<!doctype html>
@php
  $schema = [
    '@context' => 'https://schema.org',
    '@type' => 'CollegeOrUniversity',
    'name' => $collegeName,
    'url' => url('/'),
    'logo' => asset('logo.png'),
    'address' => $site['college_address'] ?? '',
    'telephone' => $site['college_phone'] ?? '',
  ];
@endphp
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $metaTitle }}</title>
  <meta name="description" content="{{ $metaDescription }}">
  <link rel="canonical" href="{{ $canonical }}">
  <meta property="og:type" content="website">
  <meta property="og:title" content="{{ $metaTitle }}">
  <meta property="og:description" content="{{ $metaDescription }}">
  <meta property="og:url" content="{{ $canonical }}">
  <meta property="og:image" content="{{ $metaImage }}">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="{{ $metaTitle }}">
  <meta name="twitter:description" content="{{ $metaDescription }}">
  <meta name="twitter:image" content="{{ $metaImage }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="theme-color" content="#071f33">
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|manrope:500,600,700,800" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/css/about.css', 'resources/css/brand.css', 'resources/css/fees.css', 'resources/css/contact.css', 'resources/css/event.css', 'resources/js/app.js'])
  <script type="application/ld+json">
    {!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
  </script>
</head>
<body>
  @include('partials.public-header')
  @yield('content')
  @include('partials.public-footer')
  <div id="dcn-chat-widget"></div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

