<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Vessel Host' }} · Vessel Host</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>
{{ $slot ?? '' }}
@yield('content')
</body>
</html>