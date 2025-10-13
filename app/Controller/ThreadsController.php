<?php

App::uses('AppController', 'Controller');
App::uses('ThreadService', 'Service');
App::uses('Validator', 'Lib/Validation');

class ThreadsController extends AppController {
  public $components = ['Authorize'];

  private $threadService;
  private $validator;
  public function __construct($request = null, $response = null) {
    parent::__construct($request, $response);
    $this->threadService = new ThreadService();
    $this->validator     = new Validator();
  }

  public function beforeFilter() {
    parent::beforeFilter();
    CakeLog::write('debug', 'allow() called from ThreadsController');
    $this->Authorize->allow([
      'public' => ['home'],
      'loginUser' => ['home', 'create', 'show'],
    ]);
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

  public function new() {
    $this->request->allowMethod('get');
    $this->set([
      'threadData' => [],
      'validationErrors' => [],
    ]);
    // スレッド作成画面表示
    return $this->render('new');
  }

  public function create() {
    $this->request->allowMethod('post');
    $threadData = $this->request->data['Thread'];

    // バリデーション
    if (!$this->validator->execute($threadData, 'thread')) {
      $this->set([
        'threadData' => $threadData,
        'validationErrors' => $this->validator->getErrorMessages(),
      ]);
      return $this->render('new');
    }

    // スレッド作成
    $loginUser = $this->Authenticate->getLoginUser();
    $author = [
      'created_by' => $loginUser['uid'],
      'updated_by' => $loginUser['uid']
    ];
    if (!$this->threadService->create($threadData, $author)) {
      $this->Flash->error($this->threadService->getLastError('message'));
      $this->set('threadData', $threadData);
      return $this->render('new');
    }
    $this->Flash->success('スレッドを作成しました。');
    return $this->redirect(['action' => 'show', $this->threadService->getLastResult()['id']]);
  }

  public function show($id = null) {
    $this->request->allowMethod('get');
    $threadId = $id;
    // スレッド詳細表示
    if (!$this->threadService->fetchByThreadId($threadId)) {
      $this->Flash->error($this->threadService->getLastError('message'));
    }
    $this->set('thread', $this->threadService->getLastResult());
    return $this->render('show');
  }
}
