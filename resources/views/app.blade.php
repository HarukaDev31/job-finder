<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Job Finder - Portal de Trabajo')</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <!-- Vue Router View -->
        <router-view></router-view>
    </div>

    <script>
        // Configuración global de la aplicación
        window.appConfig = {
            user: @json(auth()->user()),
            isAuthenticated: @json(auth()->check()),
            csrfToken: '{{ csrf_token() }}'
        };
    </script>
</body>
</html> 