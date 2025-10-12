<?php
App::uses('AppModel', 'Model');
App::uses('DatabaseUtil', 'Lib/Utility');

class UserModelException extends Exception {}

class User extends AppModel {
  public function insertUser($params) {
    $sql = DatabaseUtil::sqlReader('insert_user.sql');
    try {
      $result = $this->query($sql, $params);
    } catch (Exception $e) {
      throw $e;
    }

    // insertUser() 内部の Model::query() は、失敗するほとんどの場合、上流で例外をスローするのだが、
    // ドキュメントに失敗時には false を返すと明記がある以上念のためハンドリングする必要がある
    if ($result === false) {
      throw new UserModelException();
    }
    // 成功時は falsy な [] が返る場合がある。
    if (is_array($result) && count($result) === 0) {
      $result = true;
    }
    return $result;
  }

  public function selectUserByEmail($params) {
    $sql = DatabaseUtil::sqlReader('select_user_by_email.sql');
    try {
      $result = $this->query($sql, $params);
    } catch (Exception $e) {
      throw $e;
    }

    if (count($result) === 0) {
      return [];
    } else if ($result === false) {
      throw new UserModelException();
    }
    return $result[0]['users'];
  }

  public function countUsersByEmail($params) {
    $sql = DatabaseUtil::sqlReader('count_users_by_email.sql');
    try {
      $result = $this->query($sql, $params);
    } catch (Exception $e) {
      throw $e;
    }

    CakeLog::write('debug', 'UserModel#countUsersByEmail: ' . print_r($result, true));

    if ($result === false) {
      throw new UserModelException();
    }

    return (int)$result[0][0]['count'];
  }
}
