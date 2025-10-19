<?php

return [
    // === From Users Table =========
    // (No queries yet)
    
    // === From Threads Table =======
    'threads_withUsers' => <<<SQL
        SELECT * FROM `threads`
        INNER JOIN `users` ON `threads`.`user_id` = `users`.`user_id`
        ORDER BY `threads`.`created_datetime` DESC;
    SQL,
    'threads_byUserUid' => <<<SQL
        SELECT * FROM `threads`
        WHERE `threads`.`created_by` = :uid
        ORDER BY `threads`.`created_datetime` DESC;
    SQL,

    // === From Comments Table ======
    'comments_withThreads_byUserUid' => <<<SQL
        SELECT * FROM `comments`
        INNER JOIN `threads` ON `comments`.`thread_id` = `threads`.`id`
        WHERE `comments`.`created_by` = :user_uid
        ORDER BY `comments`.`created_at` DESC;
    SQL,
    'comments_withUsers_byThreadId' => <<<SQL
        SELECT * FROM `comments`
        INNER JOIN `users` ON `comments`.`user_id` = `users`.`user_id`
        WHERE `comments`.`thread_id` = :thread_id
        ORDER BY `comments`.`created_datetime` ASC;
    SQL,
];
