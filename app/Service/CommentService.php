<?php
App::uses('BaseService', 'Service');
App::uses('Comment',     'Model');
App::uses('StringUtil',  'Lib/Utility');

class CommentService extends BaseService {

  private $commentModel;

  public function __construct() {
    parent::__construct();
    $this->commentModel = new Comment();
  }

  /**
   * コメント登録
   * 
   * @param int $threadID スレッドID
   * @param array{ body: string } $commentData コメントデータ
   * @param array $authorData 投稿者データ
   * @return bool 処理の成否
   */
  public function create($threadID, $commentData, $authorData) {
    // 引数ガード
    if (!is_numeric($threadID) || !is_array($commentData) || !is_array($authorData)) {
      $this->setLastError('unexpected');
      return false;
    }

    // パラメータ発行
    $authorUID = $authorData['uid'];
    $params = [
      'uid'         => StringUtil::createUuid(),
      'user_id'     => (int)$authorData['user_id'],
      'thread_id'   => (int)$threadID,
      'body'        => $commentData['body'],
      'created_by'  => $authorUID,
      'updated_by'  => $authorUID,
    ];

    // DB: スレッドに紐づくコメント全件取得
    try {
      $this->commentModel->insertComment($params);
      $this->setLastResult($params['uid']);
      return true;
    } catch (Exception $e) {
      $this->setLastError('server', null, $e);
      return false;
    }
  }

  /**
   * thread_id に紐づくコメントを全件取得
   * 
   * @param int $threadID スレッドID
   * @return bool 処理の成否
   */
  public function fetchWithUserByThreadID($threadID) {
    // 引数ガード
    if (!is_numeric($threadID)) {
      $this->setLastError('unexpected');
      return false;
    }

    // パラメータ発行
    $params = ['thread_id' => (int)$threadID];

    // DB: スレッドに紐づくコメント全件取得
    try {
      $result = $this->commentModel->selectWithUserByThreadID($params);
      $this->setLastResult($result);
      return true;
    } catch (Exception $e) {
      $this->setLastError('server', null, $e);
      return false;
    }
  }
}
