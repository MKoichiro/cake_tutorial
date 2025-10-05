<?php
App::uses('AppController', 'Controller');
include('../Lib/Validation/Validator.php');
include('../Service/UserService.php');

class RegistrationsController extends AppController {

  public function displayForm() {
    $this->request->allowMethod('get');

    $this->set('userInput', $this->Session->read('userInput'));
    $this->set('validationErrors', $this->Session->read('validationErrors'));
    $this->Session->delete('userInput');
    $this->Session->delete('validationErrors');

    $this->render('form');
  }

  public function validation($userInput) {
    $validator = new Validator();
    $isValid = $validator->execute($userInput, 'registerUser');
    if (!$isValid) {
      $this->Session->write('validationErrors', $validator->getErrorMessages());
      return $this->redirect(['[method]' => 'GET', 'controller' => 'registrations', 'action' => 'displayForm']);
    }
  }
  public function displayConfirm() {
    $this->request->allowMethod('post');
    $userInput = $this->request->data['User'];
    $this->Session->write('userInput', $userInput);

    // バリデーションチェック
    $this->validation($userInput);

    $this->set('userInput', $userInput);
    return $this->render('confirm');
  }

  public function register($userInput) {
    $userService = new UserService();
    $ok = $userService->register($userInput);
    $errorMessage = $userService::lastError();
    if (!$ok) {
      $this->Flash->error(__($errorMessage['message']));
      return $this->redirect(['[method]' => 'GET', 'controller' => 'registrations', 'action' => 'displayForm']);
    }
    $this->Session->delete('userInput');
    $this->Session->delete('validationErrors');
  }
  public function displayComplete() {
    $this->request->allowMethod('post');
    $userInput = $this->Session->read('userInput');

    // 登録処理
    $this->register($userInput);

    // 登録後、自動ログイン
    $this->login($userInput);

    $this->set('loginUser', $this->getLoginUser());
    $this->Flash->success(__('登録に成功しました。'));
    return $this->render('complete');
  }
}
