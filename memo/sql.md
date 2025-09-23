0. 前準備
- xamppコントロールパネルからshellを起動。
	これは、管理者権限でcmdを開き、`C:\\xampp\`に移動したのとほとんど同じ。
- 以下シェル操作
```cmd
koichiro@DESKTOP-D150Q3Q c:\xampp
# cmdターミナルの文字コード確認
# chcp
# 932(shift-jis)なら、65001(UTF-8)に変更しておく
# chcp 65001
# MySQL/MariaDB のCLIを起動し、サーバーに接続 （※ rootのパスワードは空）
# mysql -u root -p
-- 研修中に使うデータベース"training"を作成
MariaDB [(none)]> CREATE DATABASE training CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;
-- 研修中に使う一般ユーザー"training"を作成
MariaDB [(none)]> CREATE USER training@'localhost' IDENTIFIED BY 'password';
-- そのユーザーの権限を研修中データベース内の変更に限る
MariaDB [(none)]> GRANT ALL ON training.* TO 'training'@'localhost';
-- ユーザー一覧を確認
MariaDB [(none)]> SELECT User, Host FROM mysql.global_priv;
-- データベース一覧を確認
MariaDB [(none)]> SHOW DATABASES;
-- mysqlを抜けてcmdに帰る
MariaDB [(none)]> exit
# 一般ユーザーに切り替えて再度入る
# mysql -u training -p
-- training DBへ移動
USE training;
```

1. テーブル作成
MariaDB [(training)]>
CREATE TABLE users (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	last_name VARCHAR(30) NOT NULL,
	first_name VARCHAR(30) NOT NULL,
	email VARCHAR(255) NOT NULL UNIQUE COLLATE utf8mb4_unicode_ci,	-- emailは大小不問の照合順序(*_ci系)を使用
	password VARCHAR(255) NOT NULL,					-- password_hash('raw_password', PASSWORD_DEFAULT);でハッシュ化する想定なら将来性込みで255が妥当。
	remarks TEXT DEFAULT '特になし',

	-- メアドは研修用簡易チェック
	-- CONSTRAINTを使用する場合、（違反、エラー時に制約名が表示できる）
	CONSTRAINT chk_users_pw_len CHECK (CHAR_LENGTH(password) >= 8),
  	CONSTRAINT chk_users_email  CHECK (email REGEXP '^[^@]+@[^@]+\\.[^@]+$')

	-- CONSTRAINTを使用しない場合
	-- CHECK(
	-- 	CHAR_LENGTH(password) >= 8
	-- 	AND email REGEXP '^[^@]+@[^@]+\\.[^@]+$'
	-- )
);
MariaDB [(training)]>　SHOW TABLES; -- テーブルが作成されたことを確認

[MEMO]
age:
	直に置くのではなく、生年月日がベター（DATE）
gender:
	基本の二択なら、BOOLEAN（これはMYSQLではTINYINT(1)のエイリアスらしい。）
	LGBTQに配慮し「その他」「無回答」または具体名などを追加するなら、別途参照テーブルを作ってしまうのが良いかも。


2. データの追加
MariaDB [(training)]>
INSERT INTO users (last_name, first_name, email, password, remarks)
VALUES
  ('田中', 'たかし', 'TANAKA@example.com', 'password123', DEFAULT),
  ('山田', 'はなこ', 'hanako@example.com', 'password123', DEFAULT),
  ('佐藤', 'じろう', 'jiro@example.com', 'password123', '備考'),
  ('Trump', 'Donald', 'donald@trump.com', 'password123', DEFAULT);

3. データの抽出
MariaDB [(training)]> SELECT * FROM users;				-- ユーザーデータが追加されたことを確認

4. データの更新
MariaDB [(training)]> UPDATE users SET remarks = '備考3' WHERE id = 3;	-- ※ idはinsert intoの試行回数によって変動
MariaDB [(training)]> SELECT * FROM users WHERE id = 3;			-- 更新の確認
