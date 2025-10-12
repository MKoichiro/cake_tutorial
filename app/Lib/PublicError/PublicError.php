<?php

/**
 * 最終的にユーザーに表示するエラーを表現するクラス
 */
class PublicError {
  /**
   * 予め定義されたエラーの種類
   * - unknown:     不明なエラー;       PublicError の仕様違反を吸収、意図的には割り当てない
   * - unexpected:  予期せぬエラー;     開発時のミスを吸収、公開メソッドの引数違反などに割り当てる
   * - server:      サーバーエラー;     php の例外など、サーバー側で発生したエラーに割り当てる
   * - db:          データベースエラー; データベースの接続失敗やクエリ失敗に割り当てる
   * - auth:        認証エラー;         トークンの不正やユーザー情報の不一致に割り当てる
   * - timeout:     タイムアウトエラー; 処理が指定時間内に完了しなかった場合に割り当てる
   * - validation:  入力内容エラー;     ユーザーからの入力が不正な場合に割り当てる
   */
  private static $types = ['unknown', 'unexpected', 'server', 'db', 'auth', 'timeout', 'validation'];

  /**
   * 各エラー種類に対応するテンプレートメッセージ
   */
  private static $templateMessages = [
    'unknown'    => '不明なエラーが発生しました。',
    'unexpected' => '予期せぬエラーが発生しました。',
    'server'     => 'サーバーでエラーが発生しました。',
    'db'         => 'データベースでエラーが発生しました。',
    'auth'       => '認証に失敗しました。',
    'timeout'    => '処理がタイムアウトしました。',
    'validation' => '入力内容に誤りがあります。',
  ];

  private $type;
  private $message;
  private $exception;
  private $code;

  /**
   * @param string          $type       エラーの種類。予め定義されたものを使用すること。
   * @param string|null     $message    エラーメッセージ。nullの場合は$typeに対応するテンプレートメッセージが使用される。
   * @param Exception|null  $exception  内部的に発生した例外。
   * @param int|null        $code       エラーコード。未運用のため任意。
   */
  public function __construct($type, $message = null, $exception = null, $code = null) {
    $this->setType($type);
    $this->message   = $message ?? self::$templateMessages[$type] ?? self::$templateMessages['unknown'];
    $this->exception = $exception;
    $this->code      = $code;
  }
  private function setType($type) {
    $this->type = in_array($type, self::$types) ? $type : 'unknown';
  }

  public function getType()    { return $this->type; }
  public function getMessage() { return $this->message; }
  public function getException(){ return $this->exception; }
  public function getCode()    { return $this->code; }
}
