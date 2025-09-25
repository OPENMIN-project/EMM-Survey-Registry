<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ValidOrcidId implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // If the value is empty or null, it's valid (optional field)
        if (empty($value)) {
            return true;
        }

        // First validate the format
        if (!$this->validateFormat($value)) {
            return false;
        }

        try {
            // Make a request to the ORCID API to verify the ID exists
            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])->get("https://orcid.org/{$value}");

            // If the response is successful (200), the ORCID ID exists
            return $response->successful();
        } catch (\Exception $e) {
            // If there's an error making the request, we'll assume it's valid
            // to avoid blocking registration due to network issues
            Log::warning('Failed to validate ORCID ID', [
                'orcid_id' => $value,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function validateFormat($value) {
        return !!preg_match('/^\d{4}-\d{4}-\d{4}-\d{3}[\dX]$/', $value);
    }
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute is not a valid ORCID ID or does not exist.';
    }
} 