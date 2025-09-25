<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="/vendor/nova/app.css">
    <link rel="stylesheet" href="{{mix("css/app.css")}}">
</head>
<body class="bg-40 flex items-center justify-center min-h-screen">
<div class="w-full max-w-sm bg-white rounded-lg shadow-md p-8">
    <h1 class="text-2xl text-center font-normal mb-6 text-90">Register</h1>
    <svg class="block mx-auto mb-6" xmlns="http://www.w3.org/2000/svg" width="100" height="2" viewBox="0 0 100 2">
        <path fill="#D8E3EC" d="M0 0h100v2H0z"></path>
    </svg>
    <form action="/register" method="POST" class="space-y-4">
        @csrf()
        <!-- Name -->
        <div class="mb-6">
            <label for="name" class="block font-bold mb-2">Name</label>
            <input type="text" id="name" name="name" required
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
        <!-- Email -->
        <div class="mb-6">
            <label for="email" class="block font-bold mb-2">Email</label>
            <input type="email" id="email" name="email" required
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
        <!-- Password -->
        <div class="mb-6">
            <label for="password" class="block font-bold mb-2">Password</label>
            <input type="password" id="password" name="password" required
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
        <!-- Confirm Password -->
        <div class="mb-6">
            <label for="password_confirmation" class="block font-bold mb-2">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
        <!-- Submit Button -->
        <div>
            <button type="submit"
                    class="w-full btn btn-default btn-primary hover:bg-primary-dark">
                Register
            </button>
        </div>
        <a class="block mt-6 text-center text-sm" href="{{route('nova.login')}}">Go to dashboard</a>
    </form>
</div>
</body>
</html>
