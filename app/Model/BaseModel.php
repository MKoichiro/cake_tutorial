<?php

App::uses('AppModel', 'Model');


/**
 * トランザクションのコンテキストの中かどうで実行を分ける
 */
class BaseModel extends AppModel {

    /**
     * クエリを実行する関数
     *
     * @param string ... $sql ... SQL 文
     * @param array ... $params ... 第一引数の SQL 文にバインドしたいパラメーター
     * @param DbDataSource $dataSource ...
     * ... ... DbDataSource クラスを継承した DbDataSource インスタンス。
     * ... ... トランザクションの文脈外の呼び出しがしたい場合は呼び出し元でインスタンスを生成して指定する。
     *
     * @return mixed $sql の結果セット
     * @throws Exception
     */
    public function executeSql($sql, $params = null, $dataSource = null) {
        CakeLog::write('debug', 'The following query has been prepared:'."\n" . $sql);
        CakeLog::write('debug', 'The following parameters will be bound:'."\n" . print_r($params, true));

        try {
            $target = $dataSource !== null ? $dataSource : $this;
            $result = $params === null
                ? $target->query($sql)
                : $target->query($sql, $params);
        } catch (Exception $e) {
            CakeLog::write('error', 'Exception: ' . $e->getMessage());
            throw $e;
        }

        // query() の仕様で失敗時に false を返す場合がある
        if ($result === false) {
            CakeLog::write('error', 'Exception: ');
            throw new InternalErrorException();
        }
        return $result;
    }
}
