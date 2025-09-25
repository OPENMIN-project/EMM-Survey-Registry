<?php

namespace App\Http\Controllers;

use App\FieldGroup;
use App\Survey;
use App\Enum\SurveyStatus;
use App\SurveyField;
use Illuminate\Http\Request;


class SurveysController extends Controller
{
    public function index(Request $request)
    {
        return view('surveys');
    }

    public function show(Request $request, $id)
    {
        $survey = Survey::whereStatus(SurveyStatus::PUBLISHED)->findOrFail($id);
        $fieldHeadings = SurveyField::headings()->with('displayFields', 'displayFields.displayFields')->get()
            ->map->only('name', 'field_code', 'code', 'id', 'displayFields');

        return view('survey', compact('survey', 'fieldHeadings'));
    }
}
