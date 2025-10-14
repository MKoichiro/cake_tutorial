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

  public function fetchThreadByUID($threadUID) {
    // 引数ガード
    if (!is_string($threadUID)) {
      $this->setLastError('unexpected');
      return false;
    }

    // params 発行
    $params = ['uid' => $threadUID];

    // DB
    try {
      $result = $this->threadModel->selectThreadByUID($params);
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

  public function create($threadData, $authorData) {
    // 引数ガード
    if (!is_array($threadData) || !is_array($authorData)) {
      $this->setLastError('unexpected');
      return false;
    }
    if (!isset($threadData['title']) || !array_key_exists('description', $threadData)) {
      $this->setLastError('unexpected');
      return false;
    }
    if (!isset($authorData['user_id']) || !isset($authorData['uid'])) {
      $this->setLastError('unexpected');
      return false;
    }

    // params 発行
    $authorUID = $authorData['uid'];
    $threadDescription = trim($threadData['description']);
    $params = [
      'uid'         => StringUtil::createUuid(),
      'user_id'     => $authorData['user_id'],
      'title'       => $threadData['title'],
      'description' => $threadDescription === '' ? null : $threadDescription,
      'created_by'  => $authorUID,
      'updated_by'  => $authorUID,
    ];

    // DB
    try {
      $this->threadModel->insertThread($params);
      $this->setLastResult($params['uid']);
      return true;
    } catch (Exception $e) {
      $this->setLastError('server', null, $e);
      return false;
    }
  }
}
