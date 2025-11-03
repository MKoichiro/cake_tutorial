<!-- <?=
    $this->element('debug', [
        'arrays' => [
            '$threads' => isset($threads) ? $threads : null,
        ],
    ]);
?> -->

<div class="view-container threads-home">
    <h1>スレッド一覧</h1>
    
    <a class="app-btn" href="/cake_tutorial/threads">新規スレッド作成</a>
    
    <?php if (empty($threads)): ?>
        <p>スレッドが見つかりません。</p>
    <?php else: ?>
        <ul class="threads-list">
            <?php foreach ($threads as $thread): ?>
                <li class="thread-card">
                    <a class="thread-link" href="/cake_tutorial/threads/<?= h($thread['threads']['uid']); ?>"></a>
                    <div class="thread-title-description">
                        <h2 class="thread-title">
                            <?= h($thread['threads']['title']); ?>
                        </h2>
                        <p class="thread-description">
                            <?=
                                is_null($thread['threads']['description'])
                                    ? 'スレッドの説明はありません。'
                                    : h($thread['threads']['description']);
                            ?>
                        </p>
                    </div>

                    <footer class="thread-meta">
                        <dl class="thread-meta-list">
                            <dt>
                                <span class="material-symbols-outlined" aria-hidden="true">
                                    account_circle
                                </span>
                                投稿ユーザー
                            </dt>
                            <dd>
                                <a class="inner-link" href="#">
                                    <?= h($thread['users']['display_name']); ?>
                                </a>
                            </dd>

                            <dt>
                                <span class="material-symbols-outlined" aria-hidden="true">
                                    calendar_today
                                </span>
                                投稿日時
                            </dt>
                            <dd>
                                <time datetime="<?= h($thread['threads']['created_datetime']); ?>">
                                    <?= h($thread['threads']['created_datetime']); ?>
                                </time>
                            </dd>

                            <dt>
                                <span class="material-symbols-outlined" aria-hidden="true">
                                    moving
                                </span>
                                アクティビティ
                            </dt>
                            <dd>
                                <time datetime="<?= h($thread['threads']['created_datetime']); ?>">
                                <!-- <?= h($thread['threads']['updated_datetime']); ?> -->
                                 0000000000000000000
                                </time>
                            </dd>
                        </dl>
                    </footer>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
