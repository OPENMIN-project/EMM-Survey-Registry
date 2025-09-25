<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Excel;
use Spatie\SimpleExcel\SimpleExcelReader;
use SplFileInfo;

class UpdateUsersOrcidId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ethmig:update-batch-orcid-ids {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import file to update batch users orcid id\'s';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $file = new SplFileInfo($this->argument('file'));

        if (!$file->isFile() || !$file->isReadable()) {
            $this->error("File is not a file or is not readable.");
        }

        if ($file->getExtension() !== 'csv') {
            $this->error('File is not in csv format.');
        }

        $this->info('Importing file: ' . $file->getFilename());

        $rows = SimpleExcelReader::create($file->getRealPath())->getRows();

        $values = $rows->values()->mapWithKeys(function (array $rowProperties) {
            $orcidId = $rowProperties['orcid_id'] ?? null;
            $email = $rowProperties['email'] ?? null;

            if ($orcidId && $email) {
                return [Str::lower($email) => $orcidId];
            }

            return [];
        })->collect();

        $users = User::whereIn('email', $values->keys())->get();

        if ($users->isEmpty()) {
            $this->error('No users found with the provided email addresses.');
            return 1;
        }
        $this->info('Found ' . $users->count() . ' users to update.');
        $this->info($users->pluck('orcid_id', 'email'));
        $this->info('Updating users with ORCID IDs...');

        $users->each(function (User $user) use ($values) {
            $orcidId = $values->get(Str::lower($user->email));
            if (!$orcidId) {
                $this->warn("No ORCID ID found for user: {$user->name}");
                return;
            }
            $user->update([
                'orcid_id' => $orcidId,
            ]);
            $this->info("Updated user: {$user->name} with ORCID ID: {$orcidId}");
        });

        $this->info('ORCID ID Import completed.');

        return 0;
    }
}
