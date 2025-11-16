<header>
    <div class="header-container">
        <a class="app-title" href="<?= $rootPath; ?>/home">
            掲示板アプリ
        </a>

        <?php if (!isset($noHeaderNav)): ?>
            <?php if ($loginUserInfo !== []): ?>
                <a class="header-link link-user" href="<?= $rootPath; ?>/users/<?= h($loginUserInfo['user_uid']); ?>">
                    <span class="material-symbols-outlined" data-icon="account_circle">
                        account_circle
                    </span>
                    <span class="header-user-name">
                        <?= StringUtil::displayFormat($loginUserInfo['display_name']); ?>
                    </span>
                </a>

                <button class="header-btn btn-toggle-header-menu">
                    <span class="span-menu">メニュー</span>
                    <span class="span-menu-toggle-icon" class="material-symbols-outlined" data-icon="keyboard_arrow_down">
                        keyboard_arrow_down
                    </span>
                </button>

            <?php else: ?>
                <nav class="not-logged-in-nav">
                    <a class="header-link link-login" href="<?= $rootPath; ?>/login">ログイン</a>
                    <a class="header-link link-register-user" href="<?= $rootPath; ?>/users/register">新規登録</a>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <ul id="header-menu-acc-container">
        <li>
            <a class="header-link link-home" href="<?= $rootPath; ?>/home">
                <span class="material-symbols-outlined" data-icon="home">
                    home
                </span>
                <span>
                    ホーム
                </span>
            </a>
        </li>

        <li>
            <a class="header-link link-mypage" href="<?= $rootPath; ?>/users/<?= h($loginUserInfo['user_uid']); ?>">
                <span class="material-symbols-outlined" data-icon="account_circle">
                    account_circle
                </span>
                <span>
                    マイページ
                </span>
            </a>
        </li>

        <li>
            <a class="header-link link-create-thread" href="<?= $rootPath; ?>/threads/register">
                <span class="material-symbols-outlined" data-icon="post_add">
                    post_add
                </span>
                <span>
                    スレッドを立てる
                </span>
            </a>
        </li>

        <li>
            <form action="<?= $rootPath; ?>/logout" id="logout" method="post">
                <?= $this->element('Form/notifyMethod', ['method' => 'DELETE']); ?>
            </form>

            <button class="header-btn btn-logout" form="logout">
                <span class="material-symbols-outlined" data-icon="logout">
                    logout
                </span>
                <span>
                    ログアウト
                </span>
            </button>
        </li>
    </ul>
</header>