<?php

include('../Config/sql.php');
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');
App::uses('User', 'Model');

class UserService {
  private static $lastError = null;

  public static function lastError() {
    return self::$lastError;
  }

  public function register($formInput) {
    $sql = require '../Config/sql.php';

    $passwordHasher = new BlowfishPasswordHasher();
    $params = [
      'uid' => CakeText::uuid(),
      'display_name' => $formInput['display_name'],
      'email' => $formInput['email'],
      'password_hash' => $passwordHasher->hash($formInput['password']),
    ];

    $userModel = new User();

    $userModel->selectUserByEmail('k@g');

    try {
      $result = $userModel->insertUser($sql, $params);
      // insertUser() 内部の Model::query() は、失敗するほとんどの場合、上流で例外をスローするのだが、
      // ドキュメントに失敗時には false を返すと明記がある以上念のためハンドリングする必要がある
      if ($result === false) {
        self::$lastError = [
          'code' => 'db_error',
          'message' => 'DB実行に失敗しました。'
        ];
      }
      return $result;

    } catch (PDOException $e) {
      $msg = $e->getMessage();
      
      // email重複（既存ユーザー）は別途処理
      $errorInfo = $e->errorInfo;
      $sqlState = $errorInfo[0];
      $driverErrorCode = $errorInfo[1];
      $driverErrorMessage = $errorInfo[2];
      CakeLog::debug('PDOException in UserService::register(), $e->errorInfo: ' . print_r($errorInfo, true));

      // 判定条件
      $emailDuplication = (
        // UNIQUE制約違反（MySQL）
        $sqlState === '23000' && $driverErrorCode === 1062
        // emailカラムのUNIQUE制約違反
        && strpos($driverErrorMessage, 'uk_users_email') !== false
      );
      CakeLog::debug('$emailDuplication: ' . ($emailDuplication ? 'true' : 'false'));

      if ($emailDuplication) {
        CakeLog::error('DB ERROR in UserService::register(): ' . $msg);
        self::$lastError = [
          'code' => 'duplicate_email',
          'message' => 'このメールアドレスは既に登録されています。'
        ];
        return false;
      }

      // email 重複以外の PDOException は汎用DB例外
      CakeLog::error('DB ERROR in UserService::register(): ' . $msg);
      self::$lastError = [
        'code' => 'db_exception',
        'message' => 'サーバーでエラーが発生しました。'
      ];
      return false;

    } catch (Exception $e) {
      // PDOException 以外の例外はひとまとめにハンドリングするぐらいが現実解
      CakeLog::error('DB ERROR in UserService::register(): ' . $e->getMessage());
      self::$lastError = [
        'code' => 'db_exception',
        'message' => 'サーバーでエラーが発生しました。'
      ];
      return false;
    }
  }
}
