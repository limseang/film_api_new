<!DOCTYPE html>
<html lang="{{ App::getLocale() }}" dir="ltr" data-color-theme="light" >
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>@yeild('title', config('app.name'))</title>
    <meta name="description" content="Limitless - Responsive Web Application Kit by Eugene Kopyov">
    <meta name="author" content="Eugene Kopyov">
    @include('layouts.includes.header')

</head>

<body class="language_{{ App::getLocale() }}">
	@yield('content')
</body>
</html>