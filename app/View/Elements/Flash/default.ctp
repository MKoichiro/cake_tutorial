<div
  id="<?= $key; ?>Message"
  class="<?= !empty($params['class']) ? $params['class'] : 'message'; ?>"
>
  <?= $message; ?>
  <pre><?= print_r($params); ?></pre>
  <pre>
<?= print_r($key); ?>
  </pre>
  <?php if ($key === 'success'): ?>
    成功系
  <?php elseif ($key === 'error'): ?>
    エラー系
  <?php endif; ?>
</div>
