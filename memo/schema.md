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

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT,
  uid CHAR(36) NOT NULL,

  display_name VARCHAR(30) NOT NULL,
  email VARCHAR(254) NOT NULL,
  password_hash CHAR(60),

  created_by VARCHAR(36) NOT NULL,
  created_datetime DATETIME DEFAULT NOW(),
  updated_by VARCHAR(36) NOT NULL,
  updated_datetime DATETIME DEFAULT NOW(),

  PRIMARY KEY(id),
  CONSTRAINT uk_users_uid UNIQUE (uid),
  CONSTRAINT uk_users_email UNIQUE (email)
);
```
