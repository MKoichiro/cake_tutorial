<?php

return [
    // === COUNT ====================
    'count_users_byEmail' => <<<SQL
        SELECT COUNT(*) AS `count` FROM `users` WHERE `email` = :email;
    SQL,

    // === SUM ======================
    // (No queries yet)

    // ...その他、結果が単一の値となる集計操作のクエリ
];
