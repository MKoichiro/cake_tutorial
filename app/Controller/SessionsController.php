<?php
class SessionsController extends AppController {

  // public function login() {
  //   if ($this->request->is('post')) {
  //     // ユーザ認証
  //     $user = $this->Auth->identify();
  //     if ($user) {
  //       // 認証成功
  //       $this->Auth->setUser($user);
  //       return $this->redirect($this->Auth->redirectUrl());
  //     } else {
  //       // 認証失敗
  //       $this->Flash->error(__('Invalid username or password, try again'));
  //     }
  //   }
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

  public function logout() {
    $this->redirect($this->Auth->logout());
  }
}