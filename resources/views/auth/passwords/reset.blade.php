@extends('layouts.app')

@section('content')
<div class="bg-white flex flex-col justify-center lg:px-8 max-w-lg mx-auto px-6 py-12 rounded-lg shadow-lg">
  <div class="sm:mx-auto sm:w-full sm:max-w-sm">
    <a href="{{ config('app.url') }}"><img class="mx-auto h-20 w-auto" src="/images/logo.svg" alt="Ethmig" /></a>
    <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">{{ __('Reset Password') }}</h2>
  </div>
  <hr class="my-2 max-w-sm mx-auto" />

  <div class="sm:mx-auto sm:w-full sm:max-w-sm">
    <form action="{{ route('password.update') }}" method="POST">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">

      <div class="mt-6">
        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">{{ __('E-Mail Address') }}</label>
        <div class="mt-2">
          <input type="email" name="email" id="email" value="{{ $email ?? old('email') }}" autocomplete="email" required
                 class="bg-gray-100 block border border-1 border-gray-100 form-input p-2 rounded-sm shadow-md w-full @error('email') border-red-500 @enderror" />
          @error('email')
          <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>
      </div>

      <div class="mt-6">
        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">{{ __('Password') }}</label>
        <div class="mt-2">
          <input type="password" name="password" id="password" autocomplete="new-password" required
                 class="bg-gray-100 block border border-1 border-gray-100 form-input p-2 rounded-sm shadow-md w-full @error('password') border-red-500 @enderror" />
          @error('password')
          <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>
      </div>

      <div class="mt-6">
        <label for="password-confirm" class="block text-sm font-medium leading-6 text-gray-900">{{ __('Confirm Password') }}</label>
        <div class="mt-2">
          <input type="password" name="password_confirmation" id="password-confirm" autocomplete="new-password" required
                 class="bg-gray-100 block border border-1 border-gray-100 form-input p-2 rounded-sm shadow-md w-full" />
        </div>
      </div>

      <div class="mt-6">
        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition duration-150 ease-in-out">{{ __('Reset Password') }}</button>
      </div>
    </form>
  </div>
</div>
@endsection
