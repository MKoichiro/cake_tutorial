<div class="view-container threads-create">
    <div class="form-wrap">
        <form action="<?= $rootPath; ?>/threads/confirm" id="threadNewForm" method="post" accept-charset="utf-8">
            <fieldset>
                <legend>新規スレッドを作成</legend>
                <div class="input text required">
                    <label for="threadTitle">スレッドタイトル</label>
                    <div class="input-error">
                        <input
                            name="data[Thread][thread_title]"
                            maxlength="100"
                            type="text"
                            id="threadTitle"
                            value="<?= isset($requestData['Thread']['thread_title']) ? h($requestData['Thread']['thread_title']) : ''; ?>"
                            required="required"
                        >
                        <?= $this->element('Form/errorMessages', ['errors' => $validationErrors, 'key' => 'thread_title']); ?>
                    </div>
                </div>

                <div class="input textarea">
                    <label for="threadDescription">スレッド説明</label>
                    <div class="input-error">
                        <textarea
                            name="data[Thread][thread_description]"
                            maxlength="5000"
                            rows="10"
                            id="threadDescription"
                        ><?= isset($requestData['Thread']['thread_description']) ? h($requestData['Thread']['thread_description']) : ''; ?></textarea>
                        <?= $this->element('Form/errorMessages', ['errors' => $validationErrors, 'key' => 'thread_description']); ?>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>最初のコメントを設定</legend>
                <div class="input textarea">
                    <label for="commentBody">コメント内容</label>
                    <div class="input-error">
                        <textarea
                            name="data[Comment][comment_body]"
                            maxlength="5000"
                            rows="10"
                            id="commentBody"
                        ><?= isset($requestData['Comment']['comment_body']) ? h($requestData['Comment']['comment_body']) : ''; ?></textarea>
                        <?= $this->element('Form/errorMessages', ['errors' => $validationErrors, 'key' => 'comment_body']); ?>
                    </div>
                </div>
            </fieldset>

            <div class="submit">
                <button class="app-btn" type="submit">確認</button>
            </div>
        </form>
    </div>
</div>