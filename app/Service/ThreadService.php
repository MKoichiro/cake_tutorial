<?php

App::uses('Thread', 'Model');
App::uses('BaseService', 'Service');
App::uses('StringUtil', 'Lib/Utility');

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

  public function fetchByThreadId($threadId) {
    // 引数ガード
    if (empty($threadId)) {
      $this->setLastError('invalidArgument', 'スレッドIDが無効です。');
      return false;
    }

    // params 発行
    $params = ['thread_id' => $threadId];
    // DB
    try {
      $result = $this->threadModel->selectByThreadId($params);
      $this->setLastResult($result);
      return true;
    } catch (NotFoundException $e) {
      $this->setLastError('server', '指定されたスレッドは存在していません。', $e);
      return false;
    } catch (Exception $e) {
      $this->setLastError('server', null, $e);
      return false;
    }
  }

  public function create($threadData, $author) {
    // 引数ガード
    if (empty($threadData) || !isset($threadData['title']) || !isset($threadData['body'])) {
      $this->setLastError('unexpected');
      return false;
    }
    if (empty($author) || !isset($author['created_by']) || !isset($author['updated_by'])) {
      $this->setLastError('unexpected');
      return false;
    }

    // params 発行
    $params = [
      'uid' => StringUtil::createUuid(),
      'title' => $threadData['title'],
      'description' => $threadData['description'],
    ];
    $params = array_merge($params, $author);

    // DB
    try {
      $this->threadModel->insertThread($params);
      $this->setLastResult($params);
    } catch (Exception $e) {
      $this->setLastError('server', null, $e);
      return false;
    }
  }

}
