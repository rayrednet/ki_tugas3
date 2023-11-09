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
        <div id="app" class="d-flex flex-row">
            @include('components.sidebar.sidebar')
            <div id="main" class="layout-navbar d-flex flex-column">
                <div id="main-content" style="flex-grow: 1">
                    <button class="btn btn-block btn-primary">TES</button>
                    {{ $slot }}
                </div>
            </div>
        </div>

        <!-- Scripts -->
        @vite(['resources\js\app.js'])
        @stack('scripts')
    </body>
</html>
