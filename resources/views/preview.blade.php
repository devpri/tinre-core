@extends('tinre::layouts.app')

@section('title', __('URL Preview'))

@section('content')
    <div class="text-center w-full py-16 self-center m-4">
        <h1 class="font-bold mb-4">{{  __('URL Preview') }}</h1>
        <p class="uppercase mb-2">{{ __('Created At') }} {{ $url->created_at }}</p>
        <p class="font-bold text-xl mb-2">{{ $url->long_url }}</p>
        <a href="{{ rtrim(config('app.url', null), '/').'/' . $url->path }}" target="_blanl" rel="noreferrer">{{ rtrim(config('app.url', null), '/').'/' . $url->path }}</a>
    </div>
@endsection
