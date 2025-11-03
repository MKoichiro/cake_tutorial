<div
    id="<?= $key; ?>Message"
    class="<?= !empty($params['type']) ? $params['type'] : 'message'; ?>"
>
    <?php if (!empty($params['type'])): ?>
        <span class="material-symbols-outlined" aria-hidden="true">
            <?=
                match ($params['type']) {
                    'success' => 'check_circle',
                    'error'   => 'error',
                    'info'    => 'info',
                    default   => 'notification_important',
                };
            ?>
        </span>
    <?php endif; ?>
    <span class="message-content">
        <?= $message; ?>
    </span>

    <button>
        <span class="material-symbols-outlined">
            close
        </span>
    </button>
</div>
