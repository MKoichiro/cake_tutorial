<div class="view-container users-complete">
    <p class="welcome-message">
        <a class="underline-link" href="<?= $rootPath; ?>/users/<?= h($loginUserInfo['user_uid']); ?>">
            <?= StringUtil::displayFormat($loginUserInfo['display_name']); ?>
        </a>
        さん、掲示板アプリへようこそ！
    </p>

    <p>何からはじめますか？</p>

    <nav>
        <ul>
            <li>
                <a class="app-btn" href="<?= $rootPath; ?>/home">
                    <span class="material-symbols-outlined">
                        home
                    </span>
                    <span>
                        サイトホームへ
                    </span>
                </a>
            </li>
            <li>
                <a class="app-btn" href="<?= $rootPath; ?>/users/<?= h($loginUserInfo['user_uid']); ?>">
                    <span class="material-symbols-outlined">
                        account_circle
                    </span>
                    <span>
                        マイページへ
                    </span>
                </a>
            </li>
            <li>
                <a class="app-btn" href="<?= $rootPath; ?>/threads/register">
                    <span class="material-symbols-outlined">
                        post_add
                    </span>
                    <span>
                        スレッドを立てる
                    </span>
                </a>
            </li>
        </ul>
    </nav>
</div>