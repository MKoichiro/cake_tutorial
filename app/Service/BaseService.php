<?php
App::uses('PublicError', 'Lib/PublicError');


class BaseService {
  /**
   * @var PublicError|null $lastError 直近のエラー情報。成功した場合は null。
   * @var mixed $lastResult 直近の処理結果。成功した場合はその結果、失敗した場合は null。
   */
  private $lastError;
  private $lastResult;

  public function __construct() {
    $this->setLastError(null);
    $this->setLastResult(null);
  }

  protected function setLastError($type = null, $message = null, $exception = null, $code = null) {
    if ($type === null) {
      $this->lastError = null;
      return;
    }
    $this->lastError = new PublicError($type, $message, $exception, $code);
  }
  public function getLastError($attr = 'message') {
    if ($this->lastError === null) {
      return null;
    }
    switch ($attr) {
      case 'type':
        return $this->lastError->getType();
      case 'message':
        return $this->lastError->getMessage();
      case 'code':
        return $this->lastError->getCode();
      default:
        return $this->lastError;
    }
  }

  protected function setLastResult($result) {
    $this->lastResult = $result;
  }
  public function getLastResult() {
    return $this->lastResult;
  }
}
