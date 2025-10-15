<?php
App::uses('AppController',  'Controller');
App::uses('CommentService', 'Service');
App::uses('ThreadService',  'Service');
App::uses('Validator',      'Lib/Validation');

class CommentsController extends AppController {
  public $components = ['Authorize', 'Authenticate', 'Flash'];

  private $commentService;
  private $threadService;
  private $validator;
  public function __construct($request = null, $response = null) {
    parent::__construct($request, $response);
    $this->commentService = new CommentService();
    $this->threadService  = new ThreadService();
    $this->validator      = new Validator();
  }

  public function createComment($uid = null) {
    $this->request->allowMethod('post');

    $threadUID         = $this->request->param('thread_uid');
    $inputCommentData  = $this->request->data['Comment'];

    // スレッド情報取得
    if (!$this->threadService->fetchThreadByUID($threadUID)) {
      $this->Flash->error($this->threadService->getLastError('message'));
      // TODO: リダイレクト先、要検討
      // input type hiddenでもuidを受け取り、param側と一致しないならこれ以前にリダイレクトも検討
      return $this->redirect(['action' => 'home']);
    }
    $threadWithAuthorData = $this->threadService->getLastResult();
    $threadData = $threadWithAuthorData['threads'];
    $authorData = $threadWithAuthorData['users'];
    $threadUID  = $threadData['uid'];
    $threadID   = $threadData['thread_id'];

    // スレッドに紐づくコメント全件取得
    // NOTE: JOINでスレッドと一括も可能だが、ページネーションの拡張性を考慮し分離
    if (!$this->commentService->fetchWithUserByThreadID($threadID)) {
      $this->Flash->error($this->commentService->getLastError('message'));
      return $this->redirect(['action' => 'home']); // リダイレクト先、要検討
    }
    $commentsWithAuthorsData = $this->commentService->getLastResult();

    // バリデーション
    if (!$this->validator->execute($inputCommentData, 'createComment')) {
      $this->set([
        'threadWithAuthorData'       => $threadWithAuthorData,
        'commentsWithAuthorsData'     => $commentsWithAuthorsData,
        'inputCommentData' => $inputCommentData,
        'validationErrors' => $this->validator->getErrorMessages(),
      ]);
      return $this->render('../Threads/show');
    }

    // 投稿者情報取得
    $loginUser  = $this->Authenticate->getLoginUser();
    $authorData = [
      'user_id' => $loginUser['user_id'],
      'uid'     => $loginUser['uid'],
    ];

    // コメント登録
    if (!$this->commentService->create($threadID, $inputCommentData, $authorData)) {
      $this->Flash->error($this->commentService->getLastError('message'));
      $this->set([
        'threadWithAuthorData'    => $threadWithAuthorData,
        'commentsWithAuthorsData' => $commentsWithAuthorsData,
        'inputCommentData'        => $inputCommentData,
      ]);
      return $this->render('../Threads/show');
    }

    return $this->redirect([
      'controller' => 'threads',
      'action'     => 'show',
      $threadUID,
    ]);
  }
}
