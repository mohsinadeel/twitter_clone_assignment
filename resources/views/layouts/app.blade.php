<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @yield('head')
    @stack('styles')
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    @include('layouts.navbar')

    <main class="flex-1">
        @yield('content')
    </main>

    @include('layouts.footer')

    @yield('scripts')
    @stack('scripts')
</body>
</html>
