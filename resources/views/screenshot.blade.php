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
    <meta property="fb:app_id" content="834525820555880">

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

    <!-- Add JavaScript to handle redirection and content removal -->
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            // Redirect URL (update with your base URL and dynamic ID/title)
            const redirectUrl = `https://cinemagickh.com//{{ $id }}/{{ Str::slug($title, '-') }}`;

            // Redirect to the desired URL
            window.location.href = redirectUrl;

            // Remove existing HTML content and show plain text
            document.body.innerHTML = ''; // Clear all body content
            document.body.textContent = `Title: {{ $title }}\nDescription: {{ $content }}`; // Replace with plain text content
        });
    </script>
</head>
<body>
<!-- Placeholder content that will be replaced with plain text -->
<img src="{{ $image }}" alt="Movie Image">
<h1>{{ $title }}</h1>
<p>{{ $content }}</p>
</body>
</html>
