<div class="view-container users-confirm">
    <?= $this->element('step_bar', ['step' => 'confirm']); ?>

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
                    <td><?= StringUtil::displayFormat($requestData['User']['display_name']); ?></td>
                </tr>
                <tr>
                    <th>メールアドレス</th>
                    <td><?= h($requestData['User']['email']); ?></td>
                </tr>
                <tr>
                    <th>パスワード</th>
                    <td>********</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="submit-button-separate">
        <button class="app-btn" form="prev" type="submit">
            <span class="material-symbols-outlined">
                arrow_back_ios
            </span>
            <span>
                戻って編集
            </span>
        </button>
        <button class="app-btn" form="next" type="submit">
            <span>
                登録
            </span>
            <span class="material-symbols-outlined">
                arrow_forward_ios
            </span>
        </button>
    </div>

    <form id="prev" action="<?= $rootPath; ?>/users/register" method="get">
        <input name="prev" type="hidden" value="true">
    </form>
    <form id="next" action="<?= $rootPath; ?>/users/complete" method="post">
        <input name="data[User][password]" type="hidden" value="<?= h($requestData['User']['password']); ?>">
    </form>
</div>