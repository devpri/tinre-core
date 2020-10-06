<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Devpri Tinre') }}</title>
    <!-- Styles -->
    <link href="{{ asset(mix('app.css', 'vendor/tinre')) }}" rel="stylesheet">
    @foreach(\Devpri\Tinre\Tinre::styles() as $path)
        <link rel="stylesheet" href="{{ $path }}">
    @endforeach
</head>

<body>
    <div id="app">
        <loading ref="loading"></loading>
        <div class="dashboard-layout bg-gray-300 flex flex-wrap items-stretch  min-h-screen">
            <div class="w-full flex-start">
                <div class="topnav z-30 w-full bg-primary">
                    <div class="p-4 flex justify-between">
                        <div class="flex">
                            <sidebar-button></sidebar-button>
                            <div class="logo bg-primary lg:w-56 lg:pl-4">
                                <router-link :to="{ name: 'home' }" class="inline-block self-center">
                                    @include('tinre::partials.logo')
                                </router-link>
                            </div>
                        </div>
                        <div class="content flex items-center -m-2">
                            <button class="btn btn-md bg-gray-100 hover:bg-gray-300 mx-2 lg:mr-4" @click="$modal.show('create')">{{ __('Create') }}</button>
                        </div>
                    </div>
                </div>
                <div class="main-wrap relative flex min-h-screen">
                    <sidebar-content>
                        @include('tinre::partials.sidebar')
                    </sidebar-content>
                    <div class="w-auto flex-1">
                        <transition name="page" mode="out-in">
                            <slot>
                                <router-view />
                            </slot>
                        </transition>
                        @include('tinre::partials.footer')
                    </div>
                </div>
            </div>
        </div>
        <create-url></create-url>
    </div>

    <script>
        window.config = @json(\Devpri\Tinre\Tinre::addDashboardConfig()->addUserToConfig(request())->config());
        window.translations = @json(\Devpri\Tinre\Tinre::translations());
        window.messages = @json(\Devpri\Tinre\Tinre::messages(session()));
    </script>

    <!-- Scripts -->
    <script src="{{ asset(mix('manifest.js', 'vendor/tinre')) }}"></script>
    <script src="{{ asset(mix('vendor.js', 'vendor/tinre')) }}"></script>
    <script src="{{ asset(mix('dashboard.js', 'vendor/tinre')) }}"></script>

    @foreach(\Devpri\Tinre\Tinre::scripts() as $path)
        <script src="stylesheet" href="{{ $path }}"></script>
    @endforeach
</body>

</html>