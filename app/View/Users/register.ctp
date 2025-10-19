<?=
  $this->element('debug', [
    'arrays' => [
      '$exceptSecrets'    => isset($exceptSecrets) ? $exceptSecrets : null,
      '$validationErrors' => isset($validationErrors) ? $validationErrors : null,
    ],
  ]);
?>

<div class="users form">
  <form action="/cake_tutorial/users/confirm" id="UserDisplayFormForm" method="post" accept-charset="utf-8">
    <?= $this->element('Form/methodImplier', ['method' => 'post']); ?>

    <fieldset>
      <legend>Add User</legend>

      <div class="input text required">
        <label for="UserDisplayName">Display Name</label>
        <input
          name="data[User][display_name]"
          maxlength="30"
          type="text"
          id="UserDisplayName"
          value="<?= isset($exceptSecrets['display_name']) ? $exceptSecrets['display_name'] : ''; ?>"
          required="required"
          >
        <?php if (isset($validationErrors['display_name'])): ?>
          <?= $this->element('Form/errorMessages', ['errorMessages' => $validationErrors['display_name']]); ?>
        <?php endif; ?>
      </div>

      <div class="input email required">
        <label for="UserEmail">Email</label>
        <input
          name="data[User][email]"
          maxlength="254"
          type="email"
          id="UserEmail"
          value="<?= isset($exceptSecrets['email']) ? $exceptSecrets['email'] : ''; ?>"
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

      <div class="input password required">
        <label for="UserPasswordConfirmation">Password Confirmation</label>
        <input
          name="data[User][password_confirmation]"
          type="password"
          id="UserPasswordConfirmation"
          required="required"
        >
        <?php if (isset($validationErrors['password_confirmation'])): ?>
          <?= $this->element('Form/errorMessages', ['errorMessages' => $validationErrors['password_confirmation']]); ?>
        <?php endif; ?>
      </div>
    </fieldset>

    <div class="submit">
      <input type="submit" value="Submit">
    </div>
  </form>
</div>
