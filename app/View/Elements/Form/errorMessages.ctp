<?php if(isset($errorMessages)): ?>
  <ul class="error">
    <?php foreach($errorMessages as $message): ?>
      <li><?= $message; ?></li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
