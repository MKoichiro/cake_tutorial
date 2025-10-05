<pre>
  <?php isset($userInput) && var_dump($userInput); ?>
  <?php var_dump($_SESSION); ?>
</pre>

<h1>入力確認画面</h1>

<div>
  <ul>
    <?php foreach($userInput as $fieldName => $value): ?>
    <li>
      <span><?= $fieldName;?></span>
      <span><?= $value; ?></span>
    </li>
    <?php endforeach; ?>
  </ul>
</div>

<form id="prev" action="/cake_tutorial/registrations" method="get"></form>
<form id="next" action="/cake_tutorial/registrations/complete" method="post"></form>
<div class="submit">
  <button form="prev" type="submit"><?= __('戻って編集'); ?></button>
  <button form="next" type="submit"><?= __('登録'); ?></button>
</div>
