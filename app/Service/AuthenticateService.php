<?php

App::uses('BaseService', 'Service');
App::uses('User', 'Model');
App::uses('DatabaseUtil', 'Lib/Utility');

class AuthenticateService extends BaseService {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }

    /**
     * 認証処理
     *
     * @param array{ email: string, password: string } $credentials
     * @return array|false 認証成功時はユーザー情報配列、失敗時は false
     */
    public function authenticate($credentials) {
        $failure = false;
        $params = ['email' => mb_strtolower($credentials['email'])];

        // 認証失敗: email に紐づくユーザー無し
        if (!$this->userModel->selectWithSecretsByEmail($params)) {
            $failure = true;
        } else {
            // 認証失敗: パスワード不一致
            $user = $this->userModel->getLastResult();
            if (!DatabaseUtil::verifyPassword($credentials['password'], $user['password_hash'])) {
                $failure = true;
            }
        }

        if ($failure) {
            $this->setLastError('auth', 'メールアドレスまたはパスワードが正しくありません。');
            return false;
        } else {
            // 認証成功
            return AppUtil::secretsRemover($user);
        }
    }
}
