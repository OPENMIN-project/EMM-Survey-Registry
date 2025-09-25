@extends('layouts.app')

@section('content')
<div class="bg-white flex flex-col justify-center lg:px-8 max-w-lg mx-auto px-6 py-12 rounded-lg shadow-lg">
  <div class="sm:mx-auto sm:w-full sm:max-w-sm">
    <a href="{{ config('app.url') }}"><img class="mx-auto h-20 w-auto" src="/images/logo.svg" alt="Ethmig" /></a>
    <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">{{ __('Reset Password') }}</h2>
  </div>
  <hr class="my-2 max-w-sm mx-auto" />

  @if (session('status'))
    <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-sm">
      <div class="bg-green-50 border-l-4 border-green-400 p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-green-700">
              {{ session('status') }}
            </p>
          </div>
        </div>
      </div>
    </div>
    <div class="mt-6">
      <a href="{{ route('login') }}" class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition duration-150 ease-in-out">
        {{ __('Back to login') }}
      </a>
    </div>
  @else

  <div class="sm:mx-auto sm:w-full sm:max-w-sm">
    <form action="{{ route('password.email') }}" method="POST">
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
        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition duration-150 ease-in-out">{{ __('Send Password Reset Link') }}</button>
      </div>
    </form>
  </div>
</div>
@endif
@endsection
