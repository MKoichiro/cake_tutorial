<?php

App::uses('AppModel', 'Model');
App::uses('DatabaseUtil', 'Lib/Utility');

class Thread extends AppModel {
  public function selectAll() {
    $sql = DatabaseUtil::sqlReader('select_threads_orderby_created.sql');

    try {
      $result = $this->query($sql);
    } catch (Exception $e) {
      throw $e;
    }

    if ($result === false) {
      throw new Exception('ThreadModel#selectAll failed');
    }

    return $result;
  }
}
