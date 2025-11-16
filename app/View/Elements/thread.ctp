<?php
$isOwner = $threadData['threads']['user_id'] === $loginUserId;
$displayOwnerFlag = $isOwner && $displayFlags['ownerFlag']; // マイスレッドをレンダーするか
$displayEditBtn = $isOwner && $displayFlags['editBtn']; // 編集ボタンをレンダーするか
?>

<li class="app-card-item thread-card-item <?= $displayOwnerFlag ? 'highlight' : '' ?>">
    <a class="thread-link" href="<?= $rootPath; ?>/threads/<?= h($threadData['threads']['thread_uid']); ?>"></a>

    <div class="app-card-content-item-flag">
        <?php if ($displayOwnerFlag): ?>
            <small class="app-card-flag-container">
                <span class="material-symbols-outlined">
                    flag
                </span>
                <span>
                    マイスレッド
                </span>
            </small>
        <?php endif; ?>
        <div class="app-card-content-container">
            <div class="thread-title-description">
                <h2 class="thread-title">
                    <?= StringUtil::displayFormat($threadData['threads']['thread_title']); ?>
                </h2>

                <p class="thread-description">
                    <?php
                        if ($threadData['threads']['thread_description'] === null
                            || $threadData['threads']['thread_description'] === ''
                        ) {
                            echo '... スレッドの説明はありません。';
                        } else {
                            echo StringUtil::displayFormat($threadData['threads']['thread_description']);
                        }
                    ?>
                </p>
            </div>
        </div>
    </div>

    <footer class="app-meta-container">
        <dl class="app-meta-list">
            <dt>
                <span class="material-symbols-outlined">
                    account_circle
                </span>
                <span>
                    投稿者
                </span>
            </dt>
            <dd>
                <a class="inner-link underline-link" href="<?= $rootPath; ?>/users/<?= h($userData['users']['user_uid']); ?>">
                    <?= StringUtil::displayFormat($userData['users']['display_name']); ?>
                </a>
            </dd>

            <dt>
                <span class="material-symbols-outlined">
                    calendar_today
                </span>
                <span>
                    投稿日時
                </span>
            </dt>
            <dd>
                <time datetime="<?= h($threadData['threads']['created_datetime']); ?>">
                    <?= h($threadData['threads']['created_datetime']); ?>
                </time>
            </dd>

            <?php if (isset($threadData['comments']['latest_comment_datetime'])): ?>
                <dt>
                    <span class="material-symbols-outlined">
                        moving
                    </span>
                    <span>
                        アクティビティ
                    </span>
                </dt>
                <dd>
                    <?php $latestCommentDatetime = $threadData['comments']['latest_comment_datetime']; ?>
                    <?php if ($latestCommentDatetime === null): ?>
                        ...
                    <?php else: ?>
                        <time datetime="<?= h($latestCommentDatetime); ?>">
                            <?= h($latestCommentDatetime); ?>
                        </time>
                    <?php endif; ?>
                </dd>
            <?php endif; ?>
        </dl>
    </footer>

    <?php if ($displayEditBtn): ?>
        <a class="inner-link edit-link underline-link" href="<?= $rootPath; ?>/threads/<?= h($threadData['threads']['thread_uid']); ?>/edit">
            <span class="material-symbols-outlined">
                edit
            </span>
            <span>
                このスレッドを編集する
            </span>
        </a>
        <button
            class="inner-link edit-link underline-link thread-delete-btn"
            data-action="<?= $rootPath; ?>/threads/<?= h($threadData['threads']['thread_uid']); ?>/delete"
            data-dialog-title="スレッドを削除"
            data-content="<?= h($threadData['threads']['thread_title']); ?>"
        >
            <span class="material-symbols-outlined">
                delete
            </span>
            <span>
                このスレッドを削除する
            </span>
        </button>
    <?php endif; ?>
</li>