<?php

namespace Tests\Feature;

use App\Rules\ValidOrcidId;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OrcidValidationTest extends TestCase
{
    /** @test */
    public function it_validates_orcid_format()
    {
        Http::fake([
            'https://orcid.org/0000-0000-0000-0000' => Http::response([], 200),
            'https://orcid.org/0000-0000-0000-000X' => Http::response([], 200),
        ]);

        $rule = new ValidOrcidId();

        // Valid ORCID format
        $this->assertTrue($rule->passes('orcid', '0000-0000-0000-0000'));
        $this->assertTrue($rule->passes('orcid', '0000-0000-0000-000X'));

        // Invalid ORCID format
        $this->assertFalse($rule->passes('orcid', 'invalid-format'));
        $this->assertFalse($rule->passes('orcid', '0000-0000-0000-000'));
        $this->assertFalse($rule->passes('orcid', '0000-0000-0000-00000'));
    }

    /** @test */
    public function it_accepts_empty_orcid_values()
    {
        $rule = new ValidOrcidId();

        $this->assertTrue($rule->passes('orcid', ''));
        $this->assertTrue($rule->passes('orcid', null));
    }

    /** @test */
    public function it_validates_orcid_existence_via_api()
    {
        Http::fake([
            'https://orcid.org/0000-0000-0000-0000' => Http::response([], 200),
            'https://orcid.org/0000-0000-0000-0001' => Http::response([], 404),
        ]);

        $rule = new ValidOrcidId();

        // Existing ORCID ID
        $this->assertTrue($rule->passes('orcid', '0000-0000-0000-0000'));

        // Non-existing ORCID ID
        $this->assertFalse($rule->passes('orcid', '0000-0000-0000-0001'));
    }

    /** @test */
    public function it_handles_api_errors_gracefully()
    {
        Http::fake([
            'https://orcid.org/0000-0000-0000-0000' => Http::response([], 500),
        ]);

        $rule = new ValidOrcidId();

        // Should return true when API is down to avoid blocking registration
        $this->assertTrue($rule->passes('orcid', '0000-0000-0000-0000'));
    }

    /** @test */
    public function it_returns_proper_error_message()
    {
        $rule = new ValidOrcidId();

        $this->assertEquals(
            'The orcid is not a valid ORCID ID or does not exist.',
            $rule->message()
        );
    }
} 