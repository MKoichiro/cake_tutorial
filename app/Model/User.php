<?php
App::uses('AppModel', 'Model');

class User extends AppModel {
  public function insertUser($sql, $params) {
    $result = $this->query($sql, $params);
    $this->log('SQL RESULT: ' . '$result = ' . print_r($result, true), 'info');

    // 成功時は falsy な [] が返る場合があるので、厳密に false と比較する
    if ($result === false) {
      return false;
    }
    return true;
  }

  public function selectUserByEmail($email) {
    $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
    $params = ['email' => $email];
    $result = $this->query($sql, $params);
    $this->log('SQL RESULT: ' . '$result = ' . print_r($result, true), 'info');

    if ($result === false || count($result) === 0) {
      return null;
    }

    return $result[0];
  }

  public function selectUserById($id) {
    $sql = "SELECT * FROM users WHERE uid = :uid LIMIT 1";
    $params = ['id' => $id];
    $result = $this->query($sql, $params);
    $this->log('SQL RESULT: ' . '$result = ' . print_r($result, true), 'info');

    if ($result === false || count($result) === 0) {
      return null;
    }

    return $result[0];
  }
}
