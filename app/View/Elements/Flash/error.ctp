<!-- <div
  id="<?= $key; ?>Message"
  class="<?= !empty($params['class']) ? $params['class'] : 'message'; ?>"
>
  <?= $message; ?>
  エラー系
</div> -->

<?= $this->element('Flash/default', ['message' => $message, 'params' => $params, 'key' => $key]); ?>
