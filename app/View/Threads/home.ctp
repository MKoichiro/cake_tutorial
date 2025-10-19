<?=
  $this->element('debug', [
    'arrays' => [
      '$threads' => isset($threads) ? $threads : null,
    ],
  ]);
?>

<h1>スレッド一覧</h1>

<a href="/cake_tutorial/threads">新規スレッド作成</a>

<?php if (empty($threads)): ?>
  <p>スレッドが見つかりません。</p>
<?php else: ?>
  <ul>
    <?php foreach ($threads as $thread): ?>
      <li>
        <a href="/cake_tutorial/threads/<?= h($thread['threads']['uid']); ?>">
          <div>
            <h2>
              <?= h($thread['threads']['title']); ?>
            </h2>
            <p>
              <?=
                is_null($thread['threads']['description'])
                  ? h($thread['threads']['description'])
                  : '';
              ?>
            </p>
          </div>

          <div>
            <a href="#">
              <?= h($thread['users']['display_name']); ?>
            </a>
            <small>
              <time>
                <?= h($thread['threads']['created_datetime']); ?>
              </time>
            </small>
          </div>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
