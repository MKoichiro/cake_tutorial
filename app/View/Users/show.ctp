<?php
$this->Html->script(
    ['/js/users/show.js'],
    ['block' => 'scriptBottom', 'defer' => true]
); ?>
<?= $this->element('dialog', ['message' => '本当にこのスレッドを削除しますか？', 'method' => 'delete']); ?>

<div class="view-container users-show">
    <?php if ($userData === null): ?>
        <p>ユーザーデータの取得に失敗しました。</p>
    <?php else: ?>
        <h1 class="app-page-title">
            <span class="material-symbols-outlined">
                account_circle
            </span>
            <span>
                <?= $isAuthorizedAsOwner ? 'マイページ' : StringUtil::displayFormat($userData['users']['display_name']); ?>
            </span>
        </h1>

        <section class="parent-section">
            <h2 class="app-section-title">
                <span class="material-symbols-outlined">
                    info
                </span>
                <span>
                    ユーザー情報
                </span>
            </h2>
            <dl class="user-info-list">
                <?php if ($isAuthorizedAsOwner): ?>
                    <dt>
                        <dd class="unavthed">
                            <?= StringUtil::displayFormat($userData['users']['display_name']); ?>
                        </dd>
                    </dt>
                <?php else: ?>
                    <dt>
                    </dt>
                    <dd>
                        <div id="name-display-mode">
                            <span id="name-display">
                                <?= StringUtil::displayFormat($userData['users']['display_name']); ?>
                            </span>
                            <button id="name-enter-edit-btn" type="button">
                                <span class="material-symbols-outlined">
                                    edit
                                </span>
                                <span>
                                    edit
                                </span>
                            </button>
                        </div>
                        <div id="name-edit-mode">
                            <input id="name-edit-input" type="text" value="name-update" type="text">
                            <button id="name-edit-submit" form="name-update" type="submit">
                                <span class="material-symbols-outlined">
                                    send
                                </span>
                            </button>
                        </div>
                        <form id="name-update" action="" accept-charset="utf-8"></form>
                    </dd>
                    <dt>メールアドレス</dt>
                    <dd>
                        <div id="email-display-mode">
                            <?= h($userData['users']['email']); ?>
                            <button id="email-enter-edit-btn" type="button">
                                <span class="material-symbols-outlined">
                                    edit
                                </span>
                                <span>
                                    edit
                                </span>
                            </button>
                        </div>
                        <div id="email-edit-mode">
                            <input id="email-edit-input" form="email-update" type="text">
                            <button id="email-edit-submit" form="email-update" type="submit">
                                <span class="material-symbols-outlined">
                                    send
                                </span>
                            </button>
                        </div>
                        <form id="email-update" action="" accept-charset="utf-8"></form>
                    </dd>
                <?php endif; ?>
            </dl>
        </section>

        <section class="parent-section">
            <h2 class="app-section-title">
                <span class="material-symbols-outlined">
                    list
                </span>
                <span>
                    ユーザーコンテンツ
                </span>
            </h2>

            <hr class="app-separator" data-content="投稿スレッド">
            <section class="app-card-context">
                <?php if ($threadsData === null): ?>
                    <p class="app-card-item">スレッドデータの取得に失敗しました。</p>
                <?php elseif (empty($threadsData)): ?>
                    <p class="app-card-item">まだ投稿がありません。</p>
                <?php else: ?>
                    <ul class="app-card-container threads-list">
                        <?php foreach ($threadsData as $data): ?>
                            <?= $this->element('thread', [
                                'threadData' => $data,
                                'userData' => $data,
                                'loginUserId' => $loginUserId,
                                'displayFlags' => ['ownerFlag' => false, 'editBtn' => true],
                            ]); ?>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <?= $this->element('new_thread_link'); ?>
            </section>

            <hr class="app-separator" data-content="投稿コメント">
            <section class="comments-section app-card-context">
                <?php if ($commentsWithThreadsData === null): ?>
                    <p class="app-card-item">コメントデータの取得に失敗しました。</p>
                <?php elseif (empty($commentsWithThreadsData)): ?>
                    <p class="app-card-item">まだ投稿がありません。</p>
                <?php else: ?>
                    <ul class="app-card-container">
                        <?php foreach ($commentsWithThreadsData as $data): ?>
                            <li class="app-card-item">
                                <p>
                                    <?= StringUtil::displayFormat($data['comments']['comment_body']); ?>
                                </p>
                                <dl class="app-meta-list">
                                    <dt>
                                        <span class="material-symbols-outlined">
                                            account_circle
                                        </span>
                                        <span>
                                            投稿ユーザー
                                        </span>
                                    </dt>
                                    <dd>
                                        <a class="underline-link" href="<?= $rootPath; ?>/users/<?= h($userData['users']['user_uid']); ?>">
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
                                        <time datetime="<?= h($data['comments']['created_datetime']); ?>">
                                            <?= h($data['comments']['created_datetime']); ?>
                                        </time>
                                    </dd>
                                    <dt>
                                        <span class="material-symbols-outlined">
                                            history_toggle_off
                                        </span>
                                        <span>
                                            最終更新
                                        </span>
                                    </dt>
                                    <dd>
                                        <time datetime="<?= h($data['comments']['updated_datetime']); ?>">
                                            <?= h($data['comments']['updated_datetime']); ?>
                                        </time>
                                    </dd>
                                    <dt>
                                        <span class="material-symbols-outlined">
                                            article
                                        </span>
                                        <span>
                                            スレッド
                                        </span>
                                    </dt>
                                    <dd>
                                        <a class="thread-link underline-link" href="<?= $rootPath; ?>/threads/<?= h($data['threads']['thread_uid']); ?>">
                                            <?= StringUtil::displayFormat($data['threads']['thread_title']); ?>
                                        </a>
                                    </dd>
                                    <dd>
                                        <a class="liner-link edit-link underline-link" href="<?= $rootPath; ?>/threads/<?= h($data['threads']['thread_uid']); ?>/comments/<?= h($data['comments']['comment_uid']); ?>/edit">
                                            edit
                                        </a>
                                    </dd>
                                </dl>
                                このコメントを編集する
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>
        </section>
    <?php endif; ?>
</div>