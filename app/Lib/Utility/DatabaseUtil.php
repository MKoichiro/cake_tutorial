<?php
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');
App::uses('Configure', 'Core');

class DatabaseUtil {
  public static function sqlReader($file, $dir = null) {
    if ($dir === null) {
      $dir = Configure::read('App.sqlDir');
    }
    if (!file_exists($dir . $file)) {
      throw new InvalidArgumentException('SQLファイルが存在しません。');
    }
    $sql = file_get_contents($dir . $file);
    return $sql;
  }

  // public static function sqlBuilder($params, $file, $dir = null) {
  //   $sql = self::sqlReader($file, $dir);
  //   foreach ($params as $key => $value) {
  //     $sql = str_replace(':' . $key, $value, $sql);
  //   }
  //   return $sql;
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

// class SQL {
//   private $sql;

//   public function __construct($sql) {
//     $this->sql = $sql;
//   }

//   private function setSQL($sql) {
//     $this->sql = $sql;
//   }
//   public function getSQL() {
//     return $this->sql;
//   }

//   public function addWhere($conditions) {
//     $currentSql = $this->getSQL();
//     // 最後の文字がセミコロンなら削除
//     $currentSql = rtrim($currentSql, '; ');

//     $where = ' WHERE ';
//     $and = ' AND ';
//     // すでに WHERE 句がある場合は AND でつなげる
//     if (stripos($currentSql, ' WHERE ') !== false) {
//       $currentSql .= $and;
//     } else {
//       $currentSql .= $where;
//     }
//     $addition = '';
//     $first = true;
//     foreach ($conditions as $colAndOperator => $col) {
//       if (!$first) {
//         $addition .= $and;
//       }
//       $addition .= "$colAndOperator :$col";
//       $first = false;
//     }
//     $this->setSQL($currentSql . $addition);
//     return $this;
//   }
// }
