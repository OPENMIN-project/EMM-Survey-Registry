<?php

use App\Http\Controllers\SurveysController;
use App\User;
use App\UserRole;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use const App\Http\Controllers\Auth\LoginController;

Route::redirect('/', '/surveys');

Route::get('/surveys', [SurveysController::class, 'index']);
Route::get('/surveys/{id}', [SurveysController::class, 'show']);

Route::get('/storage/avatars/{filename}', function (Request $request, $filename) {
    abort_unless(Storage::disk('avatars')->exists($filename), 404);

    return response()->file(Storage::disk('avatars')->path($filename));
})->middleware('auth');
Route::get('download', function (Request $request) {
    $data = $request->validate([
        'path'     => 'required',
        'filename' => 'required',
    ]);

    $decryptedPath = decrypt($data['path']);

    if(!file_exists($decryptedPath)) {
        abort(404);
    }

    return response()->download(
        $decryptedPath,
        $data['filename']
    );
})->name('export-csv.download')->middleware(ValidateSignature::class);

Route::get('/login', 'LoginController@showLoginForm')
    ->name('auth.login')
    ->middleware('nova.guest');

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');
Route::post('validate-orcid', 'Auth\RegisterController@validateOrcid')->name('validate.orcid');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

// Confirm Password (added in v6.2)
Route::get('password/confirm', 'Auth\ConfirmPasswordController@showConfirmForm')->name('password.confirm');
Route::post('password/confirm', 'Auth\ConfirmPasswordController@confirm');

// Email Verification Routes...
Route::get('email/verify', 'Auth\VerificationController@show')->name('verification.notice');
Route::get('email/verify/{id}/{hash}', 'Auth\VerificationController@verify')->name('verification.verify'); // v6.x
/* Route::get('email/verify/{id}', 'Auth\VerificationController@verify')->name('verification.verify'); // v5.x */
Route::get('email/resend', 'Auth\VerificationController@resend')->name('verification.resend');
