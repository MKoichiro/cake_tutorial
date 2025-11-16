<div
    id="<?= $key; ?>"
    class="<?= $params['type'] !== [] ? $params['type'] : 'message'; ?>"
>
    <?php if ($params['type'] !== []): ?>
        <span class="material-symbols-outlined">
            <?=
                match ($params['type']) {
                    'success' => 'check_circle',
                    'error' => 'error',
                    'info' => 'info',
                    default => 'notification_important',
                };
            ?>
        </span>
    <?php endif; ?>

    <span class="message-content">
        <?= $message; ?>
    </span>

    <button id="flash-close">
        <span class="material-symbols-outlined">
            close
        </span>
    </button>
</div>