<?php if (isset($errors[$key])): ?>
    <?php $errorMessages = $errors[$key]; ?>
    <?php if (is_array($errorMessages) && count($errorMessages) > 0): ?>
        <div class="error">
            <ul>
                <?php foreach ($errorMessages as $message): ?>
                    <li><?= $message; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
<?php endif; ?>