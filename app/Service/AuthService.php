<?php
class AuthService {

  private static $loginUser = null;

  public static function setLoginUser($user) {
    self::$loginUser = $user;
  }

  public static function getLoginUser() {
    return self::$loginUser;
  }

  public function authenticate($user) {
    $email = $user['email'];
    $password = $user['password'];

    $userModel = new User();
    $dbUser = $userModel->selectUserByEmail($email)['users'];
    $hasher = new BlowfishPasswordHasher();

    if ($dbUser && $hasher->check($password, $dbUser['password_hash'])) {
      // 認証成功
      unset($dbUser['password_hash']);
      // return $dbUser;
      self::setLoginUser($dbUser);
      return true;
    }
    // 認証失敗
    return false;
  }
}
