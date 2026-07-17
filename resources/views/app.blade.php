<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#082f49">
        <meta name="description" content="Elixe conecta negocios y clientes mediante una red de pantallas digitales en Galicia.">
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        <title inertia>{{ config('app.name', 'Elixe Ads Platform') }}</title>
        @routes
        @viteReactRefresh
        @vite(['resources/css/app.css', 'resources/js/app.tsx'])
        @inertiaHead
    </head>
    <body>
        @inertia
    </body>
</html>
