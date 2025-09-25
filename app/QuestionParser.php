<?php
namespace App;

class QuestionParser
{

    /**
     * @var string
     */
    private $question;
    private $code = "";
    private $name = "";

    private $pattern = "/^([\da-z\.]+)/";

    /**
     * QuestionParser constructor.
     * @param string $question
     */
    public function __construct(string $question)
    {
        $this->question = $question;

        $matches = [];
        preg_match($this->pattern, $question, $matches);
        if(count($matches) > 0){
            $this->code = trim($matches[0],'.');
        }
        $label = preg_replace($this->pattern, "", $question);
        $this->name = $label;
    }

    public function getQuestion()
    {
        return $this->question;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getFieldCode()
    {
        return 'f_' . str_replace('.', '_', $this->code);
    }
}