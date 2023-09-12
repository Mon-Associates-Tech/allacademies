<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <link rel="stylesheet" href="{{ asset('katex.min.css') }}">
    <style>
        p {
            margin-block-start: 0.2rem;
            margin-block-end: 0.2rem;
            line-height: 1.5;
        }

        h1, h2, h3, h4, h5, h6{
            font-weight: normal;
            margin-bottom: 0.2rem;
            margin-top:0;
        }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>