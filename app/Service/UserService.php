<?php

App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');
App::uses('User', 'Model');

class UserService {
  private static $lastError = null;

  public static function lastError() {
    return self::$lastError;
  }

  public function register($formInput) {
    $passwordHasher = new BlowfishPasswordHasher();
    $params = [
      'uid' => CakeText::uuid(),
      'display_name' => $formInput['display_name'],
      'email' => $formInput['email'],
      'password_hash' => $passwordHasher->hash($formInput['password']),
    ];

    $userModel = new User();

    try {
      $result = $userModel->insertUser($params);
      // insertUser() 内部の Model::query() は、失敗するほとんどの場合、上流で例外をスローするのだが、
      // ドキュメントに失敗時には false を返すと明記がある以上念のためハンドリングする必要がある
      if ($result === false) {
        self::$lastError = [
          'code' => 'db_error',
          'message' => 'DB実行に失敗しました。'
        ];
      }
      return $result;
    } catch (DuplicateEmailException $e) {
      self::$lastError = [
        'code' => 'duplicate_email',
        'message' => 'そのメールアドレスは既に登録されています。'
      ];
      return false;
    } catch (Exception $e) {
      CakeLog::error('DB ERROR in UserService::register(): ' . $e->getMessage());
      self::$lastError = [
        'code' => 'db_exception',
        'message' => 'サーバーでエラーが発生しました。'
      ];
      return false;
    }
  }
}
