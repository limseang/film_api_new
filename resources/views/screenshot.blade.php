<!DOCTYPE html>
<html>
<head>
    <!-- Basic Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Required Open Graph Meta Tags for Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $content }}">
    <meta property="og:image" content="{{ $image }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="CineMagicKH">
    <meta property="fb:app_id" content="834525820555880"> <!-- Add this line with your app ID -->

    <!-- Additional Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $content }}">
    <meta name="twitter:image" content="{{ $image }}">
    <meta name="twitter:url" content="{{ url()->current() }}">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Page Title -->
    <title>{{ $title }}</title>
</head>
<body>
<img src="{{ $image }}" alt="Movie Image">
<h1>{{ $title }}</h1>
<p>{{ $content }}</p>
</body>
</html>
