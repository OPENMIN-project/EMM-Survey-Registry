<?php


use App\OptionsRepository;

if (!function_exists('field_value_asString')) {
  function field_value_asString($survey, $field_code, $default = '-') : string
  {
    if (is_array($field_code)) {
      $field_code = $field_code['field_code'];
    }

    return OptionsRepository::field($field_code, data_get($survey, "answers.{$field_code}", $default));
  }
}


if (!function_exists('field_value')) {
  function field_value($survey, $field_code, $default = null)
  {
    if (is_array($field_code)) {
      $field_code = $field_code['field_code'];
    }

    return OptionsRepository::field_raw($field_code, data_get($survey, "answers.{$field_code}", $default));
  }
}

if (!function_exists('field_value_raw')) {
  function field_value_raw($survey, $field_code, $default = null)
  {
    if (is_array($field_code)) {
      $field_code = $field_code['field_code'];
    }

    return data_get($survey, "answers.{$field_code}", $default);
  }
}
