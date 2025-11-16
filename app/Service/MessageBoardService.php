<?php

App::uses('Thread', 'Model');
App::uses('Comment', 'Model');
App::uses('User', 'Model');
App::uses('StringUtil', 'Lib/Utility');
App::uses('DatabaseUtil', 'Lib/Utility');
App::uses('MessageBoardQueries', 'Config/Sql');
App::uses('Validator', 'Lib/Validation');

// NOTE:
// ・・・ executeSql()でSQLを実際に実行するモデルの変数に)を走行する関数では、 例外は握りつぶさずそのままスロー。ログは残す。
// ・・・ 呼び出し元で握りつぶして仮に返却するとともにログは重複しないケースだけ残す。
// ・・・ executeSql()を実行する関数では、Model::executeSql()の返り値を整形すること止める。
// ・・・ コメント、スレッド登録処理の場合
// ・・・ INSERT系の処理の場合は、処理の成否を示す bool
// ・・・ SELECT系の処理の場合は、処理の成否を示す 'status' と取得したデータを格納した配列

class MessageBoardService {

    /** @var Thread $ThreadModel */
    private $ThreadModel;

    /** @var Comment $CommentModel */
    private $CommentModel;

    /** @var Validator $validator */
    private $validator;


    public function __construct() {
        $this->ThreadModel = new Thread();
        $this->CommentModel = new Comment();
        $this->validator = new Validator();
    }


    /**
     * スレッドとその投稿ユーザーを全件取得
     *
     * @return array
     * ... 'threads': array,
     * ... 'users': array,
     * ... 'latest_comment_datetime': string
     * }
     * @throws Exception
     */
    public function fetchAllThreadsWithUsersData() {
        // SQL 文とパラメーターを準備
        $sqlKey = 'SELECT_THREADS_WITH_USERS';
        $sql = constant('MessageBoardQueries::'.$sqlKey);
        CakeLog::write(
            'info',
            '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
            'Executing query identified by the key '.$sqlKey.'. See "app/Config/Sql/*.php" to check raw queries.'
        );

        // SQL を実行
        try {
            $result = $this->ThreadModel->executeSql($sql);
        } catch (Exception $e) {
            CakeLog::write(
                'error',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Failed to fetch all threads data.'. "\n" .
                'The following error occurred: '."\n". $e->getMessage()
            );
            throw $e;
        }

        return $result;
    }


    /**
     * 引数の uid を持つスレッドとその投稿ユーザー情報の配列を1件取得
     *
     * @param string $threadUid
     * @return array[
     * ... 'thread': array,
     * ... 'user': array,
     * ]
     * @throws Exception
     */
    public function fetchThreadDataByUid($threadUid) {
        // SQL 文とパラメーターを準備
        $sqlKey = 'SELECT_THREAD_WITH_USER_BY_UID';
        $sql = constant('MessageBoardQueries::'.$sqlKey);
        CakeLog::write(
            'info',
            '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
            'Executing query identified by the key '.$sqlKey.'. See "app/Config/Sql/*.php" to check raw queries.'
        );

        $params = [
            'thread_uid' => $threadUid
        ];

        // SQL を実行
        try {
            $result = $this->ThreadModel->executeSql($sql, $params);
        } catch (Exception $e) {
            CakeLog::write(
                'error',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Failed to fetch thread and user data for thread with UID `'.$threadUid.'`.' . "\n" .
                'The following error occurred: '."\n". $e->getMessage()
            );
            throw $e;
        }

        // スレッドが見つからない
        if (count($result) === 0) {
            CakeLog::write(
                'error',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Thread with UID `'.$threadUid.'` is not found.'
            );
            throw new Exception();
        }

        // スレッドが重複
        if (count($result) >= 2) {
            CakeLog::write(
                'error',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Thread with UID `'.$threadUid.'` might be duplicated.'
            );
            throw new Exception();
        }

        return $result[0]; // 'thread' => [], 'user' => []
    }


    /**
     * 引数の thread_uid を持つスレッドの threads.thread_id を返す
     *
     * @param string $threadUid
     * @return int $threadId
     * @throws Exception
     */
    private function fetchThreadIdByUid($threadUid) {
        // SQL 文とパラメーターを準備
        $sqlKey = 'SELECT_THREAD_ID_BY_UID';
        $sql = constant('MessageBoardQueries::'.$sqlKey);
        CakeLog::write(
            'info',
            '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
            'Executing query identified by the key '.$sqlKey.'. See "app/Config/Sql/*.php" to check raw queries.'
        );
        $params = [
            'thread_uid' => $threadUid
        ];

        // SQL を実行
        try {
            $result = $this->ThreadModel->executeSql($sql, $params);
        } catch (Exception $e) {
            CakeLog::write(
                'error',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Failed to fetch thread_id for thread with UID `'.$threadUid.'`.' . "\n" .
                'The following error occurred: '."\n". $e->getMessage()
            );
            throw $e;
        }

        // スレッドが見つからない
        if (count($result) === 0) {
            CakeLog::write(
                'error',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Thread with UID `'.$threadUid.'` is not found.'
            );
            throw new Exception();
        }

        // スレッドが重複
        if (count($result) >= 2) {
            CakeLog::write(
                'error',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Thread with UID `'.$threadUid.'` might be duplicated.'
            );
            throw new Exception();
        }

        return $result[0]['threads']['thread_id'];
    }


    /**
     * 引数で指定された thread_id に紐づくコメントとその投稿ユーザーを全件取得
     *
     * @param int $threadId
     * @return array[
     * ... 'comment': array,
     * ... 'user': array,
     * ]
     * @throws Exception
     */
    private function fetchCommentsWithUsersByThreadId($threadId) {
        // SQL 文とパラメーターを準備
        $sqlKey = 'SELECT_COMMENTS_WITH_USERS_BY_THREADID';
        $sql = constant('MessageBoardQueries::'.$sqlKey);
        CakeLog::write(
            'info',
            '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
            'Executing query identified by the key '.$sqlKey.'. See "app/Config/Sql/*.php" to check raw queries.'
        );
        $params = [
            'thread_id' => (int) $threadId
        ];

        // SQL を実行
        try {
            $result = $this->CommentModel->executeSql($sql, $params);
        } catch (Exception $e) {
            CakeLog::write(
                'error',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Failed to fetch comments and users data for thread with ID `'.$threadId.'`.' . "\n" .
                'The following error occurred: '."\n". $e->getMessage()
            );
            throw $e;
        }

        return $result;
    }


    /**
     * 引数の user_id に紐づくスレッドを全件取得
     *
     * @param int $userId
     * @return array[ 'threads': array[] ]
     * @throws Exception
     */
    public function fetchThreadsByUserId($userId) {
        // SQL 文とパラメーターを準備
        $sqlKey = 'SELECT_THREADS_BY_USERID';
        $sql = constant('MessageBoardQueries::'.$sqlKey);
        CakeLog::write(
            'info',
            '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
            'Executing query identified by the key '.$sqlKey.'. See "app/Config/Sql/*.php" to check raw queries.'
        );

        $params = [
            'user_id' => (int) $userId
        ];

        // SQL を実行
        try {
            $result = $this->ThreadModel->executeSql($sql, $params);
        } catch (Exception $e) {
            CakeLog::write(
                'error',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Failed to fetch threads data for user with ID `'.$userId.'`.' . "\n" .
                'The following error occurred: '."\n". $e->getMessage()
            );
            throw $e;
        }

        return $result;
    }


    /**
     * 引数の user_id に紐づくコメントとそれに紐づくスレッド情報を全件取得
     *
     * @param int $userId
     * @return array[
     * ... 'thread': array,
     * ... 'comment': array,
     * ]
     * @throws Exception
     */
    public function fetchCommentsWithThreadsByUserId($userId) {
        // SQL 文とパラメーターを準備
        $sqlKey = 'SELECT_COMMENTS_WITH_THREADS_BY_USERID';
        $sql = constant('MessageBoardQueries::'.$sqlKey);
        CakeLog::write(
            'info',
            '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
            'Executing query identified by the key '.$sqlKey.'. See "app/Config/Sql/*.php" to check raw queries.'
        );

        $params = [
            'user_id' => (int) $userId
        ];

        // SQL を実行
        try {
            $result = $this->CommentModel->executeSql($sql, $params);
        } catch (Exception $e) {
            CakeLog::write(
                'error',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Failed to fetch comments and threads data for user with ID `'.$userId.'`.' . "\n" .
                'The following error occurred: '."\n". $e->getMessage()
            );
            throw $e;
        }

        return $result;
    }


    /**
     * 1 件のスレッドを登録
     *
     * @param array[
     * ... 'user_id': int,
     * ... 'thread_title': string,
     * ... 'thread_description': string,
     * ... 'created_by': string,
     * ... 'created_datetime': string(null),
     * ... 'updated_by': string,
     * ... 'updated_datetime': string(null),
     * ] $baseParams
     *
     * @param string $threadUid
     * ... $threadUid の形式は(PHP の指定で) 'Y-m-d H:i:s'。省略した場合は現在時刻。
     * ... @see: https://book.cakephp.org/ja/core-utility-libraries/string.html#CakeText::uuid
     *
     * @return bool true: 成功, false: 失敗
     * @throws Exception
     */
    private function registerThread($baseParams, $threadUid = null, $dataSource = null) {
        // 主要項目のバリデーション
        $validationUnit = [
            'thread_title' => [ 'required', 'notEmpty' ],
            'thread_description' => [ 'required', 'notEmpty' ],
        ];
        if (!$this->validator->execute($validationUnit, 'registerThread', 'allowRawDataLack')) {
            $validationErrors = $this->validator->getErrorMessages();
            CakeLog::write(
                'warning',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Validation for thread failed for the following reasons:' . "\n" .
                print_r($validationErrors, true)
            );
            return false;
        }


        // SQL 文とパラメーターを準備
        $sqlKey = 'INSERT_THREAD';
        $sql = constant('MessageBoardQueries::'.$sqlKey);
        CakeLog::write(
            'info',
            '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
            'Executing query identified by the key '.$sqlKey.'. See "app/Config/Sql/*.php" to check raw queries.'
        );

        $threadUid = $threadUid === null ? StringUtil::generateUuid() : $threadUid;
        $currentDatetime = date('Y-m-d H:i:s');

        $params = [
            'thread_uid' => $threadUid,
            'user_id' => $baseParams['user_id'],
            'thread_title' => $baseParams['thread_title'],
            'thread_description' => $baseParams['thread_description'],
            'created_by' => $baseParams['created_by'],
            'created_datetime' => isset($baseParams['created_datetime'])
                ? $baseParams['created_datetime'] : $currentDatetime,
            'updated_by' => $baseParams['updated_by'],
            'updated_datetime' => isset($baseParams['updated_datetime'])
                ? $baseParams['updated_datetime'] : $currentDatetime,
        ];

        // SQL を実行
        try {
            $result = $this->ThreadModel->executeSql($sql, $params, $dataSource);
        } catch (Exception $e) {
            CakeLog::write(
                'error',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Failed to register thread.' . "\n" .
                'The following error occurred: '."\n". $e->getMessage()
            );
            throw $e;
        }

        return $result === [] || $result === true ? true : false;
    }


    /**
     * 1 件のコメントを登録
     *
     * @param array[
     * ... 'user_id': int,
     * ... 'thread_id': int,
     * ... 'comment_body': string,
     * ... 'created_by': string,
     * ... 'created_datetime': string(null),
     * ... 'updated_by': string,
     * ... 'updated_datetime': string(null),
     * ] $baseParams
     *
     * @param string $null $threadUid
     * ... $threadUid の形式は(PHP の指定で) 'Y-m-d H:i:s'。省略した場合は現在時刻。
     *
     * @param string $null $threadUid
     * ... $threadUid の形式は(PHP の指定で) 'Y-m-d H:i:s'。省略した場合は現在時刻。
     * ... @see: https://book.cakephp.org/ja/core-utility-libraries/string.html#CakeText::uuid
     *
     * @return bool true: 成功, false: 失敗
     * @throws Exception
     */
    private function registerComment($baseParams, $commentUid = null, $dataSource = null) {
        // 主要項目のバリデーション
        $validationUnit = [
            'comment_body' => $baseParams['comment_body'] !== null ? $baseParams['comment_body'] : '',
        ];
        $this->validator->execute($validationUnit, 'registerComment');
        if ($validationErrors = $this->validator->getErrorMessages()) {
            CakeLog::write(
                'warning',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Validation for comment failed for the following reasons:' . "\n" .
                print_r($validationErrors, true)
            );
            return false;
        }


        // SQL 文とパラメーターを準備
        $sqlKey = 'INSERT_COMMENT';
        $sql = constant('MessageBoardQueries::'.$sqlKey);
        CakeLog::write(
            'info',
            '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
            'Executing query identified by the key '.$sqlKey.'. See "app/Config/Sql/*.php" to check raw queries.'
        );

        $commentUid = $commentUid === null ? StringUtil::generateUuid() : $commentUid;
        $currentDatetime = date('Y-m-d H:i:s');

        $params = [
            'comment_uid' => $commentUid,
            'user_id' => $baseParams['user_id'],
            'thread_id' => $baseParams['thread_id'],
            'comment_body' => $baseParams['comment_body'],
            'created_by' => $baseParams['created_by'],
            'created_datetime' => isset($baseParams['created_datetime'])
                ? $baseParams['created_datetime'] : $currentDatetime,
            'updated_by' => $baseParams['updated_by'],
            'updated_datetime' => isset($baseParams['updated_datetime'])
                ? $baseParams['updated_datetime'] : $currentDatetime,
        ];

        // SQL を実行
        try {
            $result = $this->CommentModel->executeSql($sql, $params, $dataSource);
        } catch (Exception $e) {
            CakeLog::write(
                'error',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Failed to register comment.' . "\n" .
                'The following error occurred: '."\n". $e->getMessage()
            );
            throw $e;
        }

        return $result === [] || $result === true ? true : false;
    }


    /**
     * 1 件のスレッドの title, description, updated_by, updated_datetime を更新
     *
     * @param array[
     * ... 'thread_title': string,
     * ... 'thread_description': string,
     * ] $baseParams
     *
     * @param string $null $targetThreadUid
     *
     * @return bool true: 成功, false: 失敗
     * @throws Exception
     */
    public function updateThreadCore($baseParams, $targetThreadUid) {
        // 主要項目のバリデーション
        $validationUnit = [
            'thread_title' => [ 'required', 'notEmpty' ],
            'thread_description' => [ 'required', 'notEmpty' ],
        ];
        if (!isset($baseParams['thread_title'])) { $baseParams['thread_title'] = ''; }
        if (!isset($baseParams['thread_description'])) { $baseParams['thread_description'] = ''; }

        $this->validator->execute($validationUnit, 'registerThread', 'allowRawDataLack');
        if ($validationErrors = $this->validator->getErrorMessages()) {
            CakeLog::write(
                'warning',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Validation for thread failed for the following reasons:' . "\n" .
                print_r($validationErrors, true)
            );
            return false;
        }


        // SQL 文とパラメーターを準備
        $sqlKey = 'UPDATE_THREAD_CORE';
        $sql = constant('MessageBoardQueries::'.$sqlKey);
        CakeLog::write(
            'info',
            '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
            'Executing query identified by the key '.$sqlKey.'. See "app/Config/Sql/*.php" to check raw queries.'
        );

        $params = [
            'target_thread_uid' => $targetThreadUid,
            'thread_title' => $baseParams['thread_title'],
            'thread_description' => $baseParams['thread_description'],
            'updated_by' => $baseParams['updated_by'],
            'updated_datetime' => date('Y-m-d H:i:s'),
        ];

        // SQL を実行
        $dataSource = ConnectionManager::getDataSource('default');
        $dataSource->begin();
        try {
            $result = $this->ThreadModel->executeSql($sql, $params);
        } catch (Exception $e) {
            $dataSource->rollback();
            CakeLog::write(
                'error',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Failed to update thread.' . "\n" .
                'The following error occurred: '."\n". $e->getMessage()
            );
            throw $e;
        }

        $dataSource->commit();
        return $result === [] || $result === true ? true : false;
    }


    /**
     * 1 件のスレッドの title, description, updated_by, updated_datetime を更新
     *
     * @param array[
     * ... 'thread_title': string,
     * ... 'thread_description': string,
     * ] $baseParams
     *
     * @param string $null $targetThreadUid
     *
     * @return bool true: 成功, false: 失敗
     * @throws Exception
     */
    public function updateCommentCore($baseParams, $targetCommentUid) {
        // 主要項目のバリデーション
        $validationUnit = [
            'comment_body' => [ 'required', 'notEmpty' ],
        ];
        if ($baseParams['comment_body'] === null) { $baseParams['comment_body'] = ''; }

        $this->validator->execute($validationUnit, 'registerComment');
        if ($validationErrors = $this->validator->getErrorMessages()) {
            CakeLog::write(
                'warning',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Validation for thread failed for the following reasons:' . "\n" .
                print_r($validationErrors, true)
            );
            return false;
        }


        // SQL 文とパラメーターを準備
        $sqlKey = 'UPDATE_COMMENT_CORE';
        $sql = constant('MessageBoardQueries::'.$sqlKey);
        CakeLog::write(
            'info',
            '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
            'Executing query identified by the key '.$sqlKey.'. See "app/Config/Sql/*.php" to check raw queries.'
        );

        $params = [
            'target_comment_uid' => $targetCommentUid,
            'comment_body' => $baseParams['comment_body'],
            'updated_by' => $baseParams['updated_by'],
            'updated_datetime' => date('Y-m-d H:i:s'),
        ];

        // SQL を実行
        $dataSource = ConnectionManager::getDataSource('default');
        $dataSource->begin();
        try {
            $result = $this->CommentModel->executeSql($sql, $params);
        } catch (Exception $e) {
            $dataSource->rollback();
            CakeLog::write(
                'error',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Failed to update comment.' . "\n" .
                'The following error occurred: '."\n". $e->getMessage()
            );
            throw $e;
        }

        $dataSource->commit();
        return $result === [] || $result === true ? true : false;
    }


    /**
     * ThreadsController#home() アクションで使用するデータを取得
     * スレッドとその投稿ユーザー全件取得
     *
     * @return array[
     * ... 'status': bool,
     * ... 'threadsWithUsersData': [
     * ... 'thread': array,
     * ... 'users': array,
     * ],
     * ... 'getLatestCommentDatetime': string
     * ]
     */
    public function getHomeContents() {
        // 返り値を初期化
        $result = [
            'status' => false,
            'threadsWithUsersData' => [],
        ];

        // スレッドとその投稿ユーザーを全件取得
        try {
            $threadsWithUsersData = $this->fetchAllThreadsWithUsersData();

        } catch (Exception $e) {
            return $result;
        }

        $result['status'] = true;
        $result['threadsWithUsersData'] = $threadsWithUsersData;
        CakeLog::write(
            'debug',
            '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
            'Successfully fetch the following data for ThreadsController#home:' . "\n" .
            print_r($result, true)
        );

        return $result;
    }


    /**
     * ThreadsController#create() アクションで使用するデータ登録処理
     * スレッドを1件登録する
     *
     * @param array[
     * ... 'Thread': [
     * ... 'thread_title': string,
     * ... 'thread_description': string,
     * ],
     * ... 'Comment': [
     * ... 'comment_body': string,
     * ]
     * ] $data
     *
     * @param string $authorUid
     *
     * @return array['status': bool, 'threadUid': string]
     */
    public function dispatchRegisterThread($data, $authorUid) {
        // 返り値を初期化
        $result = [
            'status' => false,
            'threadUid' => '',
        ];

        $threadData = $data['Thread'];
        $commentData = $data['Comment'];

        // ユーザーの uid で検索して user_id を取得
        $userService = new UserService();
        $authorId = $userService->getUserValuesByUid($authorUid)['user_id'];

        // スレッド・コメントで共通のパラメーター
        $commonParams = [
            'user_id' => $authorId,
            'created_by' => $authorUid,
            'updated_by' => $authorUid,
        ];

        // スレッドのパラメーターを準備
        $threadUid = StringUtil::generateUuid();
        $threadParams = array_merge($commonParams, [
            'thread_title' => $threadData['thread_title'],
            'thread_description' => $threadData['thread_description'],
        ]);

        // $dataSource = ConnectionManager::getDataSource('default');
        // $dataSource->begin();

        // スレッド登録処理
        // try {
        //     $this->registerThread($threadParams, $threadUid, $dataSource);

        //     // コメント内容があれば登録処理
        //     if (isset($commentData['comment_body']) && $commentData['comment_body'] !== '') {
        //         // スレッドの uid で検索して thread_id を取得
        //         $threadId = $this->fetchThreadIdByUid($threadUid);
        //         $commentUid = StringUtil::generateUuid();
        //         $commentParams = array_merge($commonParams, [
        //             'thread_id' => $threadId,
        //             'comment_body' => $commentData['comment_body'],
        //         ]);
        //         $this->registerComment($commentParams, $commentUid, $dataSource);
        //     }
        // } catch (Exception $e) {
        //     $dataSource->rollback();
        //     return $result;
        // }
        // $dataSource->commit();


        $dataSource = ConnectionManager::getDataSource('default');
        $dataSource->begin();

        // スレッド登録処理
        try {
            $this->registerThread($threadParams, $threadUid, $dataSource);

            // コメント内容があれば登録処理
            if (isset($commentData['comment_body']) && $commentData['comment_body'] !== '') {
                // スレッドの uid で検索して thread_id を取得
                $threadId = $this->fetchThreadIdByUid($threadUid);
                $commentUid = StringUtil::generateUuid();
                $commentParams = array_merge($commonParams, [
                    'thread_id' => $threadId,
                    'comment_body' => $commentData['comment_body'],
                ]);
                $this->registerComment($commentParams, $commentUid, $dataSource);
            }
        } catch (Exception $e) {
            $dataSource->rollback();
            return $result;
        }
        $dataSource->commit();

        $result['status'] = true;
        $result['threadUid'] = $threadUid;
        CakeLog::write(
            'debug',
            '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
            'Successfully registered the thread with UID: `'.$threadUid.'`.'
        );

        return $result;
    }


    /**
     * ThreadsController#show() で使用するデータを取得
     * スレッド 1 件とそれに紐づくコメント全件
     *
     * @param string $threadUid
     * @return array[
     * ... 'status': bool,
     * ... 'threadIsFound': bool,
     * ... 'threadWithAuthorData': array,
     * ... 'commentsWithAuthorData': array,
     * ]
     */
    public function getThreadShowContents($threadUid) {
        // 返り値を初期化
        $result = [
            'status' => false,
            'threadIsFound' => false,
            'threadWithAuthorData' => [],
            'commentsWithAuthorData' => [],
        ];

        // スレッドとその投稿ユーザー 1 件を取得
        try {
            // スレッドが取得・注入される。スレッドが見つからない場合は例外
            if (($threadWithAuthorData = $this->fetchThreadDataByUid($threadUid)) === []) {
                CakeLog::write(
                    'error',
                    '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                    'Thread with UID `'.$threadUid.'` is not found.'
                );
                return $result;
            }
            $result['threadIsFound'] = true;
            // スレッド 1 件に紐づくコメント全件を取得
            $commentsWithAuthorData = $this->fetchCommentsWithUsersByThreadId($threadWithAuthorData['threads']['thread_id']);
        } catch (Exception $e) {
            return $result;
        }

        $result['status'] = true;
        $result['threadWithAuthorData'] = $threadWithAuthorData;
        $result['commentsWithAuthorData'] = $commentsWithAuthorData;
        CakeLog::write(
            'debug',
            '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
            'Successfully fetch the following data for ThreadsController#show:' . "\n" .
            print_r($result, true)
        );

        return $result;
    }


    /**
     * CommentsController#create() アクションで使用するデータ登録処理
     * コメントを1件登録する
     *
     * @param array[
     * ... 'user_id': int,
     * ... 'comment_uid': string,
     * ... 'comment_body': string,
     * ] $commentData
     *
     * @param int $threadId
     *
     * @return bool true: 成功, false: 失敗
     */
    public function dispatchRegisterComment($commentData, $threadUid, $authorUid) {
        $commentUid = StringUtil::generateUuid();

        // threads.uid で検索して threads.thread_id を取得
        try {
            $threadId = $this->fetchThreadIdByUid($threadUid);
        } catch (Exception $e) {
            return false;
        }

        // users.uid で検索して users.user_id を取得
        $userService = new UserService();
        $authorId = $userService->getUserValuesByUid($authorUid, 'user_id')['user_id'];
        if ($authorId === null) {
            return false;
        }

        $params = [
            'user_id' => (int) $authorId,
            'thread_id' => (int) $threadId,
            'comment_body' => $commentData['comment_body'],
            'created_by' => $authorUid,
            'updated_by' => $authorUid,
        ];

        // 拡張性のためトランザクションを張っておく
        $dataSource = ConnectionManager::getDataSource('default');
        $dataSource->begin();
        try {
            $result = $this->registerComment($params, $commentUid, $dataSource);
        } catch (Exception $e) {
            $dataSource->rollback();
            return false;
        }
        $dataSource->commit();

        CakeLog::write(
            'debug',
            '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
            'Successfully registered the comment with UID: `'.$commentUid.'`.'
        );

        return $result;
    }


    /**
     * uid を指定してコメントを1件取得
     *
     * @param string $commentUid
     * @return array
     */
    public function fetchCommentByUid($commentUid) {
        // SQL 文とパラメーターを準備
        $sqlKey = 'SELECT_COMMENT_BY_UID';
        $sql = constant('MessageBoardQueries::'.$sqlKey);
        CakeLog::write(
            'info',
            '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
            'Executing query identified by the key '.$sqlKey.'. See "app/Config/Sql/*.php" to check raw queries.'
        );
        $params = [ 'comment_uid' => $commentUid ];

        // SQL を実行
        try {
            $result = $this->CommentModel->executeSql($sql, $params);
        } catch (Exception $e) {
            CakeLog::write(
                'error',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Failed to fetch comment and associated data for comment with UID `'.$commentUid.'`.' . "\n" .
                'The following error occurred: '."\n". $e->getMessage()
            );
            throw $e;
        }

        // スレッドが空
        if (count($result) === 0) {
        }
        // スレッドが重複
        if (count($result) >= 2) {
            CakeLog::write(
                'error',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Comment UID `'.$commentUid.'` might be duplicated.'
            );
            throw new Exception();
        }

        return $result === [] ? $result : $result[0];
    }
}