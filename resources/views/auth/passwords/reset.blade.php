@extends('tinre::layouts.auth')

@section('content')
<div class="xl:w-1/3 sm:w-full md:w-1/2 bg-white rounded-md mx-auto my-2">
    <div class="p-4">
        <h3 class="text-center mb-3">{{ __('Reset Password') }}</h3>
        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group mb-4">
                <input id="email" type="email" class="form-input w-full @error('email') invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder={{ __('E-mail') }} autofocus>

                @error('email')
                    <span class="error-message" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group mb-4">
                <input id="password" type="password" class="form-input w-full @error('password') invalid @enderror" name="password" required autocomplete="password" placeholder={{ __('Password') }} autofocus>
                @error('password')
                    <span class="error-message" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group mb-4">
                <input id="password_confirmation" type="password" class="form-input w-full @error('password') invalid @enderror" name="password_confirmation" required placeholder={{ __('Password Confirmation') }} autofocus>
            </div>

            <div>
                <button type="submit" class="btn btn-primary block w-full">
                    {{ __('Reset Password') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
