<?php
class Validator {
  private $errorMessages;
  private $configs;

  public function __construct() {
    $this->errorMessages = [];
    $this->configs = require __DIR__ . '/configs.php';
  }

  private function setFieldErrorMessages($fieldName, $fieldErrorMessages) {
    $this->errorMessages[$fieldName] = $fieldErrorMessages;
  }

  private function setErrorMessages($formInput, $formName) {
    foreach ($this->configs[$formName] as $fieldName => $configs) {
      $fieldErrorMessages = [];
      foreach ($configs as $config) {
        $checker = $config['checker'];
        if ($checker($formInput, $fieldName)) {
          $fieldErrorMessages[] = $config['message'];
        }

        if (isset($config['exit']) && $config['exit']) {
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

  public function execute($formInput, $formName) {
    $this->setErrorMessages($formInput, $formName);
    $isValid = empty($this->errorMessages);
    return $isValid;
  }
}
