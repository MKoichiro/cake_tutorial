<?php

return [
    // === From Users Table =========
    'user_withSecrets_byEmail' => <<<SQL
        SELECT * FROM users
        WHERE email = :email LIMIT 1;
    SQL,
    'user_byUid' => <<<SQL
        SELECT * FROM users
        WHERE uid = :uid LIMIT 1;
    SQL,

    // === From Threads Table =======
    'thread_withUser_byUid' => <<<SQL
        SELECT * FROM threads
        INNER JOIN users ON threads.user_id = users.user_id
        WHERE threads.uid = :uid LIMIT 1;
    SQL,

    // === From Comments Table ======
    // (No queries yet)
];
