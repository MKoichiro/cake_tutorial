<div id="dialog-backdrop"></div>

<dialog id="dialog">
    <button id="dialog-close-btn" type="button">
        <span class="material-symbols-outlined">
            close
        </span>
    </button>
    <h2><?= $message; ?></h2>
    <div>
        <h3 id="dialog-title"><!-- js で挿入 --></h3>
        <p id="dialog-content"><!-- js で挿入 --></p>
    </div>
    <div class="button-separate">
        <button id="dialog-cancel-btn" class="app-btn" type="button">
            <span class="material-symbols-outlined">
                close
            </span>
            キャンセル
        </button>
        <button class="app-btn" id="dialog-execute-btn" form="dialog-form" type="submit">
            <span class="material-symbols-outlined">
                delete
            </span>
            削除
        </button>
    </div>
    <form id="dialog-form" action="" method="post" style="display: none;">
        <?= $this->element('Form/notifyMethod', ['method' => $method]); ?>
    </form>
</dialog>
