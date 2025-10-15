<?php

App::uses('AppController', 'Controller');
App::uses('ThreadService', 'Service');
App::uses('CommentService', 'Service');
App::uses('Validator', 'Lib/Validation');

class ThreadsController extends AppController {
  public $components = ['Authorize', 'Authenticate', 'Flash'];

  private $threadService;
  private $commentService;
  private $validator;
  public function __construct($request = null, $response = null) {
    parent::__construct($request, $response);
    $this->threadService = new ThreadService();
    $this->commentService = new CommentService();
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

  public function createThread() {
    $this->request->allowMethod('get');
    // スレッド作成画面表示
    return $this->render('createThread');
  }

  public function create() {
    $this->request->allowMethod('post');
    $threadData  = $this->request->data['Thread'];
    $commentData = $this->request->data['Comment'];

    $validationErrors = [];
    if (!$this->validator->execute($threadData, 'createThread')) {
      $validationErrors['thread'] = $this->validator->getErrorMessages();
    }
    if (!$this->validator->execute($commentData, 'createComment')) {
      $validationErrors['comment'] = $this->validator->getErrorMessages();
    }
    if (count($validationErrors) > 0) {
      $this->set([
        'threadData'       => $threadData,
        'commentData'      => $commentData,
        'validationErrors' => $validationErrors,
      ]);
      return $this->render('createThread');
    }

    // エンティティ登録
    $viewData = [];
    $loginUser = $this->Authenticate->getLoginUser();
    $authorData = [
      'user_id' => $loginUser['user_id'],
      'uid'     => $loginUser['uid'],
    ];
    // １スレッド作成
    if (!$this->threadService->create($threadData, $authorData)) {
      $this->Flash->error($this->threadService->getLastError('message'));
      $this->set([
        'threadData'       => $threadData,
        'commentData'      => $commentData,
      ]);
      return $this->render('createThread');
    }
    $threadUID = $this->threadService->getLastResult();

    // ２コメント作成
    if (!$this->commentService->create($threadUID, $commentData, $authorData)) {
      $this->Flash->error($this->commentService->getLastError('message'));
      $this->set([
        'threadData'       => $threadData,
        'commentData'      => $commentData,
      ]);
      return $this->render('createThread');
    }

    $this->Flash->success('スレッドを作成しました。');
    return $this->redirect([
      'controller' => 'threads',
      'action'     => 'show',
      $threadUID
    ]);
  }

  public function show($uid = null) {
    $this->request->allowMethod('get');
    $threadUID = $this->request->param('uid');

    // スレッド取得
    if (!$this->threadService->fetchThreadByUID($threadUID)) {
      $this->Flash->error($this->threadService->getLastError('message'));
    }
    $threadWithAuthorData = $this->threadService->getLastResult();
    $threadData = $threadWithAuthorData['threads'];

    // コメント取得
    if (!$this->commentService->fetchWithUserByThreadID($threadData['thread_id'])) {
      $this->Flash->error($this->commentService->getLastError('message'));
    }
    $commentsWithAuthorsData = $this->commentService->getLastResult();

    $this->set([
      'threadWithAuthorData'    => $threadWithAuthorData,
      'commentsWithAuthorsData' => $commentsWithAuthorsData,
    ]);
    return $this->render('show');
  }
}
