<?php

namespace Donkfather\NovaTableOfContentsField;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class NovaTableOfContentsField extends Field
{
    public $showOnIndex = false;

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'nova-table-of-contents-field';

    protected function fillAttribute(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        return '';
    }
}
