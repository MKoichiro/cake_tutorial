<?= 
  $this->element('Debug/debug', [
    'arrays' => [
      '$exceptSecrets'  => $exceptSecrets,
      '$secrets'        => $secrets,
    ],
  ]);
?>

<h1>入力確認画面</h1>

<div>
  <ul>
    <?php foreach($exceptSecrets as $fieldName => $value): ?>
    <li>
      <span><?= $fieldName;?></span>
      <span><?= $value; ?></span>
    </li>
    <?php endforeach; ?>
  </ul>
</div>

<form id="prev" action="/cake_tutorial/users/register" method="get"></form>
<form id="next" action="/cake_tutorial/users/complete" method="post">
  <?= $this->element('Form/methodImplier', ['method' => 'post']); ?>
    <input
      name="data[User][password]"
      type="hidden"
      value="<?= h($secrets['password']); ?>"
    >
</form>
<div class="submit">
  <button form="prev" type="submit"><?= __('戻って編集'); ?></button>
  <button form="next" type="submit"><?= __('登録'); ?></button>
</div>
