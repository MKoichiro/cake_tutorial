<?= debug($this->viewVars); ?>
<div class="view-container app-card-context threads-home">
    <h1 class="app-page-title home">
        <span class="material-symbols-outlined">
            home
        </span>
        <span>
            スレッド一覧
        </span>
    </h1>

    <?= $this->element('new_thread_link'); ?>

    <?php if ($threadsWithUsersData === null): ?>
        <p>スレッドの取得に失敗しました。</p>
    <?php elseif ($threadsWithUsersData === []): ?>
        <p>投稿は見つかりませんでした。</p>
    <?php else: ?>
        <ul class="app-card-container threads-list">
            <?php foreach ($threadsWithUsersData as $data): ?>
                <?= $this->element('thread', [
                    'threadData' => $data,
                    'userData' => $data,
                    'loginUserId' => $loginUserId,
                    'displayFlags' => ['ownerFlag' => true, 'editBtn' => false],
                ]); ?>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>