<!-- <?=
    $this->element('debug', [
        'arrays' => [
            '$threadData'             => isset($threadData) ? $threadData : null,
            '$validationErrors' => isset($validationErrors) ? $validationErrors : null,
        ],
    ]);
?> -->

<div class="view-container threads-home">
    <div class="threads form">
        <form action="/cake_tutorial/threads" id="ThreadForm" method="post" accept-charset="utf-8">
            <?= $this->element('Form/methodImplier', ['method' => 'post']); ?>

            <fieldset>
                <legend>新規スレッドを作成</legend>

                <small><span class="required-star">*</span>の付いた項目は必須です。</small>

                <div class="input text required">
                    <label for="ThreadTitle">タイトル</label>
                    <div class="input-error">
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
                </div>

                <div class="input textarea">
                    <label for="ThreadDescription">スレッド説明</label>
                    <div class="input-error">
                        <textarea
                            name="data[Thread][description]"
                            maxlength="5000"
                            rows="10"
                            id="ThreadDescription"
                        ><?= isset($threadData['description']) ? h($threadData['description']) : ''; ?></textarea>
                        <?php if (isset($validationErrors['description'])): ?>
                            <?= $this->element('Form/errorMessages', ['errorMessages' => $validationErrors['description']]); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </fieldset>

            <div class="submit">
                <button class="app-btn" type="submit">作成</button>
            </div>
        </form>
    </div>
</div>

