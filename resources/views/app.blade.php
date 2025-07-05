<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', Config::string('app.locale')) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="icon" href="{{ url('favicon.ico') }}" sizes="any" />

        <meta name="theme-color" content="#ffffff" />

        @inertiaHead

        @vite('resources/js/app.ts')
    </head>

    <body>
        @inertia
    </body>
</html>
