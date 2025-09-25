<?php

namespace Tests\Unit;

use App\AnswerParser;
use App\Enum\FieldType;
use App\SurveyField;
use Tests\TestCase;

class AnswerParserTest extends TestCase
{

    /** @test */
     public function it_handles_numbers()
     {
        $text = "5.63";
        $field = factory(SurveyField::class)->create(['type' => FieldType::TEXT]);
        $parser = new AnswerParser($text, $field);

        $this->assertEquals($text, $parser->getValue());
     }
}
