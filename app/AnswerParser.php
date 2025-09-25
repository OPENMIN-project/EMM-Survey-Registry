<?php

namespace App;

use Illuminate\Contracts\Support\Arrayable;

class AnswerParser implements Arrayable
{

    /**
     * @var string
     */
    private $answer;
    private $code = null;
    private $name = null;

    private $pattern = "/^([-\d\.]+(?=\.[\sA-Za-z]+)|[A-Z]{2}(?=\s\())+/";
    /**
     * @var SurveyField
     */
    private $field;

    /**
     * QuestionParser constructor.
     * @param string $answer
     * @param SurveyField $field
     */
    public function __construct(string $answer, SurveyField $field)
    {
        $this->answer = $answer;
        $this->field = $field;

        if (is_numeric($answer)) {
            $this->code = $answer;
            $this->name = $answer;
        } else {
            $matches = [];
            preg_match($this->pattern, $answer, $matches);
            if (count($matches) > 0) {
                $this->code = trim($matches[0], '. ');
            }
            $label = preg_replace($this->pattern, "", $answer);
            $this->name = trim($label, '. ');
        }
    }

    public function getAnswer()
    {
        return $this->answer;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getValue()
    {
        if ($this->field->type === 'choice') {
            return $this->getCode();
        }

        return $this->getName();
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'code'   => $this->getCode(),
            'answer' => $this->getAnswer(),
            'value'  => $this->getValue(),
            'name'   => $this->getName()
        ];
    }
}
