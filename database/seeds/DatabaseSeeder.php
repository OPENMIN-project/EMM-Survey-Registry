<?php

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        Excel::import(new App\Imports\SurveyFieldsImport, base_path('misc/Survey-fields_1.7a.xlsx'));
        $this->call(SurveySeeder::class);
    }
}
