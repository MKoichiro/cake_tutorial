<?php

App::uses('AppModel', 'Model');
App::uses('SqlResolver', 'Lib/SqlResolver');

/**
 * Model->query() の返り値が false の場合を念のため区別するための例外クラス
 * 
 * NOTE: Model->query() が失敗した場合、ほとんどの場合例外をスローするが、
 *       ドキュメントに失敗時には false を返すと明記があるためハンドリングする必要がある。
 */
class ModelException extends Exception {}

/**
 * 同じクエリタイプ同士の処理の共通化と、異なるクエリ間の差分吸収を目的としたモデルクラス
 */
class BaseModel extends AppModel {

    /**
     * モデルの関数が返す値を標準化するための共通関数
     * 
     * @param string $queryType クエリ種別
     * @param mixed  $result    クエリ実行結果
     * @return mixed 変換後の返却値
     * @throws ModelException クエリ失敗時
     */
    protected function standardizeResult($queryType, $result) {
        if ($result === false) {
            throw new ModelException();
        }

        switch ($queryType) {
            case 'insertOne':
            case 'insertMany':
                // return $this->query('SELECT LAST_INSERT_ID() AS id;')[0][0]['id'];
                // return $this->getDataSource()->lastInsertId();
                return true;
            case 'selectOne':
                return $result === [] ? $result : $result[0];
            case 'selectMany':
                return $result;
            case 'aggregate':
                return array_values($result[0][0])[0];

            // validateQueryInfo() により実際には到達不可。
            default:
                throw new InvalidArgumentException(
                    'Invalid argument: $queryInfo["queryType"] is not supported.'
                );
        }
    }

    /**
     * executeSql($queryInfo) の引数ガード処理
     * 
     * @param array{ queryType: string, queryKey: string, params?: array } $queryInfo
     * @return bool バリデーション成功時に true を返す
     * @throws InvalidArgumentException バリデーション失敗時
     */
    private function validateQueryInfo($queryInfo) {
        switch (true) {
            case !is_array($queryInfo):
                throw new InvalidArgumentException(
                    'Invalid Type of Argument: $queryInfo must be an array.'
                );
            case !isset($queryInfo['queryType']) || !isset($queryInfo['queryKey']):
                throw new InvalidArgumentException(
                    'Invalid argument: $queryInfo must contain queryType and queryKey.'
                );
            case !in_array($queryInfo['queryType'], SqlResolver::queryTypes(), true):
                throw new InvalidArgumentException(
                    'Invalid argument: $queryInfo["queryType"] is not supported.'
                );
            case !is_string($queryInfo['queryKey']):
                throw new InvalidArgumentException(
                    'Invalid Type of Argument: $queryInfo["queryKey"] must be a string.'
                );
            case array_key_exists('params', $queryInfo) && !is_array($queryInfo['params']):
                throw new InvalidArgumentException(
                    'Invalid Type of Argument: $queryInfo["params"] must be an array.'
                );
            default:
                break;
        }
        return true;
    }

    /**
     * SQL実行の共通関数
     * 
     * @param array{ queryType: string, queryKey: string, params: array } $queryInfo
     * @return mixed クエリ実行結果の変換後の返却値
     */
    protected function executeSql($queryInfo) {
        $this->validateQueryInfo($queryInfo);

        $queryType  = $queryInfo['queryType'];
        $params     = $queryInfo['params'] ?? [];
        $sql        = SqlResolver::resolve($queryType, $queryInfo['queryKey']);
        CakeLog::write('debug', 'queryType: ' . $queryType, 'sql');
        CakeLog::write('debug', 'SQL: ' . $sql, 'sql');
        CakeLog::write('debug', 'PARAMS: ' . json_encode($params), 'sql');

        // TODO: query vs fetchAll の使い分け要否の検討
        // $db = $this->getDataSource();
        switch ($queryType) {
            case 'insertOne':
                // $db->fetchAll($sql, $params);
                // $result = $db->lastInsertId();
                // break;
            case 'insertMany':
            case 'selectOne':
                // NOTE: 例えば、見つからなかったときに
                //       NotFoundException をスローするような場合はここで。
            case 'selectMany':
            case 'aggregate':
            default:
                if (empty($params)) {
                    $result = $this->query($sql);
                } else {
                    // $result = $db->fetchAll($sql, $params);
                    $result = $this->query($sql, $params);
                }
        }

        return $this->standardizeResult($queryType, $result);
    }
}
