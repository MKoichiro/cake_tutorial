<?php
class AuthService {

  private static $lastError = null;

  public static function lastError() {
    return self::$lastError;
  }

  public function authenticate($user) {
    $email = $user['email'];
    $password = $user['password'];

    $userModel = new User();
    $dbUser = $userModel->selectUserByEmail($email)['users'];
    $hasher = new BlowfishPasswordHasher();

    if (is_null($dbUser)) {
      // ユーザが存在しない
      self::$lastError = 'User not found';
      return false;
    }

    if ($hasher->check($password, $dbUser['password_hash'])) {
      // 認証成功
      self::setLoginUser($dbUser);
      return true;
    } else {
      // パスワード不一致
      self::$lastError = 'Invalid password';
      return false;
    }
  }
}
