<?php

App::uses('AppModel',     'Model');
App::uses('DatabaseUtil', 'Lib/Utility');

class CommentModelException extends Exception {}

class Comment extends AppModel {
  public function insertComment($params) {
    $sql = DatabaseUtil::sqlReader('insert_comment.sql');

    try {
      $result = $this->query($sql, $params);
    } catch (Exception $e) {
      throw $e;
    }

    if ($result === false) {
      throw new CommentModelException();
    }

    return true;
  }

  public function selectWithUserByThreadID($params) {
    $sql = DatabaseUtil::sqlReader('select_comments_by_thread_id.sql');

    try {
      $result = $this->query($sql, $params);
    } catch (Exception $e) {
      throw $e;
    }

    if ($result === false) {
      throw new CommentModelException();
    }

    return $result[0];
  }
}
