<?php $parser = new Less_Parser(['compress' => true]); ?>
<!doctype html>
<html class="@yield('theme', config('app.dark-mode') ? 'dark' : 'light')-mode no-js" lang="{{ config('app.locale', 'en') }}" data-autoreload="{{ config('app.autoreload') ? 'on' : 'off' }}" data-env="{{ app()->environment() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @yield('meta')
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.min.css">
        <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Palanquin:400,700|Lekton:400,400italic,700">
<?php
$parser->parseFile(base_path().'/resources/less/ship.less');
$css = $parser->getCss();
?>
        {{-- <link rel="stylesheet" type="text/css" href="/css/ship.css"> --}}
        <style type="text/css"><?=$css?></style>
        @yield('styles')
        <title>@yield('title', config('ship.title')) - @lang('ship.app')</title>
    </head>
    <body>
        <main class="container">
            @yield('content')
        </main>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
        <script type="text/javascript" src="ship.min.js"></script>
        @yield('scripts')
    </body>
</html>
