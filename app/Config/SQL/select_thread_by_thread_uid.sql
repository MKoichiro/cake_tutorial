SELECT * FROM threads
INNER JOIN users ON threads.user_id = users.user_id
WHERE threads.uid = :uid;