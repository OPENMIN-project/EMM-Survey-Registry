<?php

namespace App\Nova\Fields;

use Illuminate\Support\Str;

class NovaDependencyContainer extends \Epartment\NovaDependencyContainer\NovaDependencyContainer
{
    /**
     * @param mixed $resource
     * @param null $attribute
     */
    public function resolveForDisplay($resource, $attribute = null)
    {
        parent::resolveForDisplay($resource, $attribute);
        foreach ($this->meta['dependencies'] as $index => $dependency) {
            if (array_key_exists('notEmpty', $dependency) && !empty($this->getProperty($resource,
                    $dependency['field']))) {
                $this->meta['dependencies'][$index]['satisfied'] = true;
            }

            if (array_key_exists('value', $dependency) && $dependency['value'] == $this->getProperty($resource,
                    $dependency['field'])) {
                $this->meta['dependencies'][$index]['satisfied'] = true;
            }
        }
    }

    private function getProperty($resource, $property)
    {
        if (Str::contains($property, ['->'])) {
            $parts = explode('->', $property, 2);
            return $this->getProperty($resource->{$parts[0]}, $parts[1]);
        }

        return $resource->$property ?? null;
    }

}
