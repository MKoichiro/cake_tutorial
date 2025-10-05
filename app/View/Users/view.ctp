<h1><?= h($user['User']['username']); ?></h1>

<pre>
  <p>$user: </p>
  <?php var_dump($user); ?>
</pre>

<p><small>Id: <?= $user['User']['id']; ?></small></p>
<p><small>Password: <?= $user['User']['password']; ?></small></p>
<p><small>Role: <?= $user['User']['role']; ?></small></p>
<p><small>Created: <?= $user['User']['created']; ?></small></p>
<p><small>Modified: <?= $user['User']['modified']; ?></small></p>
