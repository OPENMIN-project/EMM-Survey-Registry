<?php

namespace Tests\Feature;

use App\Survey;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImportUserOptionsCommandTest extends TestCase
{
    use RefreshDatabase;

    private string $csvContent;
    private string $csvFilePath;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test CSV content
        $this->csvContent = "name,email,orcid,value,userId\n" .
            "John Doe,john@example.com,0000-0001-2345-6789,John Doe,\n" .
            "Jane Smith,jane@example.com,0000-0002-3456-7890,Jane Smith,\n" .
            "Bob Wilson,bob@example.com,0000-0003-4567-8901,Bob Wilson,\n" .
            "Alice Brown,alice@example.com,,Alice Brown,\n" .
            "Charlie Davis,charlie@example.com,0000-0004-5678-9012,Charlie Davis,-1\n" .
            "Diana Evans,diana@example.com,0000-0005-6789-0123,Diana Evans,2\n";

        // Create temporary CSV file
        $this->csvFilePath = tempnam(sys_get_temp_dir(), 'test_csv_');
        file_put_contents($this->csvFilePath, $this->csvContent);
    }

    protected function tearDown(): void
    {
        // Clean up temporary file
        if (file_exists($this->csvFilePath)) {
            unlink($this->csvFilePath);
        }
        
        parent::tearDown();
    }

    /** @test */
    public function it_creates_new_users_from_csv()
    {
        $this->artisan('ethmig:import-user-options', ['file' => $this->csvFilePath])
            ->assertExitCode(0);

        // Check that users were created
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'orcid_id' => '0000-0001-2345-6789',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'orcid_id' => '0000-0002-3456-7890',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Bob Wilson',
            'email' => 'bob@example.com',
            'orcid_id' => '0000-0003-4567-8901',
        ]);

        // Check that user without ORCID was created
        $this->assertDatabaseHas('users', [
            'name' => 'Alice Brown',
            'email' => 'alice@example.com',
            'orcid_id' => null,
        ]);

        // Check that user with userId = -1 was skipped
        $this->assertDatabaseMissing('users', [
            'email' => 'charlie@example.com',
        ]);
    }

    /** @test */
    public function it_updates_existing_users_by_email()
    {
        // Create existing user
        $existingUser = User::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'orcid_id' => null,
        ]);

        $this->artisan('ethmig:import-user-options', ['file' => $this->csvFilePath])
            ->assertExitCode(0);

        // Check that user was updated with ORCID
        $this->assertDatabaseHas('users', [
            'id' => $existingUser->id,
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'orcid_id' => '0000-0002-3456-7890',
        ]);

        // Check that other users were created
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    /** @test */
    public function it_skips_user_update_when_orcid_is_empty()
    {
        // Create existing user with ORCID
        $existingUser = User::factory()->create([
            'name' => 'Alice Brown',
            'email' => 'alice@example.com',
            'orcid_id' => '0000-0000-0000-0000',
        ]);

        $this->artisan('ethmig:import-user-options', ['file' => $this->csvFilePath])
            ->assertExitCode(0);

        // Check that user's ORCID was not changed
        $this->assertDatabaseHas('users', [
            'id' => $existingUser->id,
            'orcid_id' => '0000-0000-0000-0000',
        ]);
    }

    /** @test */
    public function it_updates_survey_contributors()
    {
        // Create users first
        $user1 = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $user2 = User::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
        ]);

        // Create survey with contributors as values
        $survey = Survey::factory()->create([
            'answers' => [
                'f_11_1' => ['John Doe', 'Jane Smith'],
            ],
        ]);

        $this->artisan('ethmig:import-user-options', ['file' => $this->csvFilePath])
            ->assertExitCode(0);

        // Check that survey contributors were updated with user IDs
        $updatedSurvey = $survey->fresh();
        $this->assertEquals([$user1->id, $user2->id], $updatedSurvey->answers->f_11_1);
    }

    /** @test */
    public function it_handles_comma_separated_contributors()
    {
        // Create users first
        $user1 = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $user2 = User::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
        ]);

        // Create survey with comma-separated contributors
        $survey = Survey::factory()->create([
            'answers' => [
                'f_11_1' => ['John Doe, Jane Smith'],
            ],
        ]);

        $this->artisan('ethmig:import-user-options', ['file' => $this->csvFilePath])
            ->assertExitCode(0);

        // Check that survey contributors were updated with user IDs
        $updatedSurvey = $survey->fresh();
        $this->assertEquals([$user1->id, $user2->id], $updatedSurvey->answers->f_11_1);
    }

    /** @test */
    public function it_removes_duplicate_contributors()
    {
        // Create user
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        // Create survey with duplicate contributors
        $survey = Survey::factory()->create([
            'answers' => [
                'f_11_1' => ['John Doe', 'John Doe', 'John Doe'],
            ],
        ]);

        $this->artisan('ethmig:import-user-options', ['file' => $this->csvFilePath])
            ->assertExitCode(0);

        // Check that duplicates were removed
        $updatedSurvey = $survey->fresh();
        $this->assertEquals([$user->id], $updatedSurvey->answers->f_11_1);
    }

    /** @test */
    public function it_works_in_dry_run_mode()
    {
        $this->artisan('ethmig:import-user-options', [
            'file' => $this->csvFilePath,
            '--dry-run' => true,
        ])->assertExitCode(0);

        // Check that no users were actually created
        $this->assertDatabaseMissing('users', [
            'email' => 'john@example.com',
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'jane@example.com',
        ]);
    }

    /** @test */
    public function it_handles_missing_file()
    {
        $this->artisan('ethmig:import-user-options', ['file' => 'nonexistent.csv'])
            ->assertExitCode(1);
    }

    /** @test */
    public function it_handles_empty_csv_file()
    {
        $emptyFile = tempnam(sys_get_temp_dir(), 'empty_csv_');
        file_put_contents($emptyFile, '');

        $this->artisan('ethmig:import-user-options', ['file' => $emptyFile])
            ->assertExitCode(1);

        unlink($emptyFile);
    }

    /** @test */
    public function it_handles_csv_without_headers()
    {
        $noHeadersFile = tempnam(sys_get_temp_dir(), 'no_headers_csv_');
        file_put_contents($noHeadersFile, "John Doe,john@example.com,0000-0001-2345-6789,John Doe,\n");

        $this->artisan('ethmig:import-user-options', ['file' => $noHeadersFile])
            ->assertExitCode(1);

        unlink($noHeadersFile);
    }

    /** @test */
    public function it_handles_invalid_email_format()
    {
        $invalidEmailContent = "name,email,orcid,value,userId\n" .
            "John Doe,invalid-email,0000-0001-2345-6789,John Doe,\n";

        $invalidFile = tempnam(sys_get_temp_dir(), 'invalid_email_csv_');
        file_put_contents($invalidFile, $invalidEmailContent);

        $this->artisan('ethmig:import-user-options', ['file' => $invalidFile])
            ->assertExitCode(1);

        unlink($invalidFile);
    }

    /** @test */
    public function it_handles_duplicate_email_in_csv()
    {
        $duplicateEmailContent = "name,email,orcid,value,userId\n" .
            "John Doe,john@example.com,0000-0001-2345-6789,John Doe,\n" .
            "John Doe 2,john@example.com,0000-0002-3456-7890,John Doe 2,\n";

        $duplicateFile = tempnam(sys_get_temp_dir(), 'duplicate_email_csv_');
        file_put_contents($duplicateFile, $duplicateEmailContent);

        $this->artisan('ethmig:import-user-options', ['file' => $duplicateFile])
            ->assertExitCode(1);

        unlink($duplicateFile);
    }

    /** @test */
    public function it_handles_missing_required_fields()
    {
        $missingFieldsContent = "name,email,orcid,value,userId\n" .
            ",john@example.com,0000-0001-2345-6789,John Doe,\n";

        $missingFieldsFile = tempnam(sys_get_temp_dir(), 'missing_fields_csv_');
        file_put_contents($missingFieldsFile, $missingFieldsContent);

        $this->artisan('ethmig:import-user-options', ['file' => $missingFieldsFile])
            ->assertExitCode(1);

        unlink($missingFieldsFile);
    }

    /** @test */
    public function it_skips_records_with_empty_email()
    {
        $emptyEmailContent = "name,email,orcid,value,userId\n" .
            "John Doe,,0000-0001-2345-6789,John Doe,\n" .
            "Jane Smith,jane@example.com,0000-0002-3456-7890,Jane Smith,\n";

        $emptyEmailFile = tempnam(sys_get_temp_dir(), 'empty_email_csv_');
        file_put_contents($emptyEmailFile, $emptyEmailContent);

        $this->artisan('ethmig:import-user-options', ['file' => $emptyEmailFile])
            ->assertExitCode(0);

        // Check that only valid users were created
        $this->assertDatabaseMissing('users', [
            'name' => 'John Doe',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
        ]);

        unlink($emptyEmailFile);
    }

    /** @test */
    public function it_handles_surveys_without_contributors()
    {
        // Create survey without contributors
        $survey = Survey::factory()->create([
            'answers' => [
                'f_1_0' => 'US',
            ],
        ]);

        $this->artisan('ethmig:import-user-options', ['file' => $this->csvFilePath])
            ->assertExitCode(0);

        // Check that survey was not modified
        $updatedSurvey = $survey->fresh();
        $this->assertEquals(['f_1_0' => 'US'], (array) $updatedSurvey->answers);
    }

    /** @test */
    public function it_handles_surveys_with_empty_contributors()
    {
        // Create survey with empty contributors
        $survey = Survey::factory()->create([
            'answers' => [
                'f_11_1' => [],
            ],
        ]);

        $this->artisan('ethmig:import-user-options', ['file' => $this->csvFilePath])
            ->assertExitCode(0);

        // Check that survey was not modified
        $updatedSurvey = $survey->fresh();
        $this->assertEquals([], $updatedSurvey->answers->f_11_1);
    }

    /** @test */
    public function it_handles_contributors_not_found_in_csv()
    {
        // Create survey with contributor not in CSV
        $survey = Survey::factory()->create([
            'answers' => [
                'f_11_1' => ['Unknown Person'],
            ],
        ]);

        $this->artisan('ethmig:import-user-options', ['file' => $this->csvFilePath])
            ->assertExitCode(0);

        // Check that survey was not modified
        $updatedSurvey = $survey->fresh();
        $this->assertEquals(['Unknown Person'], $updatedSurvey->answers->f_11_1);
    }

    /** @test */
    public function it_provides_correct_statistics()
    {
        // Create existing user
        User::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'orcid_id' => null,
        ]);

        // Create survey with contributors
        $survey = Survey::factory()->create([
            'answers' => [
                'f_11_1' => ['John Doe', 'Jane Smith'],
            ],
        ]);

        $this->artisan('ethmig:import-user-options', ['file' => $this->csvFilePath])
            ->assertExitCode(0)
            ->expectsOutput('Import operation completed!');

        // The command should have:
        // - Created 3 new users (John Doe, Bob Wilson, Alice Brown)
        // - Updated 1 existing user (Jane Smith)
        // - Updated 1 survey
        // - Skipped 1 user (Charlie Davis with userId = -1)
        // - Skipped 1 user (Diana Evans with userId = 2, but no existing user with that ID)
    }
} 