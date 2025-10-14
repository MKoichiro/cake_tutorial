<?php

App::uses('AppModel', 'Model');
App::uses('DatabaseUtil', 'Lib/Utility');

class ThreadModelException extends Exception {}

class Thread extends AppModel {
  public function selectAll() {
    $sql = DatabaseUtil::sqlReader('select_threads_orderby_created.sql');

    try {
      $result = $this->query($sql);
    } catch (Exception $e) {
      throw $e;
    }

    if ($result === false) {
      throw new ThreadModelException();
    }

    return $result;
  }

  public function selectThreadByUID($params) {
    $sql = DatabaseUtil::sqlReader('select_thread_by_thread_uid.sql');

    try {
      $result = $this->query($sql, $params);
    } catch (Exception $e) {
      throw $e;
    }

    if ($result === false) {
      throw new ThreadModelException();
    }
    if ($result === []) {
      throw new NotFoundException();
    }

    return $result[0];
  }

  public function insertThread($params) {
    $sql = DatabaseUtil::sqlReader('insert_thread.sql');

    try {
      $result = $this->query($sql, $params);
    } catch (Exception $e) {
      throw $e;
    }

    if ($result === false) {
      throw new ThreadModelException();
    }

    return true;
  }
}
