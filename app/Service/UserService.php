<?php

App::uses('User', 'Model');
App::uses('MessageBoardService', 'Service');
App::uses('StringUtil', 'Lib/Utility');
App::uses('DatabaseUtil', 'Lib/Utility');
App::uses('ArrayUtil', 'Lib/Utility');
App::uses('Validator', 'Lib/Validation');
App::uses('MessageBoardQueries', 'Config/Sql');


class UserService {

    private $userModel;
    private $validator;


    public function __construct() {
        $this->userModel = new User();
        $this->validator = new Validator();
    }


    /**
     * 1 件のユーザーを返す関数
     *
     * @param string $userUid
     * @return array 当該ユーザー全件のユーザー配列。
     * @throws Exception
     */
    private function fetchUserByUid($userUid) {
        // SQL 文とパラメーターを準備
        $sqlKey = 'SELECT_USER_BY_UID';
        $sql = constant('MessageBoardQueries::'.$sqlKey);
        CakeLog::write(
            'info',
            '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
            'Executing query identified by the key '.$sqlKey.'. See "app/Config/Sql/*.php" to check raw queries.'
        );

        $params = [
            'user_uid' => $userUid
        ];

        // SQL を実行
        try {
            $result = $this->userModel->executeSql($sql, $params);
        } catch (Exception $e) {
            CakeLog::write(
                'error',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Failed to fetch data for user with UID `'.$userUid.'`.' . "\n" .
                'The following error occurred: '."\n". $e->getMessage()
            );
            throw $e;
        }

        if (count($result) >= 2) {
            CakeLog::write(
                'error',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'User UID `'.$userUid.'` might be duplicated.'
            );
            throw new Exception();
        }

        return $result;
    }


    /**
     * 第一引数で指定される uid を持つユーザー配列について、
     * 第二引数以降で指定されたキー(とそ)の値のみを抽出し て返す。
     * 該当情報は取れない仕様。必要なら AuthenticateService#fetchSecretsByEmail などの専用メソッドを使用する。
     *
     * @param string $userUid
     * @param string ... $keys ユーザー配列のキー
     * @return array 指定のキー... 配列。
     */
    public function getUserValuesByUid($userUid, ...$keys) {
        // users.uid でユーザーを1件取得
        try {
            $result = $this->fetchUserByUid($userUid);
        } catch (Exception $e) {
            return [];
        }

        if ($result === []) {
            return [];
        }

        // 指定された属性のみ返す
        return ArrayUtil::extract($result[0]['users'], ...$keys);
    }


    /**
     * @param array[
     * ... 'display_name': string,
     * ... 'email': string,
     * ... 'password': string,
     * ] $validationUnit
     *
     * @param string $mode = 'strict'
     */
    public function userValidation($validationUnit, $mode = 'strict') {
        $validationErrors = [];
        // 基本のバリデーション
        $this->validator->execute($validationUnit, 'registerUser', $mode);
        if ($validationErrors = $this->validator->getErrorsMessages()) {
            // 2: email の重複確認 (DB 操作を含む)
            $result = $this->countUsersByEmail($validationUnit['email']);
            if ($result['status'] === false) {
                throw new InternalErrorException();
            }

            if ($result['count'] > 0) {
                $validationErrors['email'] = 'このメールアドレスは使用できません。';
            }
            if ($validationErrors !== []) {
                CakeLog::write(
                    'warning',
                    '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                    'Validation for user failed for the following reasons:' . "\n" .
                    print_r($validationErrors, true)
                );
            }
        }
        return $validationErrors;
    }


    /**
     * 1 件のユーザーを登録
     * 本来の処理。
     * $userUid が null の場合、
     * created_by, updated_by は第二引数の $userUid を流用。
     * 本来の処理。
     * created_by, updated_by, first_name, last_name が指定の場合、内容で $userUid を自動生成して割り当て。
     *
     * @param array[
     * ... 'display_name': string,
     * ... 'email': string,
     * ... 'password': string,
     * ... 'created_by': string,
     * ... 'created_datetime': string(null),
     * ... 'updated_by': string(null),
     * ... 'updated_datetime': string(null),
     * ] $baseParams
     * $baseParams の形式は(PHP の指定で) 'Y-m-d H:i:s'。省略した場合は現在時刻。
     *
     * @param string $null $userUid
     * $userUid はメールアドレスのフォーマットの uuid。省略した場合、自動生成される。
     * @see: https://book.cakephp.org/ja/core-utility-libraries/string.html#CakeText::uuid
     *
     * @return bool 成功: true, 失敗: false
     * @throws Exception
     */
    public function registerUser($baseParams, $userUid = null, $dataSource = null) {
        // 主要項目のバリデーション
        $validationUnit = ArrayUtil::extract($baseParams, 'display_name', 'email', 'password');
        if ($validationErrors = $this->userValidation($validationUnit, 'allowRawDataLack')) {
            throw new BadRequestException();
        }

        // SQL 文とパラメーターを準備
        $sqlKey = 'INSERT_USER';
        $sql = constant('MessageBoardQueries::'.$sqlKey);
        CakeLog::write(
            'info',
            '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
            'Executing query identified by the key '.$sqlKey.'. See "app/Config/Sql/*.php" to check raw queries.'
        );

        $userUid = $userUid === null ? StringUtil::generateUuid('pass', $baseParams['email']) : $userUid;
        $currentDatetime = date('Y-m-d H:i:s');
        $params = [
            'user_uid' => $userUid,
            'display_name' => $baseParams['display_name'],
            'email' => $baseParams['email'],
            'password_hash' => $baseParams['password'],
            'created_by' => isset($baseParams['created_by'])
                ? $baseParams['created_by']
                : $userUid,
            'created_datetime' => isset($baseParams['created_datetime'])
                ? $baseParams['created_datetime']
                : $currentDatetime,
            'updated_by' => isset($baseParams['updated_by'])
                ? $baseParams['updated_by']
                : $userUid,
            'updated_datetime' => isset($baseParams['updated_datetime'])
                ? $baseParams['updated_datetime']
                : $currentDatetime,
        ];

        // SQL を実行
        try {
            $result = $this->userModel->executeSql($sql, $params, $dataSource);
        } catch (Exception $e) {
            CakeLog::write(
                'error',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Failed to register user.' . "\n" .
                'The following error occurred: '."\n". $e->getMessage()
            );
            throw $e;
        }

        return $result;
    }


    /**
     * UsersController#complete() アクションで使用するユーザー登録処理
     *
     * @param array[
     * ... 'display_name': string,
     * ... 'email': string,
     * ... 'password': string,
     * ] $userInfo
     *
     * @return bool 成功: true, 失敗: false
     */
    public function dispatchRegister($userInfo) {
        // 拡張性のためトランザクションを張っておく
        $dataSource = ConnectionManager::getDataSource('default');
        $dataSource->begin();
        try {
            // ユーザー登録処理
            $result = $this->registerUser($userInfo, null, $dataSource);
        } catch (Exception $e) {
            $dataSource->rollback();
            return false;
        }
        $dataSource->commit();
        return $result;
    }


    /**
     * メールアドレスでユーザーを数える
     *
     * @param string $email
     * @return array['status': bool, 'count': int|null]
     */
    public function countUsersByEmail($email) {
        // 返り値を初期化
        $result = [
            'status' => false,
            'count' => 0,
        ];

        // SQL 文とパラメーターを準備
        $sqlKey = 'COUNT_USERS_BY_EMAIL';
        $sql = constant('MessageBoardQueries::'.$sqlKey);
        CakeLog::write(
            'info',
            '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
            'Executing query identified by the key '.$sqlKey.'. See "app/Config/Sql/*.php" to check raw queries.'
        );
        $lowerCasedEmail = mb_strtolower($email);
        $params = [ 'email' => $lowerCasedEmail ];

        // SQL を実行
        try {
            $countResult = $this->userModel->executeSql($sql, $params);
        } catch (Exception $e) {
            CakeLog::write(
                'error',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Failed to execute SQL counting user with email address: `'.$lowerCasedEmail.'`.' . "\n" .
                'The following error occurred: '."\n". $e->getMessage()
            );
            return $result;
        }

        $result['status'] = true;
        $result['count'] = (int)$countResult[0][0]['count']; // TODO: 要確認
        CakeLog::write(
            'debug',
            '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
            'The number of users found with the email address: `'.$lowerCasedEmail.'` is: '.$countResult[0][0]['count']
        );

        return $result;
    }


    /**
     * UsersController#show() アクションで使用するデータを取得
     *
     * @param string $userUid
     * @return array[
     * ... 'status': bool,
     * ... 'userIsFound': bool,
     * ... 'userData': array,
     * ... 'threadsData': array,
     * ... 'commentsWithThreadsData': array,
     * ]
     */
    public function getUserShowContents($userUid) {
        // 返り値を初期化
        $result = [
            'status' => false,
            'userIsFound' => false,
            'userData' => [],
            'threadsData' => [],
            'commentsWithThreadsData' => [],
        ];

        try {
            // ユーザー 1 件取得
            $userResult = $this->fetchUserByUid($userUid);
            // ユーザー登録情報に紐づくユーザーが見つからない場合は異常
            if ($userResult === []) {
                CakeLog::write(
                    'error',
                    '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                    'User with UID `'.$userUid.'` is not found.'
                );
                return $result;
            }

            $result['userIsFound'] = true;
            $userData = $userResult[0];
            $userId = $userData['user_id'];

            $messageBoardService = new MessageBoardService();
            // ユーザーがホストのスレッド全件取得
            $threadsData = $messageBoardService->fetchThreadsByUserId($userId);
            // ユーザーが投稿したコメント全件取得
            $commentsWithThreadsData = $messageBoardService->fetchCommentsWithThreadsByUserId($userId);
        } catch (Exception $e) {
            return $result;
        }

        $result['status'] = true;
        $result['userData'] = $userData;
        $result['threadsData'] = $threadsData;
        $result['commentsWithThreadsData'] = $commentsWithThreadsData;
        CakeLog::write(
            'debug',
            '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
            'Successfully fetch the following data for UsersController#show:'."\n".
            print_r($result, true)
        );

        return $result;
    }
}
