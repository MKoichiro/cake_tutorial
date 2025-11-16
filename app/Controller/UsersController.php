<?php

App::uses('AppController', 'Controller');
App::uses('UserService', 'Service');
App::uses('AuthenticateService', 'Service');


class UsersController extends AppController {

    private $userService;
    private $AuthenticateService;


    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
        $this->userService = new UserService();
        $this->AuthenticateService = new AuthenticateService();
    }


    /**
     * ユーザー登録の入力画面を表示
     */
    public function register() {
        CakeLog::write('info', '... ' . __CLASS__ . '#' . __FUNCTION__ . ' START ...');
        $this->request->allowMethod('get');

        // ビューに渡すデータを初期化
        $safeUserData = [];
        $validationErrors = [];
        // ... // 「戻って修正」ボタンからの遷移ならはユーザー入力値を読み込み
        if ($this->request->query('prev') === 'true') {
            $safeUserData = $this->Session->read('safeUserData');
        }
        // ... // バリデーションエラーがあればユーザー入力値とバリデーションエラーを読み込み
        if ($this->Session->check('safeUserData') && $this->Session->check('validationErrors')) {
            $safeUserData = $this->Session->read('safeUserData');
            $validationErrors = $this->Session->read('validationErrors');
        }
        // ... // 読み込み後にセッションから削除
        $this->Session->delete('safeUserData');
        $this->Session->delete('validationErrors');

        // ... // ビューに値を渡してレンダリング
        $this->set([
            'safeUserData' => $safeUserData, // password, password_confirmation 以外のユーザー入力値
            'validationErrors' => $validationErrors, // バリデーションエラーのメッセージ
            'noHeader' => true, // ヘッダーのナビゲーションメニューを非表示にするフラグ
        ]);
        return $this->render('register');
    }


    /**
     * ユーザー登録の入力内容確認画面を表示
     */
    public function confirm() {
        CakeLog::write('info', '... ' . __CLASS__ . '#' . __FUNCTION__ . ' START ...');
        $this->request->allowMethod('post');

        // POST データを取得
        $requestData = $this->request->data;
        // ... // データのチェック
        if (
            (!isset($requestData['User']['display_name'])) ||
            (!isset($requestData['User']['email'])) ||
            (!isset($requestData['User']['password'])) ||
            (!isset($requestData['User']['password_confirmation']))
        ) {
            CakeLog::write('error', 'Invalid Request Data is given.');
            throw new BadRequestException();
        }

        // ... // 漏洩情報を落としたものをセッションに保存
        $safeUserData = [
            'display_name' => $requestData['User']['display_name'],
            'email' => $requestData['User']['email'],
        ];
        CakeLog::write(
            'info',
            '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
            'User-submitted "user" data excluding sensitive fields:'."\n".
            print_r($safeUserData, true)
        );
        $this->Session->write(
            'safeUserData', $safeUserData // password, password_confirmation 以外のユーザー入力値
        );

        // ... // バリデーション
        $validationErrors = $this->userService->userValidation($requestData['User']);
        if ($validationErrors !== []) {
            CakeLog::write(
                'warning',
                print_r($validationErrors, true)
            );
            return $this->redirect('/users/register');
        }

        // ... // ビューに値を渡してレンダリング
        $this->set([
            'safeUserData' => $safeUserData, // ユーザー登録フォームのユーザー入力値
            'noHeader' => true, // ヘッダーのナビゲーションメニューを非表示にするフラグ
        ]);
        return $this->render('confirm');
    }


    /**
     * ユーザー登録の完了画面を表示
     */
    public function complete() {
        CakeLog::write('info', '... ' . __CLASS__ . '#' . __FUNCTION__ . ' START ...');
        $this->request->allowMethod('post');

        // POST データ取得: パスワードのみ
        $requestData = $this->request->data;
        // ... // データのチェック
        if ((!isset($requestData['User']['password']))) {
            CakeLog::write('error', 'Invalid Request Data is given.');
            throw new BadRequestException();
        }

        // ... // セッションが切れていたら最初からやり直し
        if ($this->Session->read('safeUserData') === null) {
            CakeLog::write(
                'notice',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Session has expired. Redirecting to users/register.'
            );
            $this->Flash->server('予期せぬエラーが発生しました。最初からやり直して下さい。');
            return $this->redirect('/users/register');
        }
        $safeUserData = $this->Session->read('safeUserData');

        // ... // $safeUserData と $requestData をマージ
        $userInfo = [
            'display_name' => $safeUserData['display_name'],
            'email' => $safeUserData['email'],
            'password' => $requestData['User']['password'],
        ];

        if (!$this->userService->dispatchRegister($userInfo)) {
            $this->Flash->error('予期せぬエラーが発生しました。最初からやり直して下さい。');
            return $this->redirect('/users/register');
        }

        // ... // 自動ログイン
        $credentials = [
            'email' => $safeUserData['email'],
            'password' => $requestData['User']['password'],
        ];
        $authResult = $this->AuthenticateService->authenticate($credentials);
        if (!$authResult['status']) {
            CakeLog::write(
                'error',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Automatic login failed right after user registration. User email: '.$safeUserData['email'].'.'
            );
            throw new InternalErrorException();
        }
        // ... // ログイン成功の共通処理を呼び出し、登録完了画面をレンダリング
        $this->Login->login($authResult['authenticatedUserUid']);
        $this->Flash->success('ユーザー登録が完了しました。');
        $this->set([
            'noHeader' => true, // ヘッダーのナビゲーションメニューを非表示にするフラグ
        ]);
        return $this->render('complete');
    }


    /**
     * public に show(): ユーザーが投稿したマイページ画面を表示
     */
    public function show() {
        CakeLog::write('info', '... ' . __CLASS__ . '#' . __FUNCTION__ . ' START ...');
        $this->request->allowMethod('get');

        // URL パラメーターから user の uid を取得
        $userUid = $this->request->params['user_uid'];
        CakeLog::write(
            'info',
            '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
            'Thread (UID= `'.$userUid.'`) requested via query parameter.'
        );
        // ... // user の uid が取得できないケース
        if (!$userUid) {
            throw new InternalErrorException();
        }

        // ... // ユーザー 1 件とそのユーザーが投稿したスレッド全件とコメント全件を取得
        $result = $this->userService->getUserShowContents($userUid);
        if (!$result['userIsFound']) {
            throw new NotFoundException();
        }
        if (!$result['status']) {
            throw new InternalErrorException();
        }

        $isAuthorizedAsOwner = $this->Authorize->isAuthorizedAs('owner', ['user_uid' => $userUid]);
        CakeLog::write(
            'info',
            '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
            ($isAuthorizedAsOwner
                ? 'User with UID `'.$userUid.'` is authorized as the owner, rendering mypage...'
                : 'User with UID `'.$this->Login->getLoginUserValue('user_id').'` is not authorized as the owner, rendering user s...')
        );

        $this->set([
            'userData' => $result['userData'], // ユーザー情報
            'threadsData' => $result['threadsData'], // ユーザーが投稿したスレッド情報
            'commentsWithThreadsData' => $result['commentsWithThreadsData'], // ユーザーが投稿したコメント情報
            'isAuthorizedAsOwner' => $isAuthorizedAsOwner, // ログインユーザーが本人かどうか
            'loginUserId' => $this->Login->getLoginUserValue('user_id'), // ログインユーザーの id
        ]);
        return $this->render('show');
    }
}
