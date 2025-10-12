<?=
  $this->element('Debug/debug', [
    'arrays' => [
      '$threads' => $threads,
    ],
  ]);
?>

<h1>スレッド一覧</h1>

<?php if (empty($threads)): ?>
  <p>スレッドが見つかりません。</p>
<?php else: ?>
  <ul>
    <?php foreach ($threads as $thread): ?>
      <li>
        <a href="#">
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
