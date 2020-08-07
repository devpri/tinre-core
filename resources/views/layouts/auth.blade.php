<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Devpri Tinre') }}</title>
    <!-- Styles -->
    <link href="{{ mix('app.css', 'vendor/tinre') }}" rel="stylesheet">
</head>

<body class="bg-primary">
    <section class="flex flex-wrap items-stretch  min-h-screen">
        <div class="w-full self-end">
            <div class="container mx-auto px-4">
                <div class="text-center mb-6">
                    @include('tinre::partials.logo')
                </div>
                @yield('content')
            </div>
        </div>
        @include('tinre::partials.footer', ['style' => 'white'])
  </section>
</body>
</html>