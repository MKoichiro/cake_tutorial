<?php

App::uses('AppController', 'Controller');
App::uses('ThreadService', 'Service');

class ThreadsController extends AppController {
  private $threadService;
  public function __construct($request = null, $response = null) {
    parent::__construct($request, $response);
    $this->threadService = new ThreadService();
  }

  public function home() {
    $this->request->allowMethod('get');
    // スレッド一覧表示
    if (!$this->threadService->fetchAll()) {
      $this->Flash->error($this->threadService->getLastError('message'));
    }
    $this->set('threads', $this->threadService->getLastResult());
    return $this->render('home');
  }
}
