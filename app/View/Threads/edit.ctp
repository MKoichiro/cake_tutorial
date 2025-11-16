<?php
$isError = $validationErrors !== [];
$threadTitle = $isError ? $requestData['Thread']['thread_title'] : $threadData['thread_title'];
$threadDescription = $isError ? $requestData['Thread']['thread_description'] : $threadData['thread_description'];
?>

<div class="view-container threads-edit">
    <div class="form-wrap">
        <form id="threadEditForm" action="<?= $rootPath; ?>/threads/<?= $threadData['thread_uid']; ?>/update" method="post" accept-charset="utf-8">
            <?= $this->element('Form/notifyMethod', ['method' => 'PUT']); ?>
            <fieldset>
                <legend>スレッドを編集</legend>

                <div class="input text required">
                    <label for="threadTitle">スレッドタイトル</label>
                    <div class="input-error">
                        <input
                            name="data[Thread][thread_title]"
                            maxlength="100"
                            type="text"
                            id="threadTitle"
                            value="<?= h($threadTitle); ?>"
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
                            maxlength="1000"
                            rows="10"
                            id="threadDescription"
                        ><?= $threadDescription; ?></textarea>
                        <?= $this->element('Form/errorMessages', ['errors' => $validationErrors, 'key' => 'thread_description']); ?>
                    </div>
                </div>
            </fieldset>
        </form>

        <form id="prev" action="<?= $rootPath; ?>/users/<?= $userData['user_uid']; ?>" method="get">
            <input name="prev" type="hidden" value="true">
        </form>

        <div class="submit-button-separate">
            <button class="app-btn form="prev" type="submit">
                <span class="material-symbols-outlined">
                    arrow_back_ios
                </span>
                <span>
                    キャンセル
                </span>
            </button>
            <button form="threadEditForm" class="app-btn" type="submit">更新</button>
        </div>
    </div>
</div>