<?php
App::uses('AppController', 'Controller');
App::uses('CommentService', 'Service');

class CommentsController extends AppController {
  public $components = ['Authorize', 'Authenticate', 'Flash'];

  private $commentService;
  private $validator;
  public function __construct($request = null, $response = null) {
    parent::__construct($request, $response);
    $this->commentService = new CommentService();
    $this->validator = new Validator();
  }

  public function createComment() {
    $this->request->allowMethod('post');
    $threadID   = $this->request->data['thread']['id'];
    $commentData = $this->request->data['Comment'];
    $loginUser   = $this->Authenticate->getLoginUser();
    $authorData  = [
      'user_id' => $loginUser['user_id'],
      'uid'     => $loginUser['uid'],
    ];

    // バリデーション
    if (!$this->validator->execute($commentData, 'createComment')) {
      $this->set([
        'threadID'    => $threadID,
        'commentData' => $commentData,
        'validationErrors' => $this->validator->getErrorMessages(),
      ]);
      return $this->render('../Threads/show');
    }

    // コメント登録
    if (!$this->commentService->create($threadID, $commentData, $authorData)) {
      $this->Flash->error($this->commentService->getLastError('message'));
      $this->set([
        'threadID'    => $threadID,
        'commentData' => $commentData,
      ]);
      return $this->render('../Threads/show');
    }

    return $this->redirect([
      'controller' => 'threads',
      'action'     => 'show',
      $this->request->data['thread']['uid'], // <- threads.uid
    ]);
  }
}