<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@if(View::hasSection('title'))@yield('title')@else{{ config('app.name', 'Tinre') }}@endif</title>
    <meta name="description" content="@yield('meta_description')">
    <!-- Styles -->
    <link href="{{ asset(mix('app.css', 'vendor/tinre')) }}" rel="stylesheet">
</head>

<body>
    <div id="app">
        <div class="app-layout bg-gray-300 flex flex-wrap items-stretch  min-h-screen">
            <div class="topnav z-30 w-full bg-primary self-start">
                <div class="flex justify-between p-4">
                    <div class="flex items-center logo bg-primary lg:w-64">
                        <a href="{{ config('app.url') }}" class="inline-block self-center">
                            @include('tinre::partials.logo')
                        </a>
                    </div>
                    <div class="content flex items-center -mx-2">
                        @if(Auth::guest())
                            @if (Route::has('login'))
                                <a class="text-white px-2" href="{{ route('login') }}">{{ __('Login') }}</a>
                            @endif
                            @if (Route::has('login'))
                                <a class="text-white px-2" href="{{ route('register') }}">{{ __('Register') }}</a>
                            @endif
                        @else
                            <a class="text-white px-2" href="{{ config('tinre.dashboard_path') }}">{{ __('Dashboard') }}</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="main-wrap relative flex w-full">
                @yield('content')
            </div>
            @include('tinre::partials.footer')
        </div>
    </div>
    <script>
        window.translations = @json(\Devpri\Tinre\Tinre::translations());
        window.messages = @json(\Devpri\Tinre\Tinre::messages(session()));
    </script>

    <!-- Scripts -->
    <script src="{{ asset(mix('manifest.js', 'vendor/tinre')) }}"></script>
    <script src="{{ asset(mix('vendor.js', 'vendor/tinre')) }}"></script>
    <script src="{{ asset(mix('app.js', 'vendor/tinre')) }}"></script>
</body>

</html>