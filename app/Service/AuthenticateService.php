<?php
App::uses('BaseService', 'Service');
App::uses('User', 'Model');
App::uses('DatabaseUtil', 'Lib/Utility');

class AuthenticateService extends BaseService {
  private static $secrets = ['password_hash'];
  private $loginUser;
  private $userModel;

  public function __construct() {
    parent::__construct();
    $this->setLoginUser(null);
    $this->userModel = new User();
  }

  public function setLoginUser($user) {
    $this->loginUser = $user;
  }
  public function getLoginUser() {
    return $this->loginUser;
  }

  private function removeSecrets($user) {
    foreach (self::$secrets as $key) {
        unset($user[$key]);
    }
    return $user;
  }
  
  public function authenticate($credentials) {
    // 引数ガード
    if (!isset($credentials['email']) || !isset($credentials['password'])) {
      $this->setLastError('unexpected');
      return false;
    }

    // パラメータ発行
    $params = ['email' => mb_strtolower($credentials['email'])];

    // DB: ユーザ取得
    try {
      $dbUser = $this->userModel->selectUserByEmail($params);
    } catch (Exception $e) {
      $this->setLastError('server', null, $e);
      return false;
    }

    // ユーザ存在チェック & パスワード照合
    if (is_null($dbUser) || !DatabaseUtil::verifyPassword($credentials['password'], $dbUser['password_hash'])) {
      // ユーザが存在しない、またはパスワード不一致
      $this->setLastError('auth', 'メールアドレスまたはパスワードが正しくありません。');
      return false;
    }

    // 認証成功
    $user = $this->removeSecrets($dbUser);
    CakeLog::write('debug', 'AuthenticateService#authenticate: ' . print_r($user, true));
    $this->setLoginUser($user);
    return true;
  }
}
