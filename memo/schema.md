```cmd
chcp 65001
mysql -u root -p
```

```SQL
CREATE DATABASE message_board CHARACTER SET utf8mb4;
GRANT ALL ON message_board.* TO 'training'@'localhost';
exit;
```

```cmd
mysql -u training -p
```

```SQL
USE message_board;

CREATE TABLE `users` (
  `user_id`           INT UNSIGNED AUTO_INCREMENT,
  `uid`               CHAR(36)      NOT NULL,

  `display_name`      VARCHAR(30)   NOT NULL,
  `email`             VARCHAR(254)  NOT NULL,
  `password_hash`     CHAR(60),

  `created_by`        VARCHAR(36)   NOT NULL,
  `created_datetime`  DATETIME      NOT NULL,
  `updated_by`        VARCHAR(36)   NOT NULL,
  `updated_datetime`  DATETIME      NOT NULL,

  PRIMARY KEY(`user_id`),
  CONSTRAINT uk_users_uid   UNIQUE (`uid`),
  CONSTRAINT uk_users_email UNIQUE (`email`)
);
```

```SQL
CREATE TABLE `threads` (
  `thread_id`         INT UNSIGNED  AUTO_INCREMENT,
  `uid`               CHAR(36)      NOT NULL,

  `user_id`           INT UNSIGNED,
  `title`             VARCHAR(200)  NOT NULL,
  `description`       VARCHAR(5000),

  `created_by`        VARCHAR(36)   NOT NULL,
  `created_datetime`  DATETIME      NOT NULL,
  `updated_by`        VARCHAR(36)   NOT NULL,
  `updated_datetime`  DATETIME      NOT NULL,

  PRIMARY KEY(thread_id),
  CONSTRAINT fk_threads_user_id
    FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);
```

```SQL
INSERT INTO `threads` (`uid`, `user_id`, `title`, `description`, `created_by`, `updated_by`)
VALUES
(
  '2b44de74-a562-4c21-b566-95b21ca1edf7',
  1,
  'スレッドタイトル１－１',
  'スレッド説明１－１',
  'b43b9b74-e4e2-47b4-8783-6325a1e04876',
  'b43b9b74-e4e2-47b4-8783-6325a1e04876'
),
(
  '007a34ce-71b9-4d02-972c-fb09e1080a54',
  1,
  'スレッドタイトル１ー２',
  null,
  'b43b9b74-e4e2-47b4-8783-6325a1e04876',
  'b43b9b74-e4e2-47b4-8783-6325a1e04876'
),
(
  'f9edf833-b0d9-4cd1-b302-c5e832daf84f',
  3,
  'スレッドタイトル３ー１',
  '',
  '967f4b7f-a567-4bc9-a31b-fabc5c2c73dc',
  '967f4b7f-a567-4bc9-a31b-fabc5c2c73dc'
),
(
  '89f95bb5-502e-46e7-9173-b77ce9b1b309',
  4,
  'スレッドタイトル４ー１',
  'スレッド説明４ー１',
  'cbfc8cc8-088a-444f-8d50-221c3adae603',
  'cbfc8cc8-088a-444f-8d50-221c3adae603'
);
```

```SQL
CREATE TABLE `comments` (
  `comment_id`         INT UNSIGNED  AUTO_INCREMENT,
  `uid`               CHAR(36)      NOT NULL,

  `user_id`           INT UNSIGNED,
  `thread_id`         INT UNSIGNED,
  `body`              VARCHAR(5000) NOT NULL,

  `created_by`        VARCHAR(36)   NOT NULL,
  `created_datetime`  DATETIME      NOT NULL,
  `updated_by`        VARCHAR(36)   NOT NULL,
  `updated_datetime`  DATETIME      NOT NULL,

  PRIMARY KEY(comment_id),
  CONSTRAINT fk_comments_user_id
    FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_comments_thread_id
    FOREIGN KEY (`thread_id`) REFERENCES `threads` (`thread_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);
```

```SQL
CREATE TABLE `comment_likes` (
    `comment_like_id`   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`           INT UNSIGNED NOT NULL,        -- users.id への FK
    `comment_id`        INT UNSIGNED NOT NULL,        -- comments.id (または comment_id) への FK
    `deleted`           TINYINT(1)   NOT NULL,
    `created_by`        VARCHAR(36)  NOT NULL,
    `created_datetime`  DATETIME     NOT NULL,
    `updated_by`        VARCHAR(36)  NOT NULL,
    `updated_datetime`  DATETIME     NOT NULL,
    PRIMARY KEY (`comment_like_id`),
    CONSTRAINT fk_comment_likes_user_id
        FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
    CONSTRAINT fk_comment_likes_comment_id
        FOREIGN KEY (`comment_id`) REFERENCES `comments` (`comment_id`),
    UNIQUE KEY `uk_comment_user` (`comment_id`, `user_id`) -- 同じユーザーが同じコメントに複数いいねできないように
);
```
