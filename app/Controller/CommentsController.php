<?php
App::uses('AppController',       'Controller');
App::uses('MessageBoardService', 'Service');
App::uses('Validator',           'Lib/Validation');

class CommentsController extends AppController {
    // public $components = ['Authorize', 'Authenticate', 'Flash'];

    private $messageBoardService;
    private $validator;

    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
        $this->messageBoardService = new MessageBoardService();
        $this->validator           = new Validator();
        }

    public function createComment($uid = null) {
        $this->request->allowMethod('post');

        $threadUid         = $this->request->param('thread_uid');
        $inputCommentData  = $this->request->data['Comment'];

        // スレッド情報取得
        if (!$this->messageBoardService->fetchThreadByUid($threadUid)) {
            $this->Flash->error($this->messageBoardService->getLastError('message'));
            // TODO: リダイレクト先、要検討
            // input type hiddenでもuidを受け取り、param側と一致しないならこれ以前にリダイレクトも検討
            return $this->redirect(['action' => 'home']);
        }
        $threadWithAuthorData = $this->messageBoardService->getLastResult();
        $threadData = $threadWithAuthorData['threads'];
        $authorData = $threadWithAuthorData['users'];
        $threadUid  = $threadData['uid'];
        $threadId   = $threadData['thread_id'];

        // スレッドに紐づくコメント全件取得
        // NOTE: JOINでスレッドと一括も可能だが、ページネーションの拡張性を考慮し分離
        if (!$this->messageBoardService->fetchCommentsWithUsersByThreadId($threadId)) {
            $this->Flash->error($this->messageBoardService->getLastError('message'));
            return $this->redirect(['action' => 'home']); // リダイレクト先、要検討
        }
        $commentsWithAuthorsData = $this->messageBoardService->getLastResult();

        // バリデーション
        if (!$this->validator->execute($inputCommentData, 'createComment')) {
            $this->set([
                'threadWithAuthorData'    => $threadWithAuthorData,
                'commentsWithAuthorsData' => $commentsWithAuthorsData,
                'inputCommentData'        => $inputCommentData,
                'validationErrors'        => $this->validator->getErrorMessages(),
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
        if (!$this->messageBoardService->createComment($threadId, $inputCommentData, $authorData)) {
            $this->Flash->error($this->messageBoardService->getLastError('message'));
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
            $threadUid,
        ]);
    }
}
