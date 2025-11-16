<?php

App::uses('BaseService', 'Service');
App::uses('User', 'Model');
App::uses('DatabaseUtil', 'Lib/Utility');
App::uses('MessageBoardQueries', 'Config/Sql');


class AuthenticateService {

    private $userModel;


    public function __construct() {
        $this->userModel = new User();
    }


    /**
     * email で検索し、ユーザーの認証に必要な機密情報を取得
     *
     * @param string $email
     * @return array[
     * ... 'uid': string,
     * ... 'password_hash': string,
     * ... ... ユーザーの機密情報
     * ]
     * @throws Exception
     */
    public function fetchSecretsByEmail($email) {
        $sqlKey = 'SELECT_USER_SECRETS_BY_EMAIL';
        $sql = constant('MessageBoardQueries::'.$sqlKey);
        $params = [ 'email' => mb_strtolower($email) ];

        // SQL を実行
        try {
            $result = $this->userModel->executeSql($sql, $params);
        } catch (Exception $e) {
            CakeLog::write(
                'error',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Failed to fetch user secrets.'."\n".
                'The following error occurred: '."\n". $e->getMessage()
            );
            throw $e;
        }

        if (count($result) >= 2) {
            CakeLog::write(
                'error',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'User with Email `'.$email.'` might be duplicated.'
            );
            throw new InternalErrorException();
        }

        return $result;
    }


    /**
     * 認証処理
     *
     * @param array[
     * ... 'email': string,
     * ... 'password': string,
     * ] $credentials
     * @return array[
     * ... 'status': bool,
     * ... 'authenticatedUserUid': string,
     * ]
     */
    public function authenticate($credentials) {
        $result = [
            'status' => false,
            'authenticatedUserUid' => '',
        ];

        // メールアドレスで検索して機密情報取得済みのユーザー配列を取得
        try {
            $userResult = $this->fetchSecretsByEmail($credentials['email']);
        } catch (Exception $e) {
            return $result;
        }

        if ($userResult !== []) {
            $user = $userResult[0]['users'];
            if (password_verify($credentials['password'], $user['password_hash'])) {
                // ... // 認証成功
                $result['authenticatedUserUid'] = $user['user_uid'];
                $result['status'] = true;
            } else {
                CakeLog::write(
                    'warning',
                    '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                    'Authentication failed due to password mismatch.'
                );
                return $result;
            }
        } else {
            CakeLog::write(
                'warning',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Authentication failed due to email mismatch.'
            );
            return $result;
        }

        return $result;
    }
}