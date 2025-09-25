<?php

namespace App\Console\Commands;

use App\Imports\MultiSheetSurveyImport;
use App\Imports\SurveysImport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use \SplFileInfo;

class SurveyImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ethmig:survey-import {file} {--multiple-sheets}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import surveys from excel file';

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
        /** @var SplFileInfo $file */
        $file = new SplFileInfo($this->argument('file'));

        if (!$file->isFile() || !$file->isReadable()) {
            $this->error("File is not a file or is not readable.");
        }
        DB::beginTransaction();
        try{
            if($this->option('multiple-sheets')){
                Excel::import(new MultiSheetSurveyImport, $file);
            } else {
                Excel::import(new SurveysImport, $file);
            }
        } catch (\Exception $e){
            DB::rollBack();
            $this->error($e->getMessage());
            return 1;
        }
        DB::commit();
        $this->info("Surveys imported successfully");

        return 0;
    }
}
