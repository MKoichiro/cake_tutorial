<?php
class Validator {
  private $errorMessages;
  private static $configs;

  public function __construct() {
    $this->errorMessages = [];
    $this->loadConfigs();
  }

  private function loadConfigs() {
    if (is_null(self::$configs)) {
      self::$configs = require APP . 'Lib' . DS . 'Validation' . DS . 'configs.php';
    }
  }

  /**
   * $configs から $targetKey に該当する設定を抽出して返す
   * $targetKey は 'formName' または 'formName.fieldName' の形式を許容する
   */
  private function extractConfigs($targetKey) {
    $keys = explode('.', $targetKey);
    if (count($keys) === 1) {
      $formName = $keys[0];
      if (isset(self::$configs[$formName])) {
        return self::$configs[$formName];
      }
    } else if (count($keys) === 2) {
      $formName  = $keys[0];
      $fieldName = $keys[1];
      if (isset(self::$configs[$formName]) && isset(self::$configs[$formName][$fieldName])) {
        return [ $fieldName => self::$configs[$formName][$fieldName] ];
      }
    }
    return [];
  }

  private function setFieldErrorMessages($fieldName, $fieldErrorMessages) {
    $this->errorMessages[$fieldName] = $fieldErrorMessages;
  }

  private function setErrorMessages($formInput, $targetKey) {
    $configs = $this->extractConfigs($targetKey);
    foreach ($configs as $fieldName => $fieldConfigs) {
      $fieldErrorMessages = [];
      foreach ($fieldConfigs as $fieldConfig) {
        $checker = $fieldConfig['checker'];
        if ($checker($formInput, $fieldName)) {
          $fieldErrorMessages[] = $fieldConfig['message'];
        }

        if (isset($fieldConfig['exit']) && $fieldConfig['exit']) {
          break;
        }
      }
      if (!empty($fieldErrorMessages)) {
        $this->setFieldErrorMessages($fieldName, $fieldErrorMessages);
      }
    }
  }

  public function getErrorMessages() {
    return $this->errorMessages;
  }

  public function execute($formInput, $targetKey) {
    $this->setErrorMessages($formInput, $targetKey);
    return empty($this->errorMessages);
  }
}
