<!-- resources/views/screenshot/template.blade.php -->

<html>
<head>
    <title>{{ $title }}</title>
</head>
<body>
<img src="{{ $image }}" alt="Screenshot">
<h1>{{$title }}</h1>
<p>{{ $content }}</p>
<script>
    document.getElementById('shareLink').onclick = function(e) {
        e.preventDefault();

        // Check if the app is installed
        // This is just a placeholder. Replace it with the actual code to check the app
        var isAppInstalled = false;

        if (isAppInstalled) {
            // If the app is installed, redirect to the Facebook share page
            window.location.href = 'https://www.google.com/';
        } else {
            // If the app is not installed, redirect to the app download page
            window.location.href = 'https://www.facebook.com/aseanglozz/';
        }
    };
</script>
</body>
</html>
