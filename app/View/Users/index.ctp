<h1>Users</h1>
<table>
  <tr>
    <th>Id</th>
    <th>Username</th>
    <th>Action</th>
    <th>Role</th>
    <th>Created</th>
  </tr>

    <!-- ここから、$users配列をループして、投稿記事の情報を表示 -->
    <?php foreach ($users as $user): ?>
    <tr>
        <td><?= $user['User']['id']; ?></td>
        <td>
          <?php
            echo $this->Html->link(
              $user['User']['username'],
              [ 'action' => 'view', $user['User']['id'] ]
            );
          ?>
        </td>
        <td>
          <?= $this->Html->link('Edit', [ 'action' => 'edit', $user['User']['id'] ]); ?>
        </td>
        <td><?= $user['User']['role']; ?></td>
        <td><?= $user['User']['created']; ?></td>
    </tr>
    <?php endforeach; ?>
    <?php unset($user); ?>
</table>
