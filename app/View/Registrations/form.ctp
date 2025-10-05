<pre>
  <?php isset($userInput) && var_dump($userInput); ?>
  <?php isset($validationErrors) && var_dump($validationErrors); ?>
</pre>
<div class="users form">
  <form action="/cake_tutorial/registrations/confirm" id="UserDisplayFormForm" method="post" accept-charset="utf-8">
    <div style="display:none;">
      <input type="hidden" name="_method" value="POST">
    </div>

    <fieldset>
      <legend>Add User</legend>

      <div class="input text required">
        <label for="UserDisplayName">Display Name</label>
        <input
          name="data[User][display_name]"
          maxlength="30"
          type="text"
          id="UserDisplayName"
          value="<?= isset($userInput['display_name']) ? $userInput['display_name'] : ''; ?>"
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
          value="<?= isset($userInput['password']) ? $userInput['password'] : ''; ?>"
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
          value="<?= isset($userInput['password_confirmation']) ? $userInput['password_confirmation'] : ''; ?>"
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
