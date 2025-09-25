<?php

use App\Survey;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class SurveySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Excel::import(new App\Imports\SurveysImport, base_path('misc/Croatia-subnational-new.csv'));
//        $this->moreSurveys();
    }

    private function moreSurveys()
    {
        $surveys = Survey::take(10)->get();
        $records = $surveys->map(function ($survey) {
            unset($survey->id);
            $survey->answers = json_encode($survey->answers);
            return $survey->toArray();
        });

        $total = collect()->merge($records);

        for ($i = 1; $i <= 200; $i++) {
            if ($i % 100 === 0) {
                Survey::insert($total->toArray());
                echo $i . " - done \n";
                $total = collect();
            }
            $total = $total->merge($records);
        }
        unset($total);
    }
}
