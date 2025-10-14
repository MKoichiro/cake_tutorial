<?=
  $this->element('Debug/debug', [
    'arrays' => [
      '$threadData' => $threadData,
      '$authorData' => $authorData,
    ],
  ]);
?>

<div>
  <div>
    <h1>
      <?= h($threadData['title']) ?>
    </h1>

    <div>
      <div>
        <span>投稿者:</span>
        <a href="#">
          <?= h($authorData['display_name']) ?>
        </a>
      </div>

      <div>
        <span>作成日時:</span>
        <small>
          <time>
            <?= h($threadData['created_datetime']) ?>
          </time>
        </small>
      </div>
    </div>
  </div>

  <p>
    <?= h($threadData['description']) ?>
  </p>
</div>
