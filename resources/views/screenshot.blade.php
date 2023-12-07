<!-- resources/views/screenshot/template.blade.php -->

<html>
<head>
    <title>Share</title>
    <meta property="og:url" content="{{ $facebook }}" />
        <meta property="og:type" content="article" />
        <meta property="og:title" content="{{ $title }}" />
        <meta property="og:description" content="{{ $content }}" />
        <meta property="og:image" content="{{ $image }}" />
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="630" />
</head>
<body>
<div id="showShareButton">
    <img src="{{ $image }}" alt="Screenshot">
    <h1>{{$title }}</h1>
    <p>{{ $content }}</p>
</div>

</body>
<script>
    window.location.href = "{{ $facebook }}";
</script>
</html>

