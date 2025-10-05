<?php
App::uses('Component', 'Controller');
App::uses('AuthService', 'Service');

class MyAuthComponent extends Component {
  public function login($user) {
    $authService = new AuthService();
    $ok = $authService->authenticate($user);
    if ($ok) {
      // 認証成功: セッションを開始
      $this->Session->renew();
      $this->Session->write('Auth.User', $authService->getLoginUser());
      return true;
    }
    // 認証失敗
    return false;
  }

  public function logout() {
    AuthService::setLoginUser(null);
    $this->Session->destroy();
  }

  public function getLoginUser() {
    return $this->Session->read('Auth.User');
  }
}
