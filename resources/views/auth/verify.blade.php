@extends('layouts.app')

@section('content')
<div class="bg-white flex flex-col justify-center lg:px-8 max-w-lg mx-auto px-6 py-12 rounded-lg shadow-lg">
  <div class="sm:mx-auto sm:w-full sm:max-w-sm">
    <a href="{{ config('app.url') }}"><img class="mx-auto h-20 w-auto" src="/images/logo.svg" alt="Ethmig" /></a>
    <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">{{ __('Verify Your Email Address') }}</h2>
  </div>
  <hr class="my-2 max-w-sm mx-auto" />

  <div class="sm:mx-auto sm:w-full sm:max-w-sm">
    @if (session('resent'))
      <div class="mt-6">
        <div class="bg-green-50 border-l-4 border-green-400 p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm text-green-700">
                {{ __('A fresh verification link has been sent to your email address.') }}
              </p>
            </div>
          </div>
        </div>
      </div>
    @endif

    <p class="mt-6 text-center text-sm text-gray-600">
      {{ __('Before proceeding, please check your email for a verification link.') }}
    </p>
    <p class="mt-2 text-center text-sm text-gray-600">
      {{ __('If you did not receive the email') }},
      <form class="inline" method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <button type="submit" class="font-semibold text-indigo-600 hover:text-indigo-500 focus:outline-none focus:underline transition ease-in-out duration-150">{{ __('click here to request another') }}</button>.
      </form>
    </p>
  </div>
</div>
@endsection
