<?php
// ローカル環境の設定ファイル

return array(
    // プロジェクトルートの定義
    'rootUrl' => 'http://localhost/cake_tutorial',

    // DB接続情報
    'dbInfo' => array(
        'messageBoard' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => false,
            'host'       => 'localhost',
            'login'      => 'training',
            'password'   => 'password',
            'database'   => 'message_board',
            'prefix'     => '',
            'encoding'   => 'utf8mb4',
        ),

        // 未運用
        'additionalDb' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => false,
            'host'       => '10.100.10.100',
            'login'      => 'training',
            'password'   => 'password',
            'database'   => 'additional_db',
            'prefix'     => '',
            'encoding'   => 'utf8mb4',
        ),
    ),
);
