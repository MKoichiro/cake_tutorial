<?php

return $insertUser = <<<SQL
INSERT INTO users (uid, display_name, email, password_hash, created_by, updated_by)
VALUES (:uid, :display_name, :email, :password_hash, :uid, :uid);
SQL;
