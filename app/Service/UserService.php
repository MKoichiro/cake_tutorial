<?php

App::uses('User',         'Model');
App::uses('BaseService',  'Service');
App::uses('StringUtil',   'Lib/Utility');
App::uses('DatabaseUtil', 'Lib/Utility');

class UserService extends BaseService {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }

    /**
     * ユーザー登録
     * 
     * @param array $userInfo
     * @return bool 処理の成否
     */
    public function register($userInfo) {
        $params = [
            'uid'           => StringUtil::generateUuid(),
            'display_name'  => $userInfo['display_name'],
            'email'         => mb_strtolower($userInfo['email']),
            'password_hash' => DatabaseUtil::hashPassword($userInfo['password']),
        ];

        try {
            $this->setLastResult($this->userModel->insert($params));
            return true;
        } catch (Exception $e) {
            $this->setLastError('server', null, $e);
            return false;
        }
    }

    /**
     * １件取得: users.uid に紐づくユーザー
     * 
     * @param string $userUid
     * @return bool 処理の成否
     */
    public function fetchByUid($userUid) {
        $params = ['uid' => $userUid];

        try {
            $this->setLastResult($this->userModel->selectByUid($params));
            return true;
        } catch (Exception $e) {
            $this->setLastError('server', null, $e);
            return false;
        }
    }

    // TODO: 返り値の再検討
    /**
     * メールアドレス存在確認
     * 
     * @param string $email
     * @return bool 処理の成否
     */
    public function isEmailExists($email) {
        $params = ['email' => mb_strtolower($email)];

        try {
            $this->setLastResult($this->userModel->countByEmail($params));
            return true;
        } catch (Exception $e) {
            $this->setLastError('server', null, $e);
            return false;
        }
    }
}
