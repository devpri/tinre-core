@extends('tinre::layouts.auth')

@section('content')
<div class="xl:w-1/3 sm:w-full md:w-1/2 bg-white rounded-md mx-auto my-2">
    <div class="p-4">
        <h3 class="text-center mb-3">{{ __('Login') }}</h3>
        <form method="POST" action="{{ route('login') }}">
            @csrf
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
                <label class="cursor-pointer" for="remember">
                    <input class="mr-1" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span class="text-sm text-gray-900">{{ __('Remember Me') }}</span>
                </label>
            </div>

            <div>
                <button type="submit" class="btn btn-primary block w-full">
                    {{ __('Login') }}
                </button>
                            
                @if (Route::has('register'))
                    <div class="block mt-4 text-center">
                        <a class="btn btn-secondary block w-full" href="{{ route('register') }}">
                            {{ __('Create an Account') }}
                        </a>
                    </div>
                @endif

                @if (Route::has('password.request'))
                    <div class="block mt-2 text-center">
                        <a class="hover:text-primary" href="{{ route('password.request') }}">
                            {{ __('Forgot Your Password?') }}
                        </a>
                    </div>                       
                @endif
            </div>
        </form>
    </div>
</div>
@endsection
