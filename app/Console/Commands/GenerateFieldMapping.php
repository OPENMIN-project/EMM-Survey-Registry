<?php

namespace App\Console\Commands;

use App\SurveyField;
use Illuminate\Console\Command;

class GenerateFieldMapping extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ethmig:generate-mapping';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate field mapping for elasticsearch.';

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
     * @return mixed
     */
    public function handle()
    {
        if (SurveyField::query()->count() < 1) {
            $this->error("Survey fields must be present in the database.");
        }

        (new \App\Jobs\GenerateFieldMapping)->handle();

        return 0;
    }
}
