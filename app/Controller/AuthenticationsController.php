<?php
App::uses('AppController', 'Controller');
App::uses('Validator', 'Lib/Validation');

class AuthenticationsController extends AppController {
  private $validator;

  public function __construct($request = null, $response = null) {
    parent::__construct($request, $response);
    $this->validator = new Validator();
  }

  // ログインフォーム表示アクション
  public function displayForm() {
    $this->request->allowMethod('get');

    // 既にログインしていればリダイレクト
    if ($this->Authenticate->isLoggedIn()) {
      return $this->redirect(['[method]' => 'GET', 'controller' => 'threads', 'action' => 'home']);
    }

    $this->set([
      'exceptSecrets'    => $this->Session->read('exceptSecrets'),
      'validationErrors' => $this->Session->read('validationErrors'),
    ]);

    // 再読み込みでクリア
    $this->Session->delete('exceptSecrets');
    $this->Session->delete('validationErrors');

    $this->render('login');
  }

  // ログイン処理アクション
  public function login() {
    $this->request->allowMethod('post');
    $userInput = $exceptSecrets = $this->request->data['User'];
    unset($exceptSecrets['password']);
    $this->Session->write('exceptSecrets', $exceptSecrets);

    // バリデーションチェック
    if (!$this->validator->execute($userInput, 'loginUser')) {
      $this->Session->write('validationErrors', $this->validator->getErrorMessages());
      return $this->redirect(['[method]' => 'GET', 'action' => 'displayForm']);
    }

    // 認証: 失敗
    if (!$this->Authenticate->login($userInput)) {
      $this->Flash->error($this->Authenticate->getLoginError('message'));
      return $this->redirect(['[method]' => 'GET', 'action' => 'displayForm']);
    }

    // 認証: 成功
    $this->Session->delete('exceptSecrets');
    $this->Session->delete('validationErrors');
    $this->Flash->success('ログインしました。');
    return $this->redirect(['[method]' => 'GET', 'controller' => 'threads', 'action' => 'home']);
  }

  // ログアウト処理アクション
  public function logout() {
    $this->request->allowMethod('delete');

    if ($this->Authenticate->logout()) {
      $this->Flash->success('ログアウトしました。');
    }
    return $this->redirect(['[method]' => 'GET', 'action' => 'displayForm']);
  }
}
