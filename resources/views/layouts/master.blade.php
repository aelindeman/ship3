<!doctype html>
<html class="@yield('theme', config('app.dark-mode') ? 'dark' : 'light')-mode no-js" lang="{{ config('app.locale', 'en') }}" data-autoreload="{{ config('app.autoreload') ? 'on' : 'off' }}" data-env="{{ app()->environment() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @yield('meta')
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.min.css">
        <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,300italic,600,600italic|Source+Code+Pro:300,600">
        <link rel="stylesheet" type="text/css" href="{{ url('ship.min.css') }}">
        @yield('styles')
        <title>@yield('title', config('ship.title')) - @lang('ship.app')</title>
    </head>
    <body>
        <main>
            @yield('content')
        </main>
        <div id="loading-indicator">@lang('ship.loading')</div>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
        @yield('scripts')
    </body>
</html>
