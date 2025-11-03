<!-- <?= 
    $this->element('debug', [
        'arrays' => [
            '$exceptSecrets'    => isset($exceptSecrets) ? $exceptSecrets : null,
            '$secrets'          => isset($secrets) ? $secrets : null,
        ],
    ]);
?> -->

<div class="view-container user-confirm">
    <?= $this->element('progress_bar', ['step' => 'confirm']); ?>
    <p>入力内容に問題がなければ、登録ボタンを押してください。</p>
    
    <div class="confirm-table-wrap">
        <table class="confirm-table">
            <colgroup>
                <col class="col-label">
                <col class="col-value">
            </colgroup>
            <tbody>
                <tr>
                    <th>表示名</th>
                    <td><?= h($exceptSecrets['display_name']); ?></td>
                </tr>
                <tr>
                    <th>メールアドレス</th>
                    <td><?= h($exceptSecrets['email']); ?></td>
                </tr>
                <tr>
                    <th>パスワード</th>
                    <td>********</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="submit submit-confirm">
        <button class="app-btn" form="prev" type="submit">
            <span class="material-symbols-outlined">
                arrow_back_ios
            </span>
            <?= __('戻って編集'); ?>
        </button>
        <button class="app-btn" form="next" type="submit">
            <?= __('登録'); ?>
            <span class="material-symbols-outlined">
                arrow_forward_ios
            </span>
        </button>
    </div>
    
    <form id="prev" action="/cake_tutorial/users/register" method="get"></form>
    <form id="next" action="/cake_tutorial/users/complete" method="post">
        <?= $this->element('Form/methodImplier', ['method' => 'post']); ?>
            <input
                name="data[User][password]"
                type="hidden"
                value="<?= h($secrets['password']); ?>"
            >
    </form>
</div>
