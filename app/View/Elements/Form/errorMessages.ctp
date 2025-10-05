<?php if(isset($errorMessages)): ?>
<div class="error">
  <ul>
    <?php foreach($errorMessages as $message): ?>
      <li><?= $message; ?></li>
    <?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>
