<?php
App::uses('AppController', 'Controller');

class UsersController extends AppController {
  // public function beforeFilter() {
  //   parent::beforeFilter();
  //   $this->Auth->allow('add', 'logout');
  // }

  // public function login() {
  //   if ($this->request->is('post')) {
  //     if ($this->Auth->login()) {
  //       $this->redirect($this->Auth->redirectUrl());
  //     } else {
  //       $this->Flash->error(__('Invalid username or password, try again'));
  //     }
  //   }
  // }

  // public function logout() {
  //   $this->redirect($this->Auth->logout());
  // }

  public function index() {
    $this->User->recursive = 0;
    $this->set('users', $this->paginate());
  }

  public function view($id = null) {
    $this->User->id = $id;
    if (!$this->User->exists()) {
      throw new NotFoundException(__('Invalid user'));
    }
    $this->set('user', $this->User->findById($id));
  }

  // public function add() {
  //   if ($this->request->is('post')) {
  //     $this->User->create();
  //     if ($this->User->save($this->request->data)) {
  //       $this->Flash->success(__('The user has been saved'));
  //       return $this->redirect(['action' => 'index']);
  //     }
  //     $this->Flash->error(__('The user could not be saved. Please try again.'));
  //   }
  // }
    // フォーム表示（GET）
  public function displayRegister() {
    // ビューに渡す初期値があればセット
    // $this->request->data = array('User' => array(...));
    $this->render('register'); // app/View/Users/register.ctp を使う例
  }

  // 作成（POST）
  public function register() {
    $this->request->allowMethod('post'); // CakePHP2 では onlyAllow を使う
    $this->User->create();
    if ($this->User->save($this->request->data)) {
      $this->Flash->success(__('The user has been saved'));
      return $this->redirect(array('action' => 'index'));
    }
    $this->Flash->error(__('The user could not be saved. Please try again.'));
    // バリデーションエラー時は同じフォームを再表示
    $this->render('register');
  }

  public function edit($id = null) {
    $this->User->id = $id;
    if (!$this->User->exists()) {
      throw new NotFoundException(__('Invalid user'));
    }
    if ($this->request->is(['post', 'put'])) {
      if ($this->User->save($this->request->data)) {
        $this->Flash->success(__('The user has been saved'));
        return $this->redirect(['action' => 'index']);
      }
      $this->Flash->error(__('The user could not be saved. Please, try again.'));
    } else {
      $this->request->data = $this->User->findById($id);
      unset($this->request->data['User']['password']);
    }
  }

  public function delete($id = null) {
    $this->request->allowMethod('post');

    $this->User->id = $id;
    if (!$this->User->exists()) {
      throw new NotFoundException();
    }
    if ($this->User->delete()) {
      $this->Flash->success(__('User deleted'));
      return $this->redirect(['action' => 'index']);
    }
    $this->Flash->error(__('User was not deleted'));
    return $this->redirect(['action' => 'index']);
  }
}
