<!-- <?=
    $this->element('debug', [
        'arrays' => [
            '$loginUser' => isset($loginUser) ? $loginUser : null,
        ],
    ]);
?> -->

<div class="view-container user-complete">
    <p class="welcome-message">
        <a href="#"><?= $loginUser['display_name'] ?></a>さん、掲示板アプリへようこそ！
    </p>
    
    <p>何からはじめますか？</p>
    
    <nav>
        <ul>
            <li>
                <a class="app-btn" href="#">
                    <span class="material-symbols-outlined">
                        home
                    </span>
                    サイトホームへ
                </a>
            </li>
            <li>
                <a class="app-btn" href="#">
                    <span class="material-symbols-outlined">
                        account_circle
                    </span>
                    マイページへ
                </a>
            </li>
            <li>
                <a class="app-btn" href="#">
                    <span class="material-symbols-outlined">
                        post_add
                    </span>
                    スレッドを立てる
                </a>
            </li>
        </ul>
    </nav>
</div>

