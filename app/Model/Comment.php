<?php

App::uses('BaseModel', 'Model');

class Comment extends BaseModel {
    public function insert($params) {
        return $this->executeSql([
            'queryType' => 'insertOne',
            'queryKey'  => 'comment',
            'params'    => $params,
        ]);
    }

    public function selectWithUserByThreadId($params) {
        return $this->executeSql([
            'queryType' => 'selectMany',
            'queryKey'  => 'comments_withUsers_byThreadId',
            'params'    => $params,
        ]);
    }

    public function selectWithThreadsByUserUid($params) {
        return $this->executeSql([
            'queryType' => 'selectMany',
            'queryKey'  => 'comments_withThreads_byUserUid',
            'params'    => $params,
        ]);
    }
}
