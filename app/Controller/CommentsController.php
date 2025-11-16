<?php

App::uses('AppController', 'Controller');
App::uses('MessageBoardService', 'Service');
App::uses('Validator', 'Lib/Validation');

class CommentsController extends AppController {

    private $messageBoardService;

    private $validator;


    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
        $this->messageBoardService = new MessageBoardService();
        $this->validator = new Validator();
    }


    /** コメント作成 */
    public function create() {
        CakeLog::write('info', '... ' . __CLASS__ . '#' . __FUNCTION__ . ' START ...');
        $this->request->allowMethod('post');

        // URL パラメーターから thread_uid を取得
        $threadUid = $this->request->params['thread_uid'];
        CakeLog::write(
            'info',
            '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
            'Thread (UID:'.$threadUid.') requested via query parameter.'."\n".
            print_r($threadUid, true)
        );

        // $threadUid が取得できないケース
        if (!$threadUid) {
            throw new InternalErrorException();
        }

        // TODO: thread の存在性確認
        // if () {
        // ... // redirect
        // }

        // POST データ取得
        $requestedCommentData = $this->request->data;
        CakeLog::write(
            'info',
            '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
            'User-submitted \'comment\' data fields:'."\n".
            print_r($requestedCommentData, true)
        );

        if ((!isset($requestedCommentData['Comment']['comment_body']))) {
            CakeLog::write('error', 'Invalid Request Data is given.');
            throw new BadRequestException();
        }

        // セッションに保存
        $this->Session->write(
            'requestData', $requestedCommentData // コメント入力フォームのユーザー入力値
        );

        // バリデーション
        if (!$this->validator->execute($requestedCommentData['Comment'], 'registerComment')) {
            $validationErrors = $this->validator->getErrorsMessages();
            CakeLog::write(
                'warning',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Validation failed for the following reasons:' . "\n" .
                print_r($validationErrors, true)
            );
            $this->Session->write('validationErrors', $validationErrors);
            return $this->redirect('/threads/'.$threadUid);
        }

        // コメント DB に登録
        $authorUid = $this->Login->getLoginUserValue('user_id');
        $result = $this->messageBoardService->dispatchRegisterComment($requestedCommentData['Comment'], $threadUid, $authorUid);
        if (!$result) {
            throw new InternalErrorException();
        }

        // コメントが持つスレッド詳細画面にリダイレクト
        $this->Flash->success('コメントを投稿しました。');
        return $this->redirect('/threads/'.$threadUid);
    }


    /** コメント編集画面を表示 */
    public function edit() {
        CakeLog::write('info', '... ' . __CLASS__ . '#' . __FUNCTION__ . ' START ...');
        // $commentUid /edit
        $this->request->allowMethod('get');

        $threadUid = $this->request->params['thread_uid'];
        $commentUid = $this->request->params['comment_uid'];
        if (!$threadUid || !$commentUid) {
            throw new InternalErrorException();
        }

        // $commentUid でスレッドとその投稿ユーザーと送信元スレッドを取得
        try {
            $fetchResult = $this->messageBoardService->fetchCommentByUid($commentUid);
        } catch (Exception $e) {
            throw new InternalErrorException();
        }

        if ($fetchResult === []) {
            throw new InternalErrorException();
        }

        $commentData = $fetchResult['comments'];
        $threadData = $fetchResult['threads'];
        $userData = $fetchResult['users'];

        // 認可: オーナーでなければリダイレクト
        if (!$this->Authorize->isAuthorizedAs('owner', ['user_uid' => $userData['user_uid']])) {
            CakeLog::write('error', '... 認可エラー: 権限がありません。');
            return $this->redirect('/home');
        }

        // ... // ビューにフォーム、ビューヘルパーの初期化
        $requestData = [];
        $validationErrors = [];
        // ... // $this->Session->read('requestData') の中身ならはユーザー入力値を読み込み
        if ($this->request->query('prev') === 'true') {
            $requestData = $this->Session->read('requestData');
            // ...
        }
        // ... // $this->Session->read('validationErrors') があればユーザー入力値とバリデーションエラーを読み込み
        if ($this->Session->check('requestData') && $this->Session->check('validationErrors')) {
            $requestData = $this->Session->read('requestData');
            $validationErrors = $this->Session->read('validationErrors');
            $this->Session->delete('requestData');
            $this->Session->delete('validationErrors');
        }

        // ビューに値を渡しレンダリング
        $this->set([
            'requestData' => $requestData, // スレッド更新フォームのユーザー入力値
            'validationErrors' => $validationErrors, // バリデーションエラーのメッセージ
            'commentData' => $commentData, // コメントデータ
            'threadData' => $threadData, // スレッドデータ
            'userData' => $userData, // ユーザーデータ
            'loginUserId' => $this->Login->getLoginUserValue('user_id'), // ログインユーザーの id
        ]);
        return $this->render('edit');
    }


    /** コメント編集 */
    public function update() {
        CakeLog::write('info', '... ' . __CLASS__ . '#' . __FUNCTION__ . ' START ...');
        $this->request->allowMethod('put');

        $threadUid = $this->request->params['thread_uid'];
        $commentUid = $this->request->params['comment_uid'];
        if (!$threadUid || !$commentUid) {
            throw new InternalErrorException();
        }

        // TODO: コメントの存在性
        // $commentUid でスレッドとその投稿ユーザーと送信元スレッドを取得
        try {
            $fetchResult = $this->messageBoardService->fetchCommentByUid($commentUid);
        } catch (Exception $e) {
            throw new InternalErrorException();
        }

        if ($fetchResult === []) {
            throw new InternalErrorException();
        }

        $commentData = $fetchResult['comments'];
        $userData = $fetchResult['users'];
        $threadData = $fetchResult['threads'];

        // 認可: オーナーでなければリダイレクト
        if (!$this->Authorize->isAuthorizedAs('owner', ['user_uid' => $userData['user_uid']])) {
            CakeLog::write('error', '... 認可エラー: 権限がありません。');
            return $this->redirect('/home');
        }

        // POST データ取得
        $requestData = $this->request->data;
        CakeLog::write(
            'info',
            '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
            'User-submitted comment and comment data:'."\n".
            print_r($requestData, true)
        );

        // ...
        // POST データ: 順送をチェック
        if ((!isset($requestData['Comment']['comment_body']))) {
            CakeLog::write('error', 'Invalid Request Data is given.');
            throw new BadRequestException();
        }

        // POST データ: 変更の有無を確認
        if ($requestData['Comment']['comment_body'] === $commentData['comment_body']) {
            CakeLog::write('notice', '');
            $this->Flash->info('内容を更新してください。');
            return $this->redirect('/threads/'.$threadUid.'/comments/'.$commentData['comment_uid'].'/edit');
        }

        // POST データ: セッションに保存
        $this->Session->write(
            'requestData', $requestData // コメント編集フォームのユーザー入力値
        );

        // POST データ: バリデーション
        if (!$this->validator->execute($requestData['Comment'], 'registerComment')) {
            $validationErrors = $this->validator->getErrorsMessages();
            CakeLog::write(
                'warning',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Validation failed for the following reasons:' . "\n" .
                print_r($validationErrors, true)
            );
            $this->Session->write('validationErrors', $validationErrors);
            return $this->redirect('/threads/'.$threadUid.'/comments/'.$commentData['comment_uid'].'/edit');
        }

        // コメント更新
        $result = $this->messageBoardService->updateCommentCore($requestData['Comment'], $commentData['comment_uid']);
        if (!$result) {
            throw new InternalErrorException();
        }

        // 不要なセッションを削除
        $this->Session->delete('validationErrors');
        $this->Session->delete('requestData');

        // ユーザー評価画面へ遷移
        $this->Flash->success('コメントを更新しました。');
        return $this->redirect('/users/'.$userData['user_uid']);
    }
}