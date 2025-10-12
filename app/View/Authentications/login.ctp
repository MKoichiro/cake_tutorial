<?= $this->element('Debug/debug', [
  'arrays' => [
    '$exceptSecrets' => $exceptSecrets,
    '$validationErrors' => $validationErrors,
  ],
]); ?>

<?= $this->element('Header/header'); ?>
<div class="users form">
  <form action="/cake_tutorial/login" id="UserDisplayFormForm" method="post" accept-charset="utf-8">
    <?= $this->element('Form/methodImplier', ['method' => 'POST']); ?>

    <fieldset>
      <legend>Login</legend>

      <div class="input email required">
        <label for="UserEmail">Email</label>
        <input
          name="data[User][email]"
          maxlength="254"
          type="email"
          id="UserEmail"
          value="<?= isset($userInput['email']) ? $userInput['email'] : ''; ?>"
          required="required"
        >
        <?php if (isset($validationErrors['email'])): ?>
          <?= $this->element('Form/errorMessages', ['errorMessages' => $validationErrors['email']]); ?>
        <?php endif; ?>
      </div>

      <div class="input password required">
        <label for="UserPassword">Password</label>
        <input
          name="data[User][password]"
          type="password"
          id="UserPassword"
          required="required"
        >
        <?php if (isset($validationErrors['password'])): ?>
          <?= $this->element('Form/errorMessages', ['errorMessages' => $validationErrors['password']]); ?>
        <?php endif; ?>
      </div>
    </fieldset>

    <div class="submit">
      <input type="submit" value="Submit">
    </div>
  </form>
</div>
