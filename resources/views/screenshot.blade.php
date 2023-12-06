<!-- resources/views/screenshot/template.blade.php -->

<html>
<head>

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

