<?php

class Checker {
  public static function isValidArgs($formInput, $fieldName, $functionName = '') {
    if (!isset($formInput[$fieldName]) || !is_string($formInput[$fieldName])) {
      CakeLog::write('error', 'Invalid arguments: ' . print_r($formInput, true) . ', fieldName: ' . $fieldName . ', functionName: ' . $functionName);
      return false;
    }
    return true;
  }

  public static function min($length) {
    $functionName = __FUNCTION__;
    return function($formInput, $fieldName) use ($length, $functionName) {
      if (!self::isValidArgs($formInput, $fieldName, $functionName)) {
        return true;
      }
      return mb_strlen($formInput[$fieldName]) < $length;
    };
  }
  public static function max($length) {
    $functionName = __FUNCTION__;
    return function($formInput, $fieldName) use ($length, $functionName) {
      if (!self::isValidArgs($formInput, $fieldName, $functionName)) {
        return true;
      }
      return mb_strlen($formInput[$fieldName]) > $length;
    };
  }
  public static function notMatch($regExp) {
    $functionName = __FUNCTION__;
    return function($formInput, $fieldName) use ($regExp, $functionName) {
      if (!self::isValidArgs($formInput, $fieldName, $functionName)) {
        return true;
      }
      return !preg_match($regExp, $formInput[$fieldName]);
    };
  }
  public static function notEqualTo($anotherFieldName) {
    $functionName = __FUNCTION__;
    return function($formInput, $fieldName) use ($anotherFieldName, $functionName) {
      if (!self::isValidArgs($formInput, $fieldName, $functionName)) {
        return true;
      }
      return $formInput[$fieldName] !== $formInput[$anotherFieldName];
    };
  }
  public static function notBlank() {
    $functionName = __FUNCTION__;
    return function($formInput, $fieldName) use ($functionName) {
      if (!self::isValidArgs($formInput, $fieldName, $functionName)) {
        return true;
      }
      return trim($formInput[$fieldName]) === '';
    };
  }
}
