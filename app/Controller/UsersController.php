<?php

App::uses('AppController',       'Controller');
App::uses('UserService',         'Service');
App::uses('MessageBoardService', 'Service');

class UsersController extends AppController {
  public $components = ['Authenticate'];

  private $userService;
  private $messageBoardService;
  private $validator;

  public function __construct($request = null, $response = null) {
    parent::__construct($request, $response);
    $this->userService         = new UserService();
    $this->messageBoardService = new MessageBoardService();
    $this->validator           = new Validator();
  }

  public function register() {
    $this->request->allowMethod('get');

    $this->set([
      'exceptSecrets'    => $this->Session->read('exceptSecrets'),
      'validationErrors' => $this->Session->read('validationErrors')
    ]);

    // 再読み込みでクリア
    $this->Session->delete('exceptSecrets');
    $this->Session->delete('validationErrors');

    $this->render('register');
  }

  public function confirm() {
    $this->request->allowMethod('post');
    $userInput = $exceptSecrets = $this->request->data['User'];
    unset($exceptSecrets['password'], $exceptSecrets['password_confirmation']);
    $this->Session->write('exceptSecrets', $exceptSecrets);

    // バリデーション１: 入力値チェック
    if (!$this->validator->execute($userInput, 'registerUser')) {
      $this->Session->write('validationErrors', $this->validator->getErrorMessages());
      return $this->redirect(['action' => 'register']);
    }
    // バリデーション２: メールアドレス重複チェック
    if ($this->userService->isEmailExists($userInput['email'])) {
      $this->Session->write('validationErrors', ['email' => 'このメールアドレスは既に登録されています。']);
      return $this->redirect(['action' => 'register']);
    }

    $this->set([
      'exceptSecrets' => $exceptSecrets,
      'secrets'       => ['password' => $userInput['password']],
    ]);
    return $this->render('confirm');
  }

  public function complete() {
    $this->request->allowMethod('post');
    $exceptSecrets = $this->Session->read('exceptSecrets');
    $secrets = $this->request->data['User'];

    // パスワードの再バリデーション
    if (!$this->validator->execute($secrets, 'registerUser.password')) {
      $this->Session->write('validationErrors', $this->validator->getErrorMessages());
      return $this->redirect(['action' => 'register']);
    }

    // 登録処理
    $userInfo = array_merge($exceptSecrets, $secrets);
    if (!$this->userService->register($userInfo)) {
      $this->Flash->error($this->userService->getLastError('message'));
      return $this->redirect(['action' => 'register']);
    }
    $this->Session->delete('exceptSecrets');
    $this->Session->delete('validationErrors');

    $credentials = [
      'email'    => $userInfo['email'],
      'password' => $userInfo['password']
    ];

    // 登録後、自動ログイン
    $this->Authenticate->login($credentials);

    $this->set('loginUser', $this->Authenticate->getLoginUser());
    $this->Flash->success(__('登録に成功しました。'));
    return $this->render('complete');
  }

  public function show($uid = null) {
    $this->request->allowMethod('get');
    $requestedUserUid = $this->request->param('uid');

    // ユーザー情報取得
    if ($this->userService->fetchByUid($requestedUserUid)) {
      $userData = $this->userService->getLastResult();
    } else {
      $this->Flash->error($this->userService->getLastError('message'));
      return $this->redirect(['controller' => 'Pages', 'action' => 'home']);
    }

    if ($this->messageBoardService->fetchThreadsByUserUid($requestedUserUid)) {
      $threadsData = $this->messageBoardService->getLastResult();
    } else {
      $this->Flash->error($this->messageBoardService->getLastError('message'));
      // return $this->redirect(['controller' => 'Pages', 'action' => 'home']);
    }

    if ($this->messageBoardService->fetchCommentsWithThreadsByUserUid($requestedUserUid)) {
      $commentsWithThreadsData = $this->messageBoardService->getLastResult();
    } else {
      $this->Flash->error($this->messageBoardService->getLastError('message'));
      // return $this->redirect(['controller' => 'Pages', 'action' => 'home']);
    }

    $this->set([
      'userData'                => $userData,
      'threadsData'             => $threadsData,
      'commentsWithThreadsData' => $commentsWithThreadsData,
    ]);
    return $this->render('show');
  }
}
