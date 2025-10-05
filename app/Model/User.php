<?php
App::uses('AppModel', 'Model');

class DuplicateEmailException extends Exception {}

class User extends AppModel {
  public function insertUser($params) {
    $sql = require '../Config/sql.php';
    $this->log('SQL: ' . $sql, 'info');
    $this->log('PARAMS: ' . print_r($params, true), 'info');
    // INSERT文の実行
    try {
      $result = $this->query($sql, $params);
      $this->log('SQL RESULT: ' . '$result = ' . print_r($result, true), 'info');

      // 成功時は falsy な [] が返る場合があるので、厳密に false と比較する
      return $result === false ? false : true;
    } catch (PDOException $e) {
      // email重複（既存ユーザー）はDBに近いのでModel側で例外を検出しておく
      $errorInfo = $e->errorInfo;
      $sqlState = $errorInfo[0];
      $driverErrorCode = $errorInfo[1];
      $driverErrorMessage = $errorInfo[2];
      CakeLog::debug('PDOException in UserService::register(), $e->errorInfo: ' . print_r($errorInfo, true));

      // 判定条件
      $emailDuplication = (
        $sqlState === '23000' && $driverErrorCode === 1062          // UNIQUE制約違反（MySQL）
        && strpos($driverErrorMessage, 'uk_users_email') !== false  // emailカラムのUNIQUE制約違反
      );
      CakeLog::debug('$emailDuplication: ' . ($emailDuplication ? 'true' : 'false'));

      if ($emailDuplication) {
        throw new DuplicateEmailException();
      }

      // email 重複以外の PDOException は上位にスロー
      throw $e;
    }
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
}
