<?php

namespace App\Traits;

use Illuminate\Support\Arr;

trait HasValidationRules
{
    public function getValidationRules($attributes = null)
    {
        $rules = $this->validationRules();

        // no list is provided
        if (!$attributes)
            return $rules;

        // a single attribute is provided
        if (!is_array($attributes))
            return [$attributes => Arr::get($rules, $attributes, '')];

        // a list of attributes is provided
        $newRules = [];
        foreach ($attributes as $attr)
            $newRules[$attr] = Arr::get($rules, $attr, '');
        return $newRules;
    }
}
