<?php

App::uses('BaseModel', 'Model');

class Thread extends BaseModel {
    public function insert($params) {
        return $this->executeSql([
            'queryType' => 'insertOne',
            'queryKey'  => 'thread',
            'params'    => $params,
        ]);
    }

    public function selectAll() {
        return $this->executeSql([
            'queryType' => 'selectMany',
            'queryKey'  => 'threads_withUsers',
        ]);
    }

    public function selectByUid($params) {
        return $this->executeSql([
            'queryType' => 'selectOne',
            'queryKey'  => 'thread_withUser_byUid',
            'params'    => $params,
        ]);
    }

    public function selectByUserUid($params) {
        return $this->executeSql([
            'queryType' => 'selectMany',
            'queryKey'  => 'threads_byUserUid',
            'params'    => $params,
        ]);
    }
}
