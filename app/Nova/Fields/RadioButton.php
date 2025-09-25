<?php

namespace App\Nova\Fields;

class RadioButton extends \OwenMelbz\RadioField\RadioButton
{
    public function displayUsingLabels()
    {
        return $this;
    }
}
