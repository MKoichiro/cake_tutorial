<div class="view-container authenticates-login-form">
    <div class="form">
        <form action="<?= $rootPath; ?>/login" method="post" accept-charset="utf-8">
            <?php #echo $this->element('csrf_token'); ?>
            <fieldset>
                <legend>ログイン</legend>
                <div class="input email required">
                    <label for="UserEmail">メールアドレス</label>
                    <div class="input-error">
                        <input
                            name="data[User][email]"
                            maxlength="254"
                            type="email"
                            id="UserEmail"
                            value="<?= isset($safeUserData['email']) ? h($safeUserData['email']) : ''; ?>"
                            required="required"
                        >
                        <?= $this->element('Form/errorMessages', ['errors' => $validationErrors, 'key' => 'email']); ?>
                    </div>
                </div>

                <div class="input password required">
                    <label for="UserPassword">パスワード</label>
                    <div class="input-error">
                        <input
                            name="data[User][password]"
                            type="password"
                            id="UserPassword"
                            required="required"
                        >
                        <?= $this->element('Form/errorMessages', ['errors' => $validationErrors, 'key' => 'password']); ?>
                    </div>
                </div>
            </fieldset>

            <div class="submit">
                <button class="app-btn" type="submit">ログイン</button>
            </div>
        </form>
    </div>

    <small>
        はじめてご利用の方は
        <a class="app-btn" href="<?= $rootPath; ?>/users/register">新規登録</a>
        してください。
    </small>
</div>
