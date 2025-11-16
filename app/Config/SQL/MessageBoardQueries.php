<?php

class MessageBoardQueries {
    public const COLUMN_ALIAS = [
        // 'user_uid' => 'users.uid',
        // 'thread_uid' => 'threads.uid',
        // 'thread_title' => 'threads.title',
        // 'thread_description' => 'threads.description',
        // 'comment_body' => 'comments.body',
    ];

    public const INSERT_USER = <<<SQL
    INSERT INTO users (
        uid,
        display_name,
        email,
        password_hash,
        created_by,
        created_datetime,
        updated_by,
        updated_datetime
    ) VALUES (
        :user_uid,
        :display_name,
        :email,
        :password_hash,
        :created_by,
        STR_TO_DATE(:created_datetime, '%Y-%m-%d %H:%i:%s'),
        :updated_by,
        STR_TO_DATE(:updated_datetime, '%Y-%m-%d %H:%i:%s')
    )
    SQL;

    public const INSERT_THREAD = <<<SQL
    INSERT INTO threads (
        uid,
        user_id,
        title,
        description,
        created_by,
        created_datetime,
        updated_by,
        updated_datetime
    ) VALUES (
        :thread_uid,
        :user_id,
        :thread_title,
        :thread_description,
        :created_by,
        STR_TO_DATE(:created_datetime, '%Y-%m-%d %H:%i:%s'),
        :updated_by,
        STR_TO_DATE(:updated_datetime, '%Y-%m-%d %H:%i:%s')
    )
    SQL;

    public const INSERT_COMMENT = <<<SQL
    INSERT INTO comments (
        uid,
        thread_id,
        user_id,
        body,
        created_by,
        created_datetime,
        updated_by,
        updated_datetime
    ) VALUES (
        :comment_uid,
        :thread_id,
        :user_id,
        :comment_body,
        :created_by,
        STR_TO_DATE(:created_datetime, '%Y-%m-%d %H:%i:%s'),
        :updated_by,
        STR_TO_DATE(:updated_datetime, '%Y-%m-%d %H:%i:%s')
    )
    SQL;

    public const UPDATE_THREAD_CORE = <<<SQL
    UPDATE
        threads
    SET
        title = :thread_title,
        description = :thread_description,
        updated_by = :updated_by,
        updated_datetime = STR_TO_DATE(:updated_datetime, '%Y-%m-%d %H:%i:%s')
    WHERE
        uid = :target_thread_uid
    SQL;

    public const UPDATE_COMMENT_CORE = <<<SQL
    UPDATE
        comments
    SET
        body = :comment_body,
        updated_by = :updated_by,
        updated_datetime = STR_TO_DATE(:updated_datetime, '%Y-%m-%d %H:%i:%s')
    WHERE
        uid = :target_comment_uid
    SQL;

    public const SELECT_USER_SECRETS_BY_EMAIL = <<<SQL
    SELECT
        uid AS user_uid,
        password_hash
    FROM
        users
    WHERE
        email = :email
    SQL;

    public const SELECT_USER_BY_UID = <<<SQL
    SELECT
        user_id,
        uid AS user_uid,
        display_name,
        email,
        created_by,
        created_datetime,
        updated_by,
        updated_datetime
    FROM
        users
    WHERE
        uid = :user_uid
    SQL;

    public const SELECT_THREAD_ID_BY_UID = <<<SQL
    SELECT
        thread_id
    FROM
        threads
    WHERE
        uid = :thread_uid
    SQL;

    public const SELECT_THREAD_BY_UID = <<<SQL
    SELECT
        thread_id,
        uid AS thread_uid,
        user_id,
        title AS thread_title,
        description AS thread_description,
        created_datetime,
        updated_datetime
    FROM
        threads
    WHERE
        uid = :thread_uid
    SQL;

    public const SELECT_THREAD_WITH_USER_BY_UID = <<<SQL
    SELECT
        threads.thread_id,
        threads.uid AS thread_uid,
        threads.user_id,
        threads.title AS thread_title,
        threads.description AS thread_description,
        threads.created_datetime,
        threads.updated_datetime,
        users.user_id,
        users.uid AS user_uid,
        users.display_name
    FROM
        threads
    INNER JOIN users
        ON threads.user_id = users.user_id
    WHERE
        threads.uid = :thread_uid
    SQL;

    public const SELECT_THREADS_WITH_USERS = <<<SQL
    SELECT
        threads.thread_id,
        threads.uid AS thread_uid,
        threads.user_id,
        threads.title AS thread_title,
        threads.description AS thread_description,
        threads.created_datetime,
        threads.updated_datetime,
        users.uid AS user_uid,
        users.display_name,
        comments.created_datetime AS latest_comment_datetime
    FROM
        threads
    INNER JOIN users
        ON threads.user_id = users.user_id
    -- 該当 comments に対して threads の行を保持する挙動。INNER JOIN だと comment が未だ無いスレッドが落ちる。

    LEFT JOIN comments
        ON
            comments.thread_id = threads.thread_id
        AND
            comments.created_datetime = (
                -- MEMO: サブクエリで最大値のものを取ってくる
                SELECT
                    MAX(comments.created_datetime)
                FROM
                    comments
                WHERE
                    comments.thread_id = threads.thread_id
            )
    ORDER BY
        threads.created_datetime DESC,
        latest_comment_datetime DESC,
        threads.thread_id DESC
    SQL;

    public const SELECT_THREADS_BY_USERID = <<<SQL
    SELECT
        threads.uid AS thread_uid,
        threads.title AS thread_title,
        threads.description AS thread_description,
        threads.created_datetime,
        threads.updated_datetime,
        comments.created_datetime AS latest_comment_datetime
    FROM
        threads
    LEFT JOIN comments
        ON
            comments.thread_id = threads.thread_id
        AND
            comments.created_datetime = (
                -- MEMO: サブクエリで最大値のものを取ってくる
                SELECT
                    MAX(comments.created_datetime)
                FROM
                    comments
                WHERE
                    comments.thread_id = threads.thread_id
            )
    WHERE
        threads.user_id = :user_id
    ORDER BY
        threads.created_datetime DESC,
        threads.thread_id DESC
    SQL;

    public const SELECT_COMMENT_BY_UID = <<<SQL
    SELECT
        comments.uid AS comment_uid,
        comments.thread_id,
        comments.user_id,
        comments.body AS comment_body,
        comments.created_datetime,
        comments.updated_datetime,
        threads.uid AS thread_uid,
        threads.title AS thread_title,
        threads.description AS thread_description,
        threads.created_datetime AS thread_created_datetime,
        threads.updated_datetime AS thread_updated_datetime,
        users.uid AS user_uid,
        users.display_name
    FROM
        comments
    INNER JOIN threads
        ON comments.thread_id = threads.thread_id
    INNER JOIN users
        ON comments.user_id = users.user_id
    WHERE
        comments.uid = :comment_uid
    SQL;

    public const SELECT_COMMENTS_WITH_USERS_BY_THREADID = <<<SQL
    SELECT
        comments.uid AS comment_uid,
        comments.user_id,
        comments.body AS comment_body,
        comments.created_datetime,
        comments.updated_datetime,
        users.user_id,
        users.uid AS user_uid,
        users.display_name
    FROM
        comments
    INNER JOIN users
        ON comments.user_id = users.user_id
    WHERE
        comments.thread_id = :thread_id
    ORDER BY
        comments.created_datetime ASC,
        comments.comment_id ASC
    SQL;

    public const SELECT_COMMENTS_WITH_THREADS_BY_USERID = <<<SQL
    SELECT
        comments.uid AS comment_uid,
        comments.body AS comment_body,
        comments.created_datetime,
        comments.updated_datetime,
        threads.uid AS thread_uid,
        threads.title AS thread_title,
        threads.description AS thread_description
    FROM
        comments
    INNER JOIN threads
        ON comments.thread_id = threads.thread_id
    WHERE
        comments.user_id = :user_id
    ORDER BY
        comments.created_datetime DESC,
        comments.comment_id DESC
    SQL;

    public const COUNT_USERS_BY_EMAIL = <<<SQL
    SELECT
        COUNT(*) AS count
    FROM
        users
    WHERE
        email = :email
    SQL;
}
