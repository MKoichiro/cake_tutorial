<?php
App::uses('Component', 'Controller');
App::uses('AuthenticateService', 'Service');
App::uses('PublicError', 'Lib/PublicError');

class AuthorizeComponent extends Component {
  public $components = ['Session', 'Flash', 'Authenticate'];
  private $controller;
  private $authorizationError;
  private $violationRedirectUrl;
  private function isAuthorized($accessRequester, $payload = []) {
    switch ($accessRequester) {
      case 'public':
        return true;
      case 'loginUser':
        if ($this->Authenticate->isLoggedIn()) {
          return true;
        } else {
          $this->setAuthorizationError('auth', '権限がありません。');
          $this->violationRedirectUrl = '/login';
          return false;
        }
      // case 'admin':
      //   $user = $this->Authenticate->getLoginUser();
      //   return !is_null($user) && isset($user['role']) && $user['role'] === 'admin';
      default:
        return false;
    }
  }

  public function __construct(ComponentCollection $collection, $settings = []) {
    parent::__construct($collection, $settings);
  }

  // NOTE: ライフサイクルにおいて beforeFilter 前に実行される
  //       Component クラスには controller メンバが無いため、ここで設定
  //       なお、コンストラクタはライフサイクル外（リクエスト発生前）に実行されるので不適
  public function initialize(Controller $controller) {
    $this->controller = $controller;
  }

  private function setAuthorizationError($type = 'auth', $message = null, $exception = null, $code = null) {
    $this->authorizationError = new PublicError($type, $message, $exception, $code);
  }
  public function getAuthorizationError($attr = null) {
    return $this->authorizationError->getData($attr);
  }

  private function extractControllerWhiteList($appWhiteList, $controllerName) {
    $controllerName = strtolower($controllerName);
    $result = [];
    foreach ($appWhiteList as $requester => $actions) {
      if (!isset($actions[$controllerName])) {
        // CakeErrorControllerなど、想定外の組み込みコントローラーの場合は false で抜ける
        return false;
      }
      $result[$requester] = array_values($actions[$controllerName]);
    }
    return $result;
  }

  public function allow($whiteList = null, $controller = null) {
    if (is_null($controller)) {
      $controller = $this->controller;
    }
    $controllerName = $controller->name;
    $currentAction = $controller->request->action;

    CakeLog::write('debug', '$controllerName: ' . $controllerName);
    CakeLog::write('debug', '$currentAction: ' . $currentAction);
    CakeLog::write('debug', '$controller::$appWhiteList' . print_r($controller::$appWhiteList, true));

    if (is_null($whiteList)) {
      $appWhiteList = $controller::$appWhiteList;
      $whiteList = !is_null($appWhiteList)
      ? $this->extractControllerWhiteList($appWhiteList, $controllerName)
      : [];

      if ($whiteList === false) {
        return;
      }
    }

    $matched = false;
    foreach ($whiteList as $requester => $actions) {
      if (in_array($currentAction, $actions, true)) {
        $matched = true;
        // public アクションに設定されていれば早期許可
        if ($requester === 'public') {
          return;
        }
        // 認可不通過
        if (!$this->isAuthorized($requester)) {
          $this->Flash->error($this->getAuthorizationError('message'));
          return $controller->redirect($this->violationRedirectUrl);
        }
        // 認可通過
        return;
      }
    }

    // whiteListで未指定のアクションは現状 許可 扱いとする
    if (!$matched) {
      return;
    }
  }
}
