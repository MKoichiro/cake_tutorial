<?php
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');
App::uses('Configure', 'Core');

class DatabaseUtil {
  // public static function sqlReader($sqlId) {
  //   $sqlDir = Configure::read('App.sqlDirectory');
  //   $filePath = APP . $sqlDir . DS . $sqlId;
  //   if (!file_exists($filePath)) {
  //     throw new InvalidArgumentException("SQL file not found: $filePath");
  //   }
  //   return file_get_contents($filePath);
  // }

    

    public static function hashPassword($password) {
        $passwordHasher = new BlowfishPasswordHasher();
        return $passwordHasher->hash($password);
    }

    public static function verifyPassword($password, $hash) {
        $passwordHasher = new BlowfishPasswordHasher();
        return $passwordHasher->check($password, $hash);
    }
}
