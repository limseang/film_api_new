<!-- resources/views/screenshot/template.blade.php -->

<html>
<head>
    <title>{{ $title }}</title>
</head>
<body>
<img src="{{ $image }}" alt="Screenshot">
<h1>{{$title }}</h1>
<p>{{ $content }}</p>
</body>
</html>
