<?php
App::uses('Component', 'Controller');
App::uses('AuthenticateService', 'Service');
App::uses('PublicError', 'Lib/PublicError');

class AuthenticateComponent extends Component {
  public $components = ['Session'];
  private $loginError;
  private $authService;

  public function __construct(ComponentCollection $collection, $settings = []) {
    parent::__construct($collection, $settings);
    $this->setLoginError(null);
    $this->authService = new AuthenticateService();
  }

  private function setLoginError(PublicError|null $error) {
    $this->loginError = $error;
  }
  public function getLoginError() {
    return $this->loginError;
  }

  public function login($credentials) {
    // 認証失敗
    if (!$this->authService->authenticate($credentials)) {
      $this->setLoginError($this->authService->getLastError());
      return false;
    }
    // 認証成功: セッションを開始
    $this->Session->renew();
    $this->Session->write('Auth.User', $this->authService->getLoginUser());
    CakeLog::write('debug', 'AuthenticateComponent#login: Session started.' . print_r($this->Session->read(), true));
    return true;
  }

  public function isLoggedIn() {
    return !is_null($this->getLoginUser());
  }

  public function getLoginUser() {
    return $this->Session->read('Auth.User');
  }

  public function logout() {
    if (!$this->isLoggedIn()) {
      return false;
    }
    $this->Session->destroy();
    return true;
  }
}
