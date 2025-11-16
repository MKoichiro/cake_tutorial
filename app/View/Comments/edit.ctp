<?php
$commentBody = ($validationErrors !== []) ? $requestData['Comment']['comment_body'] : $commentData['comment_body'];
?>

<div class="view-container comments-edit">
    <?php echo $this->element('thread', [
        'threadData'   => ['threads' => $threadData],
        'userData'     => ['users'   => $userData],
        'loginUserId'  => $loginUserId,
        'displayFlags' => ['ownerFlag' => true, 'editBtn' => false],
    ]); ?>

    <div class="form">
        <form id="CommentEditForm" action="<?= $rootPath; ?>/threads/<?= $threadData['thread_uid']; ?>/comments/<?= $commentData['comment_uid']; ?>/update" method="post" accept-charset="utf-8">
            <?= $this->element('Form/notifyMethod', ['method' => 'PUT']); ?>
            <fieldset>
                <legend>コメントを編集</legend>

                <div class="input textarea">
                    <label for="CommentBody">コメント内容</label>
                    <div class="input-error">
                        <textarea
                            name="data[Comment][comment_body]"
                            maxlength="5000"
                            rows="10"
                            id="CommentBody"><?= $commentBody ?></textarea>
                        <?= $this->element('Form/errorMessages', ['errors' => $validationErrors, 'key' => 'comment_body']); ?>
                    </div>
                </div>
            </fieldset>
        </form>

        <form id="prev" action="<?= $rootPath; ?>/users/<?= $userData['user_uid']; ?>" method="get">
            <input name="prev" type="hidden" value="true">
        </form>
    </div>

    <div class="submit button-separate">
        <button class="app-btn" form="prev" type="submit">
            <span class="material-symbols-outlined">
                arrow_back_ios
            </span>
            戻る
        </button>
        <button form="CommentEditForm" class="app-btn" type="submit">更新</button>
    </div>
</div>
