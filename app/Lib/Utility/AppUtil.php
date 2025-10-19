<?php

App::uses('ArrayUtil', 'Lib/Utility');

class AppUtil {
  private const SECRET_KEYS = ['password_hash'];
  public static function secretsRemover(array $user) {
    return ArrayUtil::remove($user, self::SECRET_KEYS);
  }
}
