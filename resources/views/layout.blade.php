<!doctype html>
<html lang="en" xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ethmig – Survey Data Network</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="/images/fav_ethmig_32.png" type="image/x-icon">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css"
          integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <style>
        html {
            height: 100%;
        }
    </style>
    <script>
        let OPTIONS = @json($options)
    </script>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>
<body class="h-full text-gray-800 relative">
<div id="app" class="h-full">
    <div ref="wrapperSection" class="flex flex-col pt-1">
        {{-- HEADER --}}
        @include('partials._header')
        @yield('content')
        @include('partials._footer')
    </div>
    <back-to-top text="Back to top"></back-to-top>
</div>
</body>
</html>
