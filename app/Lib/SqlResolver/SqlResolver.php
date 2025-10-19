<?php

App::uses('Configure', 'Core');

class SqlResolver {
    /**
     * @const array  QUERY_TYPES    クエリ種別のリスト
     * @const array  QUERY_FILE_MAP クエリ種別と参照する SQL 文の存在するファイル名のマッピング
     * @const string SQL_DIRECTORY  SQL 文が存在するディレクトリのパス
     */
    private const QUERY_TYPES = [
        'insertOne',    // １件登録
        'insertMany',   // 複数件登録
        'selectOne',    // １件取得
        'selectMany',   // 複数件取得
        'aggregate',    // 単一の値を返す集計操作
    ];
    private const QUERY_FILE_MAP = [
        'insertOne'  => 'insertOne.php',
        'insertMany' => 'insertMany.php',
        'selectOne'  => 'selectOne.php',
        'selectMany' => 'selectMany.php',
        'aggregate'  => 'aggregations.php',
    ];
    private const SQL_DIRECTORY = APP . 'Config' . DS . 'SQL';

    /**
     * @var array $cachedQueries ファイル単位でキーを持ち、読み込んだクエリ群をキャッシュするための静的プロパティ
     */
    private static $cachedQueries = [];

    /**
     * クエリ種別のリストを公開するためのゲッターメソッド
     * 
     * @return array クエリ種別のリスト
     */
    public static function queryTypes() {
        return self::QUERY_TYPES;
    }

    /**
     * 指定されたクエリ種別とクエリキーを SQL 文に解決する
     * 
     * @param string $queryType クエリ種別
     * @param string $queryKey  クエリを識別するキー
     * @return string 指定されたクエリ種別とクエリキーに対応する SQL 文
     * @throws InvalidArgumentException 指定されたクエリ種別またはクエリキーが不正な場合
     */
    public static function resolve($queryType, $queryKey) {
        if (!array_key_exists($queryType, self::QUERY_FILE_MAP)) {
            throw new InvalidArgumentException(
                'Invalid argument: $queryType is not supported.'
            );
        }

        $fileName = self::QUERY_FILE_MAP[$queryType];
        if (!isset(self::$cachedQueries[$queryType])) {
            self::$cachedQueries[$queryType] = include(self::SQL_DIRECTORY . DS . $fileName);
        }

        if (!array_key_exists($queryKey, self::$cachedQueries[$queryType])) {
            throw new InvalidArgumentException(
                "Invalid argument: SQL ID '$queryKey' not found in $fileName."
            );
        }

        return self::$cachedQueries[$queryType][$queryKey];
    }
}
