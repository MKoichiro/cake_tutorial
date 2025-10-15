<?=
  $this->element('Debug/debug', [
    'arrays' => [
      '$threadWithAuthorData'       => $threadWithAuthorData,
      '$commentsWithAuthorsData'     => $commentsWithAuthorsData,
      '$inputCommentData' => $inputCommentData,
      '$validationErrors' => $validationErrors,
    ],
  ]);
?>

<?php
  $threadData = $threadWithAuthorData['threads'];
  $authorData = $threadWithAuthorData['users'];
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

<div>
  <div>
    <?php if (empty($commentsData)): ?>
      <p>コメントが見つかりません。</p>
    <?php else: ?>
      <ul>
        <?php foreach ($commentsWithAuthorsData as $data): ?>
          <li>
            <strong><?= h($data['users']['display_name']); ?></strong>
            <time><?= h($data['comments']['created_datetime']); ?></time>
            <p><?= h($data['comments']['body']); ?></p>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>

  <div class="comments form">
    <form action="/cake_tutorial/threads/<?= h($threadData['uid']); ?>/createComment" id="CommentForm" method="post" accept-charset="utf-8">
      <?= $this->element('Form/methodImplier', ['method' => 'post']); ?>

      <fieldset>
        <legend>Add Comment</legend>
        <div class="input textarea">
          <label for="CommentBody">Description</label>
          <textarea
            name="data[Comment][body]"
            maxlength="5000"
            rows="10"
            id="CommentBody"
          ><?= isset($inputCommentData['body']) ? h($inputCommentData['body']) : ''; ?></textarea>
          <?php if (isset($validationErrors['body'])): ?>
            <?= $this->element('Form/errorMessages', ['errorMessages' => $validationErrors['body']]); ?>
          <?php endif; ?>
        </div>
      </fieldset>

      <div class="submit">
        <input type="submit" value="Submit">
      </div>
    </form>
  </div>
</div>
