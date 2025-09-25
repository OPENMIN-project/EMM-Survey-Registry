@extends('layouts.app')

@section('content')
<div class="bg-white flex flex-col justify-center lg:px-8 max-w-lg mx-auto px-6 py-12 rounded-lg shadow-lg">
  <div class="sm:mx-auto sm:w-full sm:max-w-sm">
    <a href="{{ config('app.url') }}"><img class="mx-auto h-20 w-auto" src="/images/logo.svg" alt="Ethmig" /></a>
    <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">{{ __('Sign in to your account') }}</h2>
  </div>
  <hr class="my-2 max-w-sm mx-auto" />

  @if (session('notVerified'))
      <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-sm">
          <div class="bg-red-50 border-l-4 border-red-400 p-4">
              <div class="flex">
                  <div class="flex-shrink-0">
                      <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                      </svg>
                  </div>
                  <div class="ml-3">
                      <p class="text-sm text-red-700">
                          {{ session('notVerified') }}
                      </p>
                  </div>
              </div>
          </div>
      </div>
  @endif
  @if (session('registered'))
  <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-sm">
    <div class="bg-green-50 border-l-4 border-green-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700">
                    {{ session('registered') }}
                </p>
            </div>
        </div>
    </div>
</div>
  @endif
  <div class="sm:mx-auto sm:w-full sm:max-w-sm">
    <form action="{{ route('login') }}" method="POST">
      @csrf
      <div class="mt-6">
        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">{{ __('E-Mail Address') }}</label>
        <div class="mt-2">
          <input type="email" name="email" id="email" value="{{ old('email') }}" autocomplete="email" required
          class="bg-gray-100 block border border-1 border-gray-100 form-input p-2 rounded-sm shadow-md w-full @error('email') border-red-500 @enderror" />
            @error('email')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
      </div>

      <div class="mt-6">
        <div class="flex items-center justify-between">
          <label for="password" class="block text-sm font-medium leading-6 text-gray-900">{{ __('Password') }}</label>
        </div>
        <div class="mt-2">
          <input type="password" name="password" id="password" autocomplete="current-password" required
          class="bg-gray-100 block border border-1 border-gray-100 form-input p-2 rounded-sm shadow-md w-full @error('password') border-red-500 @enderror" />
            @error('password')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
      </div>

        <div class="flex items-center justify-between mt-6">
            <div class="flex items-center">
                <input id="remember" name="remember" type="checkbox" class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember" class="ml-2 block text-sm leading-5 text-gray-900">{{ __('Remember Me') }}</label>
            </div>

            @if (Route::has('password.request'))
                <div class="text-sm leading-5">
                    <a href="{{ route('password.request') }}" class="font-semibold text-indigo-600 hover:text-indigo-500 focus:outline-none focus:underline transition ease-in-out duration-150">{{ __('Forgot Your Password?') }}</a>
                </div>
            @endif
        </div>

      <div class="mt-6">
        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition duration-150 ease-in-out">{{ __('Login') }}</button>
      </div>
    </form>

    <p class="mt-10 text-center text-sm leading-6 text-gray-500">
      {{ __('Not a member?') }}
      <a href="{{route('register')}}" class="font-semibold text-indigo-600 hover:text-indigo-500 focus:outline-none focus:underline transition ease-in-out duration-150">{{ __('Register here') }}</a>
    </p>
  </div>
</div>
@endsection
