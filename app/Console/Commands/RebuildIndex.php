<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RebuildIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ethmig:rebuild-index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates and rebuilds els index';

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
        $this->call('ethmig:generate-mapping');
        try {
            $this->call('elastic:drop-index', ['index-configurator' => "App\\SurveyIndexConfigurator"]);
        } catch(\Exception $e) {

        }
        $this->call('elastic:create-index', ['index-configurator' => "App\\SurveyIndexConfigurator"]);
        $this->call('scout:import', ['model' => 'App\\Survey']);

        return 0;
    }
}
