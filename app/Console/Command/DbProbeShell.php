<?php
App::uses('AppShell', 'Console/Command');
App::uses('User', 'Model'); // usersテーブルを想定。別モデルでもOK

// app\Console\cake.bat db_probe main

class DbProbeShell extends AppShell {
    public function main() {
        $User = new User();

        $sql = 'SELECT * FROM users WHERE 1=0'; // 0件確定
        $this->out("SQL: $sql");

        $this->out("\nModel::query() の戻り:");
        $res1 = $User->query($sql);
        var_dump($res1); // ← 期待: bool(false)

        $this->out("\nDboSource::fetchAll() の戻り:");
        $db = $User->getDataSource();
        $res2 = $db->fetchAll($sql);
        var_dump($res2); // ← 期待: bool(false)

        $this->out("\nBaseModel::executeSql() の戻り:");
        $res3 = $User->countByEmail(['email' => 'test1@example.com']);
        var_dump($res3); // ← 期待: int(0)
    }
}
