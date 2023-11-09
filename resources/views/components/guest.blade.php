<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Styles -->
        @vite(['resources/sass/app.scss'])
        @stack('styles')

        @stack('head-scripts')
        @vite(['node_modules/bootstrap/dist/js/bootstrap.js'])
    </head>

    <body>
        <div id="app">
            <div class="container d-flex min-vh-100 min-wh-100">
                <div class="content d-flex flex-fill">
                    {{ $slot }}
                </div>
            </div>
        </div>

        <!-- Scripts -->
        @vite(['resources/js/app.js'])
        @stack('scripts')
    </body>
</html>
