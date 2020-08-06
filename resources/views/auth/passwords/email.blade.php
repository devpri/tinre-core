@extends('tinre::layouts.auth')

@section('content')
<div class="xl:w-1/3 sm:w-full md:w-1/2 bg-white rounded-md mx-auto my-2">
    <div class="p-4">
        <h3 class="text-center mb-3">{{ __('Reset Password') }}</h3>
        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            @if (session('status'))
                <div class="bg-green-500 p-2 text-white rounded mb-3" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            <div class="form-group mb-4">
                <input id="email" type="email" class="form-input w-full @error('email') invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder={{ __('E-mail') }} autofocus>

                @error('email')
                    <span class="error-message" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <button type="submit" class="btn btn-primary block w-full">
                    {{ __('Send Password Reset Link') }}
                </button>
                            
                @if (Route::has('login'))
                    <div class="block mt-4 text-center">
                        <a class="btn btn-secondary block w-full" href="{{ route('login') }}">
                            {{ __('Back to Login') }}
                        </a>
                    </div>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection
