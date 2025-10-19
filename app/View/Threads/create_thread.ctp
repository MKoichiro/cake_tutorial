<?=
  $this->element('debug', [
    'arrays' => [
      '$threadData'       => isset($threadData) ? $threadData : null,
      '$validationErrors' => isset($validationErrors) ? $validationErrors : null,
    ],
  ]);
?>

<div class="threads form">
  <form action="/cake_tutorial/threads" id="ThreadForm" method="post" accept-charset="utf-8">
    <?= $this->element('Form/methodImplier', ['method' => 'post']); ?>

    <fieldset>
      <legend>Add Thread</legend>

      <div class="input text required">
        <label for="ThreadTitle">Title</label>
        <input
          name="data[Thread][title]"
          maxlength="100"
          type="text"
          id="ThreadTitle"
          value="<?= isset($threadData['title']) ? $threadData['title'] : ''; ?>"
          required="required"
        >
        <?php if (isset($validationErrors['title'])): ?>
          <?= $this->element('Form/errorMessages', ['errorMessages' => $validationErrors['title']]); ?>
        <?php endif; ?>
      </div>

      <div class="input textarea">
        <label for="ThreadDescription">Description</label>
        <textarea
          name="data[Thread][description]"
          maxlength="5000"
          rows="10"
          id="ThreadDescription"
        >
          <?= isset($threadData['description']) ? h($threadData['description']) : ''; ?>
        </textarea>
        <?php if (isset($validationErrors['description'])): ?>
          <?= $this->element('Form/errorMessages', ['errorMessages' => $validationErrors['description']]); ?>
        <?php endif; ?>
      </div>
    </fieldset>

    <div class="submit">
      <input type="submit" value="Submit">
    </div>
  </form>
</div>
