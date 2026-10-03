<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $collegeName }}</title>
  <meta name="description" content="{{ $metaDescription }}">
  <meta property="og:title" content="{{ $collegeName }}">
  <meta property="og:description" content="{{ $metaDescription }}">
  <meta property="og:image" content="{{ asset('logo.png') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="theme-color" content="#071f33">
  <!-- Unified Site Favicons -->
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('logo.png') }}?v=2">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('logo.png') }}?v=2">
  <link rel="shortcut icon" type="image/png" href="{{ asset('logo.png') }}?v=2">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('logo.png') }}?v=2">
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|manrope:500,600,700,800" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
  <div id="app"></div>
</body>
</html>

