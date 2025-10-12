<?php

App::uses('User',         'Model');
App::uses('BaseService',  'Service');
App::uses('StringUtil',   'Lib/Utility');
App::uses('DatabaseUtil', 'Lib/Utility');

class UserService extends BaseService {

  private $userModel;
  public function __construct() {
    parent::__construct();
    $this->userModel = new User();
  }

  public function register($userInfo) {
    // 引数ガード
    if (!is_array($userInfo) || is_null($userInfo) || !isset($userInfo['display_name']) || !isset($userInfo['email']) || !isset($userInfo['password'])) {
      $this->setLastError('unexpected');
      return false;
    }

    // パラメータ発行
    $params = [
      'uid'           => StringUtil::createUuid(),
      'display_name'  => $userInfo['display_name'],
      'email'         => mb_strtolower($userInfo['email']),
      'password_hash' => DatabaseUtil::hashPassword($userInfo['password']),
    ];

    // DB: 登録実行
    try {
      $result = $this->userModel->insertUser($params);
      $this->setLastResult($result);
      return true;
    } catch (Exception $e) {
      $this->setLastError('server', null, $e);
      return false;
    }
  }

  public function isEmailExists($email) {
    // 引数ガード
    if (is_null($email) || !is_string($email) || trim($email) === '') {
      $this->setLastError('unexpected');
      return null;
    }

    // パラメータ発行
    $params = ['email' => mb_strtolower($email)];

    // DB: メールアドレス存在チェック
    try {
      $count = $this->userModel->countUsersByEmail($params);
      $this->setLastResult($count);
      return $count > 0;
    } catch (Exception $e) {
      $this->setLastError('server', null, $e);
      return null;
    }
  }
}
