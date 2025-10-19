<?php

App::uses('Thread',      'Model');
App::uses('Comment',     'Model');
App::uses('BaseService', 'Service');
App::uses('StringUtil',  'Lib/Utility');

class MessageBoardService extends BaseService {
    private $threadModel;
    private $commentModel;

    public function __construct() {
        $this->threadModel  = new Thread();
        $this->commentModel = new Comment();
    }

    /**
     * 全件取得: スレッド
     * 
     * @return bool 処理の成否
     */
    public function fetchAllThreads() {
        try {
            $result = $this->threadModel->selectAll();
            $this->setLastResult($result);
        return true;
        } catch (Exception $e) {
            $this->setLastError('server', null, $e);
            return false;
        }
    }

    /**
     * １件取得: threads.uid に紐づくスレッド
     * 
     * @param string $threadUid
     * @return bool 処理の成否
     */
    public function fetchThreadByUid($threadUid) {
        $params = ['uid' => $threadUid];

        try {
            $result = $this->threadModel->selectByUid($params);
            $this->setLastResult($result);
            return true;
        } catch (NotFoundException $e) {
            $this->setLastError('server', '指定されたスレッドは存在していません。', $e);
            return false;
        } catch (Exception $e) {
            $this->setLastError('server', null, $e);
            return false;
        }
    }

    /**
     * 全件取得: users.uid に紐づくスレッド
     * 
     * @param string $userUid
     * @return bool 処理の成否
     */
    public function fetchThreadsByUserUid($userUid) {
        $params = ['uid' => $userUid];

        try {
            $result = $this->threadModel->selectByUserUid($params);
            $this->setLastResult($result);
            return true;
        } catch (NotFoundException $e) {
            $this->setLastError('server', '指定されたスレッドは存在していません。', $e);
            return false;
        } catch (Exception $e) {
            $this->setLastError('server', null, $e);
            return false;
        }
    }

    /**
     * 登録: スレッド
     * 
     * @param array{ title: string, description: string|null } $threadData
     * @param array{ uid: string, user_id: int }               $authorData
     * @return bool 処理の成否
     */
    public function createThread($threadData, $authorData) {
        $authorUid = $authorData['uid'];
        $threadDescription = trim($threadData['description']);
        $params = [
            'uid'         => StringUtil::generateUuid(),
            'user_id'     => (int)$authorData['user_id'],
            'title'       => $threadData['title'],
            'description' => $threadDescription === '' ? null : $threadDescription,
            'created_by'  => $authorUid,
            'updated_by'  => $authorUid,
        ];

        try {
            $this->threadModel->insert($params);
            $this->setLastResult($params['uid']);
            return true;
        } catch (Exception $e) {
            $this->setLastError('server', null, $e);
            return false;
        }
    }


    // === 以下、コメント関連のメソッド =========================
    /**
     * 登録: コメント
     * 
     * @param int                                $threadId
     * @param array{ body: string }              $commentData
     * @param array{ uid: string, user_id: int } $authorData
     * @return bool 処理の成否
     */
    public function createComment($threadId, $commentData, $authorData) {
        $authorUid = $authorData['uid'];
        $params = [
            'uid'         => StringUtil::generateUuid(),
            'user_id'     => (int)$authorData['user_id'],
            'thread_id'   => (int)$threadId,
            'body'        => $commentData['body'],
            'created_by'  => $authorUid,
            'updated_by'  => $authorUid,
        ];

        try {
            $this->commentModel->insert($params);
            $this->setLastResult($params['uid']);
            return true;
        } catch (Exception $e) {
            $this->setLastError('server', null, $e);
            return false;
        }
    }

    /**
     * 全件取得: thread_id に紐づくコメント
     * 
     * @param int $threadId
     * @return bool 処理の成否
     */
    public function fetchCommentsWithUsersByThreadId($threadId) {
        $params = ['thread_id' => (int)$threadId];

        try {
            $result = $this->commentModel->selectWithUserByThreadId($params);
            $this->setLastResult($result);
            return true;
        } catch (Exception $e) {
            $this->setLastError('server', null, $e);
            return false;
        }
    }

    /**
     * 全件取得: users.uid に紐づくコメント（スレッド情報付き）
     * 
     * @param string $userUid
     * @return bool 処理の成否
     */
    public function fetchCommentsWithThreadsByUserUid($userUid) {
        $params = ['uid' => $userUid];

        try {
            $result = $this->commentModel->selectWithThreadsByUserUid($params);
            $this->setLastResult($result);
            return true;
        } catch (Exception $e) {
            $this->setLastError('server', null, $e);
            return false;
        }
    }
}
