<?php
$threadDescriptionDisplay = StringUtil::displayFormat($requestData['Thread']['thread_description']);
$commentBodyDisplay = StringUtil::displayFormat($requestData['Comment']['comment_body']);
?>

<div class="view-container threads-confirm">

    <p>以下の内容でスレッドを作成します。</p>

    <div class="confirm-table-wrap">
        <table class="confirm-table">
            <colgroup>
                <col class="col-label">
                <col class="col-value">
            </colgroup>
            <tbody>
                <tr>
                    <th>スレッドタイトル</th>
                    <td><?= StringUtil::displayFormat($requestData['Thread']['thread_title']) ?></td>
                </tr>
                <tr>
                    <th>スレッド説明</th>
                    <td>
                        <?= $threadDescriptionDisplay === '' ? '...未入力' : $threadDescriptionDisplay; ?>
                    </td>
                </tr>
                <tr>
                    <th>コメント内容</th>
                    <td>
                        <?= $commentBodyDisplay === '' ? '...未入力' : $commentBodyDisplay; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="submit-button-separate">
        <button class="app-btn form="prev" type="submit">
            <span class="material-symbols-outlined">
                arrow_back_ios
            </span>
            <span>
                キャンセル
            </span>
        </button>
        <button class="app-btn form="next" type="submit">
            <span>
                作成
            </span>
            <span class="material-symbols-outlined">
                arrow_forward_ios
            </span>
        </button>
    </div>

    <form id="prev" action="<?= $rootPath; ?>/threads/register" method="get">
        <input name="prev" type="hidden" value="true">
    </form>
    <form id="next" action="<?= $rootPath; ?>/threads/complete" method="post">
    </form>
</div>