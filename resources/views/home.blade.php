@extends('tinre::layouts.app')

@section('content')
    <div class="text-center w-full py-16 self-center">
        <p class="text-2xl uppercase">{{ __('Open Source') }}</p>
        <h1 class="font-bold">{{  __('URL Shortener') }}</h1>
        <create-url-guest app-url="{{ rtrim(config('app.url', null), '/') . '/' }}" custom-path="{{ config('tinre.guest_form_custom_path') }}"></create-url-guest>
    </div>
@endsection
