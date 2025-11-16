<div class="view-container users-register">
    <?= $this->element('step_bar', ['step' => 'register']); ?>

    <div class="form-wrap">
        <form action="<?= $rootPath; ?>/users/confirm" id="userDisplayForeForm" method="post" accept-charset="utf-8">
            <fieldset>
                <legend>ユーザー情報を入力してください
                    <small><span class="required-star">*</span>の付いた項目は必須です。</small>
                </legend>

                <div class="input text required">
                    <label for="userDisplayName">表示名</label>
                    <div class="input-error">
                        <input
                            name="data[User][display_name]"
                            maxlength="30"
                            type="text"
                            id="userDisplayName"
                            value="<?= isset($safeUserData['display_name']) ? h($safeUserData['display_name']) : ''; ?>"
                            required="required"
                        >
                        <?= $this->element('Form/errorMessages', ['errors' => $validationErrors, 'key' => 'display_name']); ?>
                    </div>
                </div>

                <div class="input email required">
                    <label for="userEmail">メールアドレス</label>
                    <div class="input-error">
                        <input
                            name="data[User][email]"
                            maxlength="254"
                            type="email"
                            id="userEmail"
                            value="<?= isset($safeUserData['email']) ? h($safeUserData['email']) : ''; ?>"
                            required="required"
                        >
                        <?= $this->element('Form/errorMessages', ['errors' => $validationErrors, 'key' => 'email']); ?>
                    </div>
                </div>

                <div class="input password required">
                    <label for="userPassword">パスワード</label>
                    <div class="input-error">
                        <input
                            name="data[User][password]"
                            type="password"
                            id="userPassword"
                            value="<?= isset($safeUserData['password']) ? $safeUserData['password'] : ''; ?>"
                            required="required"
                        >
                        <?= $this->element('Form/errorMessages', ['errors' => $validationErrors, 'key' => 'password']); ?>
                    </div>
                </div>

                <div class="input password required">
                    <label for="userPasswordConfirmation">パスワード (確認)</label>
                    <div class="input-error">
                        <input
                            name="data[User][password_confirmation]"
                            maxlength="72"
                            type="password"
                            id="userPasswordConfirmation"
                            value="<?= isset($safeUserData['password_confirmation']) ? $safeUserData['password_confirmation'] : ''; ?>"
                            required="required"
                        >
                        <?= $this->element('Form/errorMessages', ['errors' => $validationErrors, 'key' => 'password_confirmation']); ?>
                    </div>
                </div>
            </fieldset>

            <div class="submit">
                <button class="app-btn" type="submit">確認へ</button>
            </div>
        </form>
    </div>
</div>