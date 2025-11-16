<?php
$stepNumberMap = [
    'register' => 1,
    'confirm' => 2,
    'complete' => 3,
];

$stepNumber = $stepNumberMap[$step];
?>

<nav class="step-bar-container">
    <ol class="step-bar">
        <li class="<?= $stepNumber >= 1 ? 'active' : '' ?> <?= $stepNumber === 1 ? 'current' : '' ?>">
            <span class="material-symbols-outlined">
                person_add
            </span>
            <br>
            <span>ユーザー情報入力</span>
        </li>

        <li class="<?= $stepNumber >= 2 ? 'active' : '' ?> <?= $stepNumber === 2 ? 'current' : '' ?>">
            <span class="material-symbols-outlined">
                fact_check
            </span>
            <br>
            <span>内容確認</span>
        </li>

        <li class="<?= $stepNumber >= 3 ? 'active' : '' ?> <?= $stepNumber === 3 ? 'current' : '' ?>">
            <span class="material-symbols-outlined">
                check_circle
            </span>
            <br>
            <span>登録完了</span>
        </li>
    </ol>
</nav>