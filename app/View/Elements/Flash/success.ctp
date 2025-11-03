<div
  id="<?= $key; ?>Message"
  class="<?= !empty($params['class']) ? $params['class'] : 'message'; ?>"
>
  <?= $message; ?>
  <pre>
    <?php print_r($params); ?>
  </pre>
  <pre>
    <?php print_r($key); ?>
  </pre>
  success.ctp
</div>
