@extends('layouts.app')

@section('content')
<div class="bg-white flex flex-col justify-center lg:px-8 max-w-lg mx-auto px-6 py-12 rounded-lg shadow-lg">
  <div class="sm:mx-auto sm:w-full sm:max-w-sm">
    <img class="mx-auto h-20 w-auto" src="/images/logo.svg" alt="Ethmig" />
    <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">{{ __('Create a new account') }}</h2>
  </div>

  <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
    <form action="{{ route('register') }}" method="POST">
        @csrf

      <div class="mt-6">
        <label for="name" class="block text-sm font-medium leading-6 text-gray-900">{{ __('Name') }}</label>
        <div class="mt-2">
          <input type="text" name="name" id="name" value="{{ old('name') }}" autocomplete="name" required autofocus
           class="bg-gray-100 block border border-1 border-gray-100 form-input p-2 rounded-sm shadow-md w-full @error('name') border-red-500 @enderror" />
            @error('name')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
      </div>

      <div class="mt-6">
        <label for="country" class="block text-sm font-medium leading-6 text-gray-900">{{ __('Country') }}</label>
        <div class="mt-2">
          <select name="country" id="country" required class="bg-gray-100 block border border-1 border-gray-100 form-input p-2 rounded-sm shadow-md w-full @error('country') border-red-500 @enderror">
            <option value="">{{ __('Select a country') }}</option>
            @foreach(\App\Country::all() as $country)
              <option value="{{ $country->code }}" {{ old('country') == $country->code ? 'selected' : '' }}>
                {{ $country->label }}
              </option>
            @endforeach
          </select>
          @error('country')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>
      </div>
      <div class="mt-6">
        <label for="orcid" class="block text-sm font-medium leading-6 text-gray-900">{{ __('ORCID iD') }} ({{ __('optional') }})</label>
        <div class="mt-2">
          <input type="text" name="orcid" id="orcid" value="{{ old('orcid') }}" pattern="^\d{4}-\d{4}-\d{4}-\d{3}[\dX]$" placeholder="0000-0000-0000-0000"
           class="bg-gray-100 block border border-1 border-gray-100 form-input p-2 rounded-sm shadow-md w-full @error('orcid') border-red-500 @enderror" />
          @error('orcid')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
          @enderror
          <p class="mt-1 text-xs text-gray-500">{{ __('Format: 0000-0000-0000-0000') }}</p>
          <div id="orcid-validation" class="mt-1 text-xs hidden">
            <div id="orcid-loading" class="text-blue-600 hidden">
              <svg class="animate-spin -ml-1 mr-2 h-4 w-4 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ __('Validating ORCID ID...') }}
            </div>
            <div id="orcid-success" class="text-green-600 hidden">
              <svg class="h-4 w-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
              </svg>
              {{ __('Valid ORCID ID') }}
            </div>
            <div id="orcid-error" class="text-red-600 hidden">
              <svg class="h-4 w-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
              </svg>
              <span id="orcid-error-text"></span>
            </div>
          </div>
        </div>
      </div>
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
        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">{{ __('Password') }}</label>
        <div class="mt-2 relative">
          <input type="password" name="password" id="password" autocomplete="new-password" required
           class="bg-gray-100 block border border-1 border-gray-100 form-input p-2 rounded-sm shadow-md w-full @error('password') border-red-500 @enderror" />
          <button type="button" onclick="togglePassword()" class="absolute right-0 mr-2 top-0 mt-3 -translate-y-1/2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-500" id="password-icon">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </button>
            @error('password')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
      </div>

      <script>
        function togglePassword() {
          const password = document.getElementById('password');
          const icon = document.getElementById('password-icon');
          password.type = password.type === 'password' ? 'text' : 'password';
          icon.setAttribute('fill', password.type === 'text' ? 'text' : 'none');
        }

        // ORCID validation
        let orcidValidationTimeout;
        const orcidInput = document.getElementById('orcid');
        const orcidValidation = document.getElementById('orcid-validation');
        const orcidLoading = document.getElementById('orcid-loading');
        const orcidSuccess = document.getElementById('orcid-success');
        const orcidError = document.getElementById('orcid-error');
        const orcidErrorText = document.getElementById('orcid-error-text');

        function validateOrcidFormat(orcid) {
          const orcidPattern = /^\d{4}-\d{4}-\d{4}-\d{3}[\dX]$/;
          return orcidPattern.test(orcid);
        }

        function showOrcidValidation() {
          orcidValidation.classList.remove('hidden');
        }

        function hideOrcidValidation() {
          orcidValidation.classList.add('hidden');
          orcidLoading.classList.add('hidden');
          orcidSuccess.classList.add('hidden');
          orcidError.classList.add('hidden');
        }

        function showOrcidLoading() {
          orcidLoading.classList.remove('hidden');
          orcidSuccess.classList.add('hidden');
          orcidError.classList.add('hidden');
        }

        function showOrcidSuccess() {
          orcidLoading.classList.add('hidden');
          orcidSuccess.classList.remove('hidden');
          orcidError.classList.add('hidden');
          orcidInput.classList.remove('border-red-500');
          orcidInput.classList.add('border-green-500');
        }

        function showOrcidError(message) {
          orcidLoading.classList.add('hidden');
          orcidSuccess.classList.add('hidden');
          orcidError.classList.remove('hidden');
          orcidErrorText.textContent = message;
          orcidInput.classList.remove('border-green-500');
          orcidInput.classList.add('border-red-500');
        }

        async function validateOrcid() {
          const orcid = orcidInput.value.trim();
          
          // Hide validation if empty
          if (!orcid) {
            hideOrcidValidation();
            orcidInput.classList.remove('border-red-500', 'border-green-500');
            return;
          }

          showOrcidValidation();

          // First validate format
          if (!validateOrcidFormat(orcid)) {
            showOrcidError('{{ __("Invalid ORCID format. Use format: 0000-0000-0000-0000") }}');
            return;
          }

          // Show loading state
          showOrcidLoading();

          // Validate existence via backend API
          try {
            const response = await fetch('{{ route("validate.orcid") }}', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
              },
              body: JSON.stringify({ orcid: orcid }),
            });
            
            if (!response.ok) {
              throw new Error('Network response was not ok');
            }
            
            const data = await response.json();
            
            if (data.valid) {
              showOrcidSuccess();
            } else {
              showOrcidError(data.message || '{{ __("ORCID ID not found. Please check the ID and try again.") }}');
            }
          } catch (error) {
            console.warn('Failed to validate ORCID ID:', error);
            // Show a warning but don't block the form
            showOrcidError('{{ __("Validation service unavailable. You can still proceed with registration.") }}');
          }
        }

        // Debounced validation
        orcidInput.addEventListener('input', function() {
          clearTimeout(orcidValidationTimeout);
          orcidValidationTimeout = setTimeout(validateOrcid, 500);
        });

        // Validate on blur
        orcidInput.addEventListener('blur', function() {
          clearTimeout(orcidValidationTimeout);
          validateOrcid();
        });

        // Validate on page load if there's a value
        if (orcidInput.value.trim()) {
          validateOrcid();
        }
      </script>
      <div class="mt-6">
        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition duration-150 ease-in-out">{{ __('Register') }}</button>
      </div>
    </form>

    <p class="mt-10 text-center text-sm leading-6 text-gray-500">
      {{ __('Already a member?') }}
      <a href="{{route('login')}}" class="font-semibold text-indigo-600 hover:text-indigo-500 focus:outline-none focus:underline transition ease-in-out duration-150">{{ __('Sign in') }}</a>
    </p>
  </div>
</div>
@endsection
