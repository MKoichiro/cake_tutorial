<?=  debug($this->viewVars); ?>
<div class="view-container threads-show">

    <section class="app-card-context app-card-item-highlight">
        <div class="app-meta-list-container">
            <?php if ($threadWithAuthorData['users']['user_id'] === $loginUserId): ?>
                <div class="app-card-item-highlight-flag">
                    <small class="app-card-flag-container">
                        <span class="material-symbols-outlined">
                            flag
                        </span>
                        <span>
                            マイ投稿
                        </span>
                    </small>
                </div>
            <?php endif; ?>
            <div class="app-card-content-container">
                <h2 class="thread-title">
                    <?= StringUtil::displayFormat($threadWithAuthorData['threads']['thread_title']); ?>
                </h2>

                <p class="thread-description">
                    <?php
                        if ($threadWithAuthorData['threads']['thread_description'] === null
                            || $threadWithAuthorData['threads']['thread_description'] === ''
                        ) {
                            echo '... スレッドの説明はありません。';
                        } else {
                            echo StringUtil::displayFormat($threadWithAuthorData['threads']['thread_description']);
                        }
                    ?>
                </p>
            </div>

            <footer class="app-meta-list">
                <dl>
                    <div>
                        <dt>
                            <span class="material-symbols-outlined">
                                account_circle
                            </span>
                            <span>
                                投稿者
                            </span>
                        </dt>
                        <dd>
                            <a class="underline-link" href="<?= $rootPath; ?>/users/<?= h($threadWithAuthorData['users']['user_uid']); ?>">
                                <?= StringUtil::displayFormat($threadWithAuthorData['users']['display_name']); ?>
                            </a>
                        </dd>
                    </div>

                    <div>
                        <dt>
                            <span class="material-symbols-outlined">
                                calendar_today
                            </span>
                            <span>
                                投稿日時
                            </span>
                        </dt>
                        <dd>
                            <time datetime="<?= h($threadWithAuthorData['threads']['created_datetime']); ?>">
                                <?= $threadWithAuthorData['threads']['created_datetime']; ?>
                            </time>
                        </dd>
                    </div>
                </dl>
            </footer>
        </div>
    </section>

    <hr class="app-separator" data-content="コメント">

    <section class="app-card-context comments-container">
        <?php if ($commentsWithAuthorData === []): ?>
            <p class="app-card-item">まだ投稿がありません。</p>
        <?php else: ?>
            <ul class="app-card-container comments-list">
                <?php foreach ($commentsWithAuthorData as $data): ?>
                    <li class="app-card-item <?= $data['users']['user_id'] === $loginUserId ? 'highlight' : '' ?>">
                        <div class="app-card-item-highlight-flag">
                            <?php if ($data['users']['user_id'] === $loginUserId): ?>
                                <small class="app-card-flag-container">
                                    <span class="material-symbols-outlined">
                                        flag
                                    </span>
                                    <span>
                                        マイコメント
                                    </span>
                                </small>
                            <?php endif; ?>
                        </div>
                        <div class="app-card-content-container">
                            <p>
                                <?= StringUtil::displayFormat($data['comments']['comment_body']); ?>
                            </p>
                        </div>
                        <dl class="comment-meta-list">
                            <dt>
                                <?php if ($data['comments']['user_id'] === $threadWithAuthorData['threads']['user_id']): ?>
                                    <span class="material-symbols-outlined">
                                        admin_panel_settings
                                    </span>
                                <?php else: ?>
                                    <span class="material-symbols-outlined">
                                        person
                                    </span>
                                <?php endif; ?>
                            </dt>
                            <dd>
                                <a class="underline-link" href="<?= $rootPath; ?>/users/<?= h($data['users']['user_uid']); ?>">
                                    <?= StringUtil::displayFormat($data['users']['display_name']); ?>
                                </a>
                            </dd>

                            <dt>
                                <span class="material-symbols-outlined">
                                    history_toggle_off
                                </span>
                            </dt>
                            <dd>
                                <time datetime="<?= h($data['comments']['updated_datetime']); ?>">
                                    <?= h($data['comments']['updated_datetime']); ?>
                                </time>
                            </dd>
                        </dl>
                        <div class="comment-like-container">
                            <button class="favorite-button">
                                <span class="material-symbols-outlined is-liked">
                                    favorite
                                </span>
                            </button>
                            <span>
                                <?= h($data[0]['comment_like_count']); ?>
                            </span>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <section>
        <?php if (!$isAuthorizedAsLoginUser): ?>
            <small>
                コメントを投稿するには
                <a class="app-btn" href="<?= $rootPath; ?>/login">ログイン</a>
                または
                <a class="app-btn" href="<?= $rootPath; ?>/users/register">新規登録</a>
                してください。
            </small>
        <?php else: ?>
            <div class="form-wrap">
                <form id="commentForm" action="<?= $rootPath; ?>/threads/<?= h($threadWithAuthorData['threads']['thread_uid']); ?>/comments/complete" method="post" accept-charset="utf-8">
                    <fieldset>
                        <legend>コメントを投稿する</legend>
                        <div class="input text required">
                            <label for="commentBody">コメント内容</label>
                            <div class="input-error">
                                <textarea
                                    name="data[Comment][comment_body]"
                                    maxlength="5000"
                                    rows="10"
                                    id="commentBody"
                                ><?= isset($requestedCommentData['Comment']['comment_body']) ? h($requestedCommentData['Comment']['comment_body']) : ''; ?></textarea>
                                <?= $this->element('Form/errorMessages', ['errors' => $validationErrors, 'key' => 'comment_body']); ?>
                            </div>
                        </div>
                    </fieldset>
                    <div class="submit">
                        <button class="app-btn" type="submit">投稿</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </section>
</div>