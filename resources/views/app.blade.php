<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', Config::string('app.locale')) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="icon" href="{{ url('favicon.ico') }}" sizes="any" />

        <meta name="theme-color" content="#ffffff" />

        @fonts('sans')

        @viteReactRefresh
        @vite('resources/js/app.tsx')

        <x-inertia::head>
            <title>Template</title>
        </x-inertia::head>
    </head>

    <body class="bg-background text-foreground font-sans antialiased">
        <x-inertia::app />
    </body>
</html>
