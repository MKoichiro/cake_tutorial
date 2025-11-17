<?php

App::uses('AppController', 'Controller');
App::uses('MessageBoardService', 'Service');
App::uses('Validator', 'Lib/Validation');
App::uses('Logger', 'Lib/Logger');

class ThreadsController extends AppController {

    private $messageBoardService;
    private $validator;


    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
        $this->messageBoardService = new MessageBoardService();
        $this->validator = new Validator();
    }


    /**
     * アプリのホーム画面(スレッド一覧)を表示
     */
    public function home() {
        CakeLog::write('info', '******************** ' . __CLASS__ . '#' . __FUNCTION__ . ' START ********************');
        $this->request->allowMethod('get');

        // $this->response->statusCode(400);          // ステータスを明示
        // // $this->viewPath = 'Errors';                // /app/View/Errors 配下を見る
        // return $this->render('/Errors/error404');          // error400.ctp をレンダ
        return $this->renderError(404);

        // スレッド全件取得
        $result = $this->messageBoardService->getHomeContents();
        if (!$result['status']) {
            throw new InternalErrorException();
        }

        $this->set([
            'threadsWithUsersData' => $result['threadsWithUsersData'], // スレッドとその投稿ユーザー情報
            'loginUserId' => $this->Login->getLoginUserValue('user_id'), // ログインユーザーの id
        ]);
        return $this->render('home');
    }


    /**
     * スレッド作成フォームを表示
     */
    public function register() {
        CakeLog::write('info', '******************** ' . __CLASS__ . '#' . __FUNCTION__ . ' START ********************');
        $this->request->allowMethod('get');

        // ビューに渡すデータを初期化
        $requestData = [];
        $validationErrors = [];
        // 「キャンセル」ボタンからの遷移ならはユーザー入力値を読み込み
        if ($this->request->query('prev') === 'true') {
            $requestData = $this->Session->read('requestData');
        }
        // バリデーションエラーがあればユーザー入力値とバリデーションエラーを読み込み
        if ($this->Session->check('requestData') && $this->Session->check('validationErrors')) {
            $requestData = $this->Session->read('requestData');
            $validationErrors = $this->Session->read('validationErrors');
        }
        // 読み込み後にセッションから削除
        $this->Session->delete('requestData');
        $this->Session->delete('validationErrors');

        // ビューに値を渡してレンダリング
        $this->set([
            'requestData' => $requestData, // スレッド作成フォームのユーザー入力値
            'validationErrors' => $validationErrors, // バリデーションエラーのメッセージ
        ]);
        return $this->render('register');
    }


    /**
     * スレッド作成の入力内容確認画面を表示
     */
    public function confirm() {
        CakeLog::write('info', '******************** ' . __CLASS__ . '#' . __FUNCTION__ . ' START ********************');
        $this->request->allowMethod('post');

        // POST データを取得
        $requestData = $this->request->data;
        Logger::postData($requestData, __CLASS__, __FUNCTION__);

        // データ構造のチェック
        if (
            (!isset($requestData['Thread']['thread_title'])) ||
            (!isset($requestData['Thread']['thread_description'])) ||
            (!isset($requestData['Comment']['comment_body']))
        ) {
            Logger::invalidRequestData(__CLASS__, __FUNCTION__);
            throw new BadRequestException();
        }

        // 'requestData' スレッド作成フォームのユーザー入力値
        $this->Session->write('requestData', $requestData);

        // バリデーション
        $validationUnit = [
            'thread_title' => $requestData['Thread']['thread_title'],
            'thread_description' => $requestData['Thread']['thread_description'],
            'comment_body' => $requestData['Comment']['comment_body'],
        ];
        if (!$this->validator->execute($validationUnit, 'registerThread')) {
            $validationErrors = $this->validator->getErrorsMessages();
            CakeLog::write(
                'warning',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Validation failed for the following reasons:' . "\n" .
                print_r($validationErrors, true)
            );
            $this->Session->write('validationErrors', $validationErrors);
            return $this->redirect('/threads/register');
        }

        // ビューに値を渡してレンダリング
        $this->set([
            'requestData' => $requestData, // スレッド作成フォームのユーザー入力値
        ]);
        return $this->render('confirm');
    }


    /**
     * スレッド作成
     */
    public function complete() {
        Logger::startAction(__CLASS__, __FUNCTION__);
        $this->request->allowMethod('post');

        // セッションからスレッドとコメントのデータを取得
        $requestData = $this->Session->read('requestData');
        Logger::sessionValue($requestData, __CLASS__, __FUNCTION__);

        // データ構造をチェック
        if (
            (!isset($requestData['Thread']['thread_title'])) ||
            (!isset($requestData['Thread']['thread_description'])) ||
            (!isset($requestData['Comment']['comment_body']))
        ) {
            Logger::invalidRequestData(__CLASS__, __FUNCTION__);
            throw new BadRequestException();
        }

        // スレッド登録
        $authorUid = $this->Login->getLoginUserValue('user_id');
        $result = $this->messageBoardService->dispatchRegisterThread($requestData, $authorUid);
        if (!$result['status']) {
            throw new InternalErrorException();
        }

        // 不要なセッションを削除
        $this->Session->delete('validationErrors');
        $this->Session->delete('requestData');

        // 作成したスレッドの詳細画面へ移動
        $this->Flash->success('スレッドを作成しました。');
        return $this->redirect('/threads/'.$result['threadUid']);
    }


    /**
     * スレッド編集画面を表示
     */
    public function edit() {
        CakeLog::write('info', '******************** ' . __CLASS__ . '#' . __FUNCTION__ . ' START ********************');
        $this->request->allowMethod('get');

        $threadUid = $this->request->params['thread_uid'];
        if (!$threadUid) {
            throw new InternalErrorException();
        }

        // $threadUid でスレッドとその投稿ユーザーを取得
        try {
            $fetchResult = $this->messageBoardService->fetchThreadDataByUid($threadUid);
        } catch (Exception $e) {
            throw new InternalErrorException();
        }
        if ($fetchResult === []) {
            throw new InternalErrorException();
        }

        // 認可: オーナーでなければリダイレクト
        if (!$this->Authorize->isAuthorizedAs('owner', ['user_uid' => $fetchResult['users']['user_uid']])) {
            CakeLog::write('error', '... 認可エラー: 権限がありません。');
            return $this->redirect('/home');
        }

        // ビューに渡すデータの初期化
        $requestData = [];
        $validationErrors = [];
        // 「キャンセル」ボタンからの遷移ならはユーザー入力値を読み込み
        if ($this->request->query('prev') === 'true') {
            // (フォーム) バリデーションエラーがあればユーザー入力値とバリデーションエラーを読み込み
            $requestData = $this->Session->read('requestData');
        }
        // (フォーム) バリデーションエラーがあればユーザー入力値とバリデーションエラーを読み込み
        if ($this->Session->check('requestData') && $this->Session->check('validationErrors')) {
            $requestData = $this->Session->read('requestData');
            $validationErrors = $this->Session->read('validationErrors');
        }
        // 更新フォーム: 読み込み後にセッションから削除
        $this->Session->delete('requestData');
        $this->Session->delete('validationErrors');

        // ビューに値を渡してレンダリング
        $this->set([
            'requestData' => $requestData, // スレッド更新フォームのユーザー入力値
            'validationErrors' => $validationErrors, // バリデーションエラーのメッセージ
            'threadData' => $fetchResult['threads'], // スレッドデータ
            'userData' => $fetchResult['users'], // ユーザーデータ
        ]);
        return $this->render('edit');
    }


    /**
     * スレッド更新
     */
    public function update() {
        CakeLog::write('info', '******************** ' . __CLASS__ . '#' . __FUNCTION__ . ' START ********************');
        $this->request->allowMethod('put');

        $threadUid = $this->request->params['thread_uid'];
        if (!$threadUid) {
            throw new InternalErrorException();
        }

        // TODO: スレッドの存在性確認
        // $threadUid でスレッドとその投稿ユーザーを取得
        try {
            $fetchResult = $this->messageBoardService->fetchThreadDataByUid($threadUid);
        } catch (Exception $e) {
            throw new InternalErrorException();
        }
        if ($fetchResult === []) {
            throw new InternalErrorException();
        }

        $threadData = $fetchResult['threads'];
        $userData = $fetchResult['users'];

        // 認可: オーナーでなければリダイレクト
        if (!$this->Authorize->isAuthorizedAs('owner', ['user_uid' => $userData['user_uid']])) {
            CakeLog::write('error', '... 認可エラー: 権限がありません。');
            return $this->redirect('/home');
        }

        // POST データを取得
        $requestData = $this->request->data;
        CakeLog::write(
            'info',
            '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
            'Read session to get user-submitted thread data and comment data:'."\n".
            print_r($requestData, true)
        );

        // データ構造をチェック
        if (
            (!isset($requestData['Thread']['thread_title'])) ||
            (!isset($requestData['Thread']['thread_description']))
        ) {
            CakeLog::write('error', 'Invalid Request Data is given.');
            throw new BadRequestException();
        }

        // 変更の有無を確認
        if (
            $requestData['Thread']['thread_title'] === $threadData['thread_title'] &&
            $requestData['Thread']['thread_description'] === $threadData['thread_description']
        ) {
            CakeLog::write('notice', '');
            $this->Flash->info('内容を更新してください。');
            return $this->redirect('/threads/'.$threadUid.'/edit');
        }

        // セッションに保存
        $this->Session->write(
            'requestData', $requestData // スレッド編集フォームのユーザー入力値
        );

        // バリデーション
        if (!$this->validator->execute($requestData['Thread'], 'registerThread', 'allowDataLack')) {
            $validationErrors = $this->validator->getErrorsMessages();
            CakeLog::write(
                'warning',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Validation failed for the following reasons:' . "\n" .
                print_r($validationErrors, true)
            );
            $this->Session->write('validationErrors', $validationErrors);
            return $this->redirect('/threads/'.$threadUid.'/edit');
        }

        // スレッド更新
        $result = $this->messageBoardService->updateThreadCore($requestData['Thread'], $threadData['thread_uid']);
        if (!$result) {
            throw new InternalErrorException();
        }

        // 不要なセッションを削除
        $this->Session->delete('validationErrors');
        $this->Session->delete('requestData');

        // ユーザー評価画面へ遷移
        $this->Flash->success('スレッドを更新しました。');
        return $this->redirect('/users/'.$userData['user_uid']);
    }


    /**
     * スレッド削除(未実装)
     */
    public function delete() {
        CakeLog::write('info', '******************** ' . __CLASS__ . '#' . __FUNCTION__ . ' START ********************');
        $this->request->allowMethod('delete');
        $this->Flash->info('未実装');
        return $this->redirect('/home');
    }


    /**
     * スレッド詳細画面を表示
     */
    public function show() {
        CakeLog::write('info', '******************** ' . __CLASS__ . '#' . __FUNCTION__ . ' START ********************');
        $this->request->allowMethod('get');

        // URL パラメーターから thread の uid を取得
        $threadUid = $this->request->params['thread_uid'];
        CakeLog::write(
            'info',
            '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
            'Thread (UID= `'.$threadUid.'`) requested via query parameter.'
        );
        // thread の uid が取得できないケース
        if (!$threadUid) {
            throw new InternalErrorException();
        }

        // スレッド 1 件とそれに紐づくコメント全件を取得
        $result = $this->messageBoardService->getThreadShowContents($threadUid);
        if (!$result['threadIsFound']) {
            throw new NotFoundException();
        }
        if (!$result['status']) {
            throw new InternalErrorException();
        }

        // コメント投稿フォーム関連の処理
        $requestData = [];
        $validationErrors = [];
        // (バリデーションエラー) ユーザー入力値とバリデーションエラーを読み込み
        if ($this->Session->check('validationErrors')) {
            $requestData = $this->Session->read('requestData');
            $validationErrors = $this->Session->read('validationErrors');
        }
        $this->Session->delete('requestData');
        $this->Session->delete('validationErrors');

        // ビューに値を渡してレンダリング
        $this->set([
            'threadWithAuthorData'      => $result['threadWithAuthorData'],             // スレッドとその投稿ユーザーの情報
            'commentsWithAuthorData'    => $result['commentsWithAuthorData'],           // スレッドとその投稿ユーザーの配列
            'requestData'               => $requestData,                                // コメント投稿フォームのユーザー入力値
            'validationErrors'          => $validationErrors,                           // コメント投稿フォームのバリデーション
            'isAuthorizedAsLoginUser'   => $this->Login->isLoggedIn(),                  // ログインユーザーかどうか
            'loginUserId'               => $this->Login->getLoginUserValue('user_id'),  // ログインユーザーの id
        ]);
        return $this->render('show');
    }
}