@extends('tinre::layouts.auth')

@section('content')
<div class="xl:w-1/3 sm:w-full md:w-1/2 bg-white rounded-md mx-auto my-2">
    <div class="p-4">
        <h3 class="text-center mb-3">{{ __('Verify Your Email Address') }}</h3>
        <form method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <div class="mb-3">
                @if (session('resent'))
                    <div class="bg-green-500 p-2 text-white rounded mb-3" role="alert">
                        {{ __('A fresh verification link has been sent to your email address.') }}
                    </div>
                @endif

                {{ __('Before proceeding, please check your email for a verification link.') }}
                {{ __('If you did not receive the email') }},
            </div>
            <div>
                <button type="submit" class="btn btn-primary block w-full">
                    {{ __('click here to request another') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
