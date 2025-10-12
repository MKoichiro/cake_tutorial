<?php

App::uses('Thread', 'Model');
App::uses('BaseService', 'Service');

class ThreadService extends BaseService {
  private $threadModel;

  public function __construct() {
    $this->threadModel = new Thread();
  }

  public function fetchAll() {
    try {
      $result = $this->threadModel->selectAll();
      $this->setLastResult($result);
      return true;
    } catch (Exception $e) {
      $this->setLastError('server', null, $e);
      return false;
    }
  }
}
