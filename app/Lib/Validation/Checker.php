<?php

class Checker {
  public static function min($length) {
    return fn($formInput, $fieldName) => strlen($formInput[$fieldName]) < $length;
  }
  public static function max($length) {
    return fn($formInput, $fieldName) => strlen($formInput[$fieldName]) > $length;
  }
  public static function notMatch($regExp) {
    return fn($formInput, $fieldName) => !preg_match($regExp, $formInput[$fieldName]);
  }
  public static function notEqualTo($fieldName) {
    return fn($formInput, $fieldName) => $formInput[$fieldName] !== $formInput[$fieldName];
  }
}
