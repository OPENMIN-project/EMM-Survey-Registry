<?php

namespace App\Console\Commands;

use App\Survey;
use App\User;
use App\UserRole;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ImportUserOptions extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ethmig:import-user-options 
                            {file : Path to the CSV file to import}
                            {--dry-run : Run without making changes to the database}';

    /**
     * The console command description.
     */
    protected $description = 'Import user options from a CSV file and update survey contributors';

    /**
     * Statistics for the import operation.
     */
    private array $stats = [
        'users_created' => 0,
        'users_updated' => 0,
        'surveys_updated' => 0,
        'errors' => 0,
    ];

    /**
     * Tracking for values without corresponding users.
     */
    private array $valuesWithoutUsers = [];

    /**
     * Tracking for surveys that were missed or had issues.
     */
    private array $surveysWithIssues = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $filePath = $this->argument('file');
            $isDryRun = $this->option('dry-run');

            $this->validateFile($filePath);
            $this->displayStartMessage($filePath, $isDryRun);

            $records = $this->loadAndParseCsv($filePath);
            $this->info("Found " . count($records) . " records");

            $validRecords = $this->filterValidRecords($records);
            $this->info("Processing " . count($validRecords) . " valid records");

            DB::transaction(function () use ($validRecords, $isDryRun) {
                $this->processUsers($validRecords, $isDryRun);
                $this->processSurveys($validRecords, $isDryRun);
            }, 5); // 5 retries for deadlock handling

            $this->displayResults($isDryRun);
            
            return Command::SUCCESS;
        } catch (ValidationException $e) {
            $this->error("Validation failed: " . $e->getMessage());
            return Command::FAILURE;
        } catch (\Exception $e) {
            $this->error("An error occurred: " . $e->getMessage());
            Log::error('ImportUserOptions failed', [
                'file' => $this->argument('file'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Validate the input file exists and is readable.
     */
    private function validateFile(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("File {$filePath} does not exist");
        }

        if (!is_readable($filePath)) {
            throw new \InvalidArgumentException("File {$filePath} is not readable");
        }
    }

    /**
     * Display the start message for the import operation.
     */
    private function displayStartMessage(string $filePath, bool $isDryRun): void
    {
        $this->info("Importing user options from {$filePath}");
        
        if ($isDryRun) {
            $this->warn("DRY RUN MODE - No changes will be saved to the database");
        }
    }

    /**
     * Load and parse the CSV file.
     */
    private function loadAndParseCsv(string $filePath): Collection
    {
        $csvData = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        if (empty($csvData)) {
            throw new \InvalidArgumentException("CSV file is empty");
        }

        $headers = str_getcsv(array_shift($csvData));
        
        if (empty($headers)) {
            throw new \InvalidArgumentException("CSV file has no headers");
        }

        $records = collect($csvData)->map(function ($row) use ($headers) {
            $rowData = str_getcsv($row);
            
            // Ensure row has same number of columns as headers
            while (count($rowData) < count($headers)) {
                $rowData[] = '';
            }
            
            // Trim all values to remove whitespace
            $trimmedData = array_map('trim', $rowData);
            
            return array_combine($headers, $trimmedData);
        });

        return $records;
    }

    /**
     * Filter out invalid records.
     */
    private function filterValidRecords(Collection $records): Collection
    {
        return $records->filter(function ($record) {
            // Keep records with email (for user creation/update) or with userId = -1 (for removal)
            return !empty($record['email']) || $record['userId'] == -1;
        });
    }

    /**
     * Process users from the records.
     */
    private function processUsers(Collection $records, bool $isDryRun): void
    {
        $progress = $this->output->createProgressBar($records->count());
        $progress->start();

        foreach ($records as $key => $record) {
            try {
                $this->processUserRecord($records, $key, $isDryRun);
            } catch (\Exception $e) {
                $this->stats['errors']++;
                $this->error("Error processing user record: " . $e->getMessage());
                Log::warning('Error processing user record', [
                    'record' => $record,
                    'error' => $e->getMessage(),
                ]);
            }
            
            $progress->advance();
        }

        $progress->finish();
        $this->line('');
    }

    /**
     * Process a single user record.
     */
    private function processUserRecord(Collection $records, int $key, bool $isDryRun): void
    {
        $record = $records->get($key);
        $email = $record['email'] ?? null;

        if (empty($email)) {
            $this->warn("Skipping record with empty email");
            return;
        }

        // Check if user exists by email
        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            $this->line("Found existing user: {$existingUser->name} ({$email})");
            $this->updateExistingUserByEmail($records, $key, $record, $existingUser, $isDryRun);
        } else {
            $this->line("Creating new user: {$record['name']} ({$email})");
            $this->createNewUser($records, $key, $record, $isDryRun);
        }
    }

    /**
     * Create a new user.
     */
    private function createNewUser(Collection $records, int $key, array $record, bool $isDryRun): void
    {
        $userData = [
            'name' => trim($record['name'] ?? ''),
            'email' => trim($record['email'] ?? ''),
            'orcid_id' => !empty($record['orcid']) ? trim($record['orcid']) : null,
            'password' => bcrypt(Str::random(12)),
            'role' => UserRole::EDITOR,
        ];

        // Validate user data
        $validator = Validator::make($userData, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'orcid_id' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            $this->error("Validation failed for user {$userData['name']}: " . implode(', ', $validator->errors()->all()));
            throw new ValidationException($validator);
        }

        if (!$isDryRun) {
            try {
                $user = User::create($userData);
                // Update the record with the new user ID for later use in survey processing
                $records->put($key, array_merge($record, ['userId' => $user->id]));
                $this->stats['users_created']++;
                $this->line("Created user: {$userData['name']} (ID: {$user->id})");
            } catch (\Exception $e) {
                $this->error("Failed to create user {$userData['name']}: " . $e->getMessage());
                Log::error('Failed to create user', [
                    'userData' => $userData,
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }
        }
    }

    /**
     * Update an existing user by email.
     */
    private function updateExistingUserByEmail(Collection $records, int $key, array $record, User $user, bool $isDryRun): void
    {
        $orcidId = $record['orcid'] ?? null;
        
        // Skip update if ORCID ID is empty
        if (empty($orcidId)) {
            return;
        }

        $updateData = [
            'orcid_id' => $orcidId,
        ];

        // Validate update data
        $validator = Validator::make($updateData, [
            'orcid_id' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            $this->error("Validation failed for user update {$user->name}: " . implode(', ', $validator->errors()->all()));
            throw new ValidationException($validator);
        }

        if (!$isDryRun) {
            try {
                $user->update($updateData);
                // Update the record with the existing user ID for later use in survey processing
                $records->put($key, array_merge($record, ['userId' => $user->id]));
                $this->stats['users_updated']++;
                $this->line("Updated user: {$user->name} with ORCID: {$updateData['orcid_id']}");
            } catch (\Exception $e) {
                $this->error("Failed to update user {$user->name}: " . $e->getMessage());
                Log::error('Failed to update user', [
                    'userId' => $user->id,
                    'updateData' => $updateData,
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }
        }
    }

    /**
     * Process surveys to update contributors.
     */
    private function processSurveys(Collection $records, bool $isDryRun): void
    {
        $this->info("Processing surveys...");
        
        $surveys = Survey::all();
        $progress = $this->output->createProgressBar($surveys->count());
        $progress->start();

        foreach ($surveys as $survey) {
            try {
                $this->processSurveyContributors($survey, $records, $isDryRun);
            } catch (\Exception $e) {
                $this->stats['errors']++;
                $this->error("Error processing survey {$survey->id}: " . $e->getMessage());
                Log::warning('Error processing survey', [
                    'survey_id' => $survey->id,
                    'error' => $e->getMessage(),
                ]);
            }
            
            $progress->advance();
        }

        $progress->finish();
        $this->line('');
    }

    /**
     * Process contributors for a single survey.
     */
    private function processSurveyContributors(Survey $survey, Collection $records, bool $isDryRun): void
    {
        $contributors = $survey->answers->f_11_1 ?? [];
        
        if (empty($contributors)) {
            return;
        }

        $currentContributors = [];
        $contributorsToRemove = [];
        $contributorsToAdd = [];
        $hasChanges = false;
        $valuesWithoutUsers = [];

        // First, collect all current contributors as values (not IDs)
        foreach ((array) $contributors as $contributor) {
            $values = explode(',', $contributor);
            
            foreach ($values as $value) {
                $value = trim($value);
                
                if (!empty($value)) {
                    $currentContributors[] = $value;
                }
            }
        }

        // Process records to determine what to add and what to remove
        foreach ($records as $record) {
            $value = trim($record['value'] ?? '');
            
            if (empty($value)) {
                continue;
            }

            // Split value by comma to handle multiple values
            $values = array_map('trim', explode(',', $value));
            
            foreach ($values as $singleValue) {
                if (empty($singleValue)) {
                    continue;
                }

                // Check if this value exists in current contributors
                $valueExists = in_array($singleValue, $currentContributors);

                if ($record['userId'] == -1) {
                    // Remove this value from contributors
                    if ($valueExists) {
                        $contributorsToRemove[] = $singleValue;
                        $hasChanges = true;
                    }
                } else {
                    // Add this value as a user ID
                    if ($valueExists && !empty($record['userId'])) {
                        $contributorsToAdd[] = $record['userId'];
                        $hasChanges = true;
                    }
                }
            }
        }

        if ($hasChanges) {
            // Remove values that should be removed
            $updatedContributors = array_values(array_diff($currentContributors, $contributorsToRemove));
            
            // Replace remaining values with user IDs where possible
            $finalContributors = [];
            $processedUserIds = []; // Track processed user IDs to avoid duplicates
            
            foreach ($updatedContributors as $contributor) {
                $matchingRecord = $records->first(function ($record) use ($contributor) {
                    $recordValues = array_map('trim', explode(',', trim($record['value'] ?? '')));
                    return in_array($contributor, $recordValues) && $record['userId'] != -1;
                });

                if ($matchingRecord && !empty($matchingRecord['userId'])) {
                    $userId = $matchingRecord['userId'];
                    
                    // Only add if this user ID hasn't been processed yet
                    if (!in_array($userId, $processedUserIds)) {
                        $finalContributors[] = $userId;
                        $processedUserIds[] = $userId;
                    }
                } else {
                    // Track values without corresponding users
                    $valuesWithoutUsers[] = $contributor;
                }
            }

            // Remove duplicates and reindex array (additional safety)
            $uniqueContributors = array_values(array_unique($finalContributors));
            
            $this->line("Would update survey {$survey->id} contributors: " . implode(', ', $uniqueContributors) . ($isDryRun ? " (dry run)" : ""));

            if (!$isDryRun) {
                $answers = $survey->answers;
                $answers->f_11_1 = $uniqueContributors;
                $survey->answers = $answers;
                $survey->save();
                $this->stats['surveys_updated']++;
            }
        } else {
            // Check if there are any values that should have been processed but weren't
            $unprocessedValues = [];
            foreach ($currentContributors as $contributor) {
                $matchingRecord = $records->first(function ($record) use ($contributor) {
                    $recordValues = array_map('trim', explode(',', trim($record['value'] ?? '')));
                    return in_array($contributor, $recordValues);
                });

                if (!$matchingRecord) {
                    $unprocessedValues[] = $contributor;
                }
            }

            if (!empty($unprocessedValues)) {
                $this->surveysWithIssues[] = [
                    'survey_id' => $survey->id,
                    'issue' => 'unprocessed_values',
                    'values' => $unprocessedValues,
                ];
            }
        }

        // Track values without corresponding users for this survey
        if (!empty($valuesWithoutUsers)) {
            $this->valuesWithoutUsers[] = [
                'survey_id' => $survey->id,
                'values' => $valuesWithoutUsers,
            ];
        }
    }

    /**
     * Display the final results of the import operation.
     */
    private function displayResults(bool $isDryRun): void
    {
        $this->line('');
        $this->info("Import " . ($isDryRun ? "simulation" : "operation") . " completed!");
        
        $this->table(
            ['Metric', 'Count'],
            [
                ['Users created', $this->stats['users_created']],
                ['Users updated', $this->stats['users_updated']],
                ['Surveys updated', $this->stats['surveys_updated']],
                ['Errors encountered', $this->stats['errors']],
            ]
        );

        // Report values without corresponding users
        if (!empty($this->valuesWithoutUsers)) {
            $this->line('');
            $this->warn("Values without corresponding users found:");
            foreach ($this->valuesWithoutUsers as $item) {
                $this->line("Survey {$item['survey_id']}: " . implode(', ', $item['values']));
            }
        }

        // Report surveys with issues
        if (!empty($this->surveysWithIssues)) {
            $this->line('');
            $this->warn("Surveys with unprocessed values:");
            foreach ($this->surveysWithIssues as $item) {
                if ($item['issue'] === 'unprocessed_values') {
                    $this->line("Survey {$item['survey_id']}: " . implode(', ', $item['values']));
                }
            }
        }

        if ($isDryRun) {
            $this->line('');
            $this->warn("This was a dry run - no changes were made to the database");
            $this->info("Reports show what would happen if the command was run normally.");
        }

        if ($this->stats['errors'] > 0) {
            $this->warn("{$this->stats['errors']} errors were encountered during the import. Check the logs for details.");
        }
    }
}
