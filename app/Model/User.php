<?php

App::uses('BaseModel', 'Model');

class User extends BaseModel {
  public function insert($params) {
    return $this->executeSql([
      'queryType' => 'insertOne',
      'queryKey'  => 'user',
      'params'    => $params,
    ]);
  }

  public function selectWithSecretsByEmail($params) {
    return $this->executeSql([
      'queryType' => 'selectOne',
      'queryKey'  => 'user_withSecrets_byEmail',
      'params'    => $params,
    ]);
  }

  public function countByEmail($params) {
    return $this->executeSql([
      'queryType' => 'aggregate',
      'queryKey'  => 'count_users_byEmail',
      'params'    => $params,
    ]);
  }

  public function selectByUid($params) {
    return $this->executeSql([
      'queryType' => 'selectOne',
      'queryKey'  => 'user_byUid',
      'params'    => $params,
    ]);
  }
}
