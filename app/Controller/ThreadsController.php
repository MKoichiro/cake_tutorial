<?php

App::uses('AppController',       'Controller');
App::uses('MessageBoardService', 'Service');
App::uses('Validator',           'Lib/Validation');

class ThreadsController extends AppController {
    // public $components = ['Authorize', 'Authenticate', 'Flash'];

    private $messageBoardService;
    private $validator;

    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
        $this->messageBoardService = new MessageBoardService();
        $this->validator           = new Validator();
    }

    public function home() {
        $this->request->allowMethod('get');
        if (!$this->messageBoardService->fetchAllThreads()) {
            $this->Flash->error($this->messageBoardService->getLastError('message'));
        }
        $this->set(['threads' => $this->messageBoardService->getLastResult()]);
        return $this->render('home');
    }

    public function createThread() {
        $this->request->allowMethod('get');
        return $this->render('createThread');
    }

    public function create() {
        $this->request->allowMethod('post');
        $threadData  = $this->request->data['Thread'];

        if (!$this->validator->execute($threadData, 'createThread')) {
            $this->set([
                'threadData'       => $threadData,
                'validationErrors' => $this->validator->getErrorMessages(),
            ]);
            return $this->render('createThread');
        }

        // エンティティ登録
        $authorData = $this->Authenticate->getLoginUserValues('user_id', 'uid');
        // １スレッド作成
        if (!$this->messageBoardService->createThread($threadData, $authorData)) {
            $this->Flash->error($this->messageBoardService->getLastError('message'));
            $this->set(['threadData' => $threadData]);
            return $this->render('createThread');
        }
        $threadUid = $this->messageBoardService->getLastResult();


        $this->Flash->success('スレッドを作成しました。');
        return $this->redirect("/threads/$threadUid");
    }

    public function show($uid = null) {
        $this->request->allowMethod('get');
        $threadUid = $this->request->param('uid');

        // スレッド取得
        if (!$this->messageBoardService->fetchThreadByUid($threadUid)) {
            $this->Flash->error($this->messageBoardService->getLastError('message'));
        }
        $threadWithAuthorData = $this->messageBoardService->getLastResult();
        $threadData = $threadWithAuthorData['threads'];

        // コメント取得
        if (!$this->messageBoardService->fetchCommentsWithUsersByThreadId($threadData['thread_id'])) {
            $this->Flash->error($this->messageBoardService->getLastError('message'));
        }
        $commentsWithAuthorsData = $this->messageBoardService->getLastResult();

        $this->set([
            'threadWithAuthorData'    => $threadWithAuthorData,
            'commentsWithAuthorsData' => $commentsWithAuthorsData,
        ]);
        return $this->render('show');
    }
}
