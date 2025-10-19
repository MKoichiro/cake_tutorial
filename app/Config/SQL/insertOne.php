<?php

return [
    // === From Users Table =========
    'user' => <<<SQL
        INSERT INTO `users` (`uid`, `display_name`, `email`, `password_hash`, `created_by`, `updated_by`)
        VALUES (:uid, :display_name, :email, :password_hash, :created_by, :updated_by);
    SQL,

    // === From Threads Table =======
    'thread' => <<<SQL
        INSERT INTO `threads` (`uid`, `user_id`, `title`, `created_by`, `updated_by`)
        VALUES (:uid, :user_id, :title, :created_by, :updated_by);
    SQL,

    // === From Comments Table ======
    'comment' => <<<SQL
        INSERT INTO `comments` (`uid`, `user_uid`, `thread_id`, `body`, `created_by`, `updated_by`)
        VALUES (:uid, :user_uid, :thread_id, :body, :created_by, :updated_by);
    SQL,
];
