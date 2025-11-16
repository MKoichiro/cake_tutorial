<?php

App::uses('AppController', 'Controller');
App::uses('Validator', 'Lib/Validation');
App::uses('AuthenticateService', 'Service');


class AuthenticationsController extends AppController {
    private $validator;
    private $AuthenticateService;

    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
        $this->validator = new Validator();
        $this->AuthenticateService = new AuthenticateService();
    }

    /**
     * ログインフォームの表示
     */
    public function login() {
        CakeLog::write('info', '... ' . __CLASS__ . '#' . __FUNCTION__ . ' START ...');
        $this->request->allowMethod('get');

        // ビューに渡すデータを初期化
        $safeUserData = [];
        $validationErrors = [];
        // (バリデーションエラーがあれば) ユーザー入力値とバリデーションエラーを読み込み
        if ($this->Session->check('validationErrors')) {
            $safeUserData = $this->Session->read('safeUserData');
            $validationErrors = $this->Session->read('validationErrors');
        }
        $this->Session->delete('safeUserData');
        $this->Session->delete('validationErrors');

        $this->set([
            'safeUserData' => $safeUserData, // password 以外のユーザー入力値
            'validationErrors' => $validationErrors, // バリデーションエラーのメッセージ
            'noHeaderNav' => true, // ヘッダーのナビゲーションメニューを非表示にするフラグ
        ]);
        return $this->render('login');
    }


    /**
     * ログイン
     */
    public function auth() {
        CakeLog::write('info', '... ' . __CLASS__ . '#' . __FUNCTION__ . ' START ...');
        $this->request->allowMethod('post');

        // CSRF トークンをチェック
        // if (!$this->Session->check('csrfToken')) {
        // ... throw new BadRequestException();
        // }

        // POST データを取得
        $requestData = $this->request->data;
        // 構造のチェック
        if ((!isset($requestData['User']['email'])) || (!isset($requestData['User']['password']))) {
            CakeLog::write('error', 'Invalid Request Data is given.');
            throw new BadRequestException();
        }

        // 漏洩情報を落としたものをセッションに保存
        $safeUserData = [
            'email' => $requestData['User']['email'],
        ];
        CakeLog::write(
            'info',
            '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
            'User Input Email: '.$safeUserData['email']
        );
        $this->Session->write([
            'safeUserData' => $safeUserData // password 以外のユーザー入力値
        ]);

        // バリデーション: メールアドレスのみ
        if (!$this->validator->execute($safeUserData, 'login')) {
            $validationErrors = $this->validator->getErrorMessages();
            CakeLog::write(
                'warning',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Validation failed for the following reasons:' . "\n" .
                print_r($validationErrors, true)
            );
            $this->Session->write([
                'validationErrors' => $validationErrors
            ]);
            return $this->redirect('/login');
        }

        // 認証処理
        $result = $this->AuthenticateService->authenticate($requestData['User']);
        // 認証処理: 失敗
        if (!$result['status']) {
            CakeLog::write(
                'info',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'User with email `'.$safeUserData['email'].'` faild to login.'
            );
            $this->Flash->error('パスワードまたはメールアドレスが違います。');
            return $this->redirect('/login');
        }

        // 認証処理: 成功
        $this->Session->delete('safeUserData');
        $this->Session->delete('validationErrors');
        $this->Login->login($result['authenticatedUserUid']);
        $this->Flash->info('ログインしました。');
        return $this->redirect('/home');
    }


    /**
     * ログアウト
     */
    public function logout() {
        CakeLog::write('info', '... ' . __CLASS__ . '#' . __FUNCTION__ . ' START ...');
        $this->request->allowMethod('delete');

        // ログアウトの共通処理
        $this->Login->logout('/home');
    }
}