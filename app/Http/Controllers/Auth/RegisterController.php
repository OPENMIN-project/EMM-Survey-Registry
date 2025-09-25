<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Rules\ValidOrcidId;
use App\User;
use App\UserRole;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'country' => ['required', 'string', 'max:255'],
            'orcid' => ['nullable', 'string', new ValidOrcidId, 'unique:users,orcid_id'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'country' => $data['country'],
            'orcid_id' => $data['orcid'] ?? null,
            'role' => UserRole::EDITOR, // Default role for new users
        ]);
    }

    protected function registered(Request $request, $user)
    {
        $this->guard()->logout();
        session()->flash('registered', 'Account created! We\'ve sent you an email to verify your account.');
        return false;
    }

    /**
     * Validate ORCID ID via AJAX request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateOrcid(Request $request)
    {
        $request->validate([
            'orcid' => 'required|string'
        ]);

        $orcid = $request->input('orcid');
        
        // First validate format
        if (!preg_match('/^\d{4}-\d{4}-\d{4}-\d{3}[\dX]$/', $orcid)) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid ORCID format. Use format: 0000-0000-0000-0000'
            ]);
        }

        try {
            // Make request to ORCID API
            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])->get("https://orcid.org/{$orcid}");

            if ($response->successful()) {
                return response()->json([
                    'valid' => true,
                    'message' => 'Valid ORCID ID'
                ]);
            } else {
                return response()->json([
                    'valid' => false,
                    'message' => 'ORCID ID not found. Please check the ID and try again.'
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to validate ORCID ID', [
                'orcid_id' => $orcid,
                'error' => $e->getMessage()
            ]);
            
            // Return valid to avoid blocking registration due to network issues
            return response()->json([
                'valid' => true,
                'message' => 'ORCID ID format is valid (validation service unavailable)'
            ]);
        }
    }
}
