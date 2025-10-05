app\Config\routes.php
```php
...
Router::connect(
	'/registrations',
	['[method]' => 'GET', 'controller' => 'registrations', 'action' => 'displayForm']
);
Router::connect(
	'/registrations/confirm',
	['[method]' => 'POST', 'controller' => 'registrations', 'action' => 'displayConfirm']
);
Router::connect(
	'/registrations/complete',
	['[method]' => 'POST', 'controller' => 'registrations', 'action' => 'displayComplete']
);
...
```

app\Model\User.php
```php
<?php
App::uses('AppModel', 'Model');

class User extends AppModel {
  public function insertUser($sql, $params) {
    $result = $this->query($sql, $params);
    $this->log('SQL RESULT: ' . '$result = ' . ($result ? 'true' : 'false'), 'info');

    return $result;
  }
}
```

app\Config\sql.php
```php
<?php

return $insertUser = <<<SQL
INSERT INTO users (uid, display_name, email, password_hash, created_by, updated_by)
VALUES (:uid, :display_name, :email, :password_hash, :uid, :uid);
SQL;
```

app\Controller\RegistrationsController.php
```php
<?php
App::uses('AppController', 'Controller');
include('../Lib/Validation/Validator.php');
include('../Service/UserService.php');

class RegistrationsController extends AppController {

  public function displayForm() {
    $this->request->allowMethod('get');

    $this->set('userInput', $this->Session->read('userInput'));
    $this->set('validationErrors', $this->Session->read('validationErrors'));
    $this->Session->delete('userInput');
    $this->Session->delete('validationErrors');

    $this->render('form');
  }

  public function validation($userInput) {
    $validator = new Validator();
    $isValid = $validator->execute($userInput, 'registerUser');
    if (!$isValid) {
      $this->Session->write('validationErrors', $validator->getErrorMessages());
      return $this->redirect(['[method]' => 'GET', 'controller' => 'registrations', 'action' => 'displayForm']);
    }
  }
  public function displayConfirm() {
    $this->request->allowMethod('post');
    $userInput = $this->request->data['User'];
    $this->Session->write('userInput', $userInput);

    $this->validation($userInput);

    $this->set('userInput', $userInput);
    return $this->render('confirm');
  }

  public function register($userInput) {
    $service = new UserService();
    $ok = $service::register($userInput);
    $errorMessage = $service->getLastError();
    if (!$ok) {
      $this->Session->delete('userInput');
      $this->Session->delete('validationErrors');

      $this->Flash->error(__($errorMessage['message']));
      return $this->redirect('/registrations');
    }
  }
  public function displayComplete() {
    $this->request->allowMethod('post');
    $userInput = $this->Session->read('userInput');

    $this->register($userInput);

    $this->Flash->success(__('登録に成功しました。'));
    return $this->render('complete');
  }
}
```

app\Lib\Validation\Validator.php
```php
<?php
class Validator {
  private $errorMessages;
  private $configs;

  public function __construct() {
    $this->errorMessages = [];
    $this->configs = require __DIR__ . '/configs.php';
  }

  private function setFieldErrorMessages($fieldName, $fieldErrorMessages) {
    $this->errorMessages[$fieldName] = $fieldErrorMessages;
  }

  private function setErrorMessages($formInput, $formName) {
    foreach ($this->configs[$formName] as $fieldName => $configs) {
      $fieldErrorMessages = [];
      foreach ($configs as $config) {
        $checker = $config['checker'];
        if ($checker($formInput, $fieldName)) {
          $fieldErrorMessages[] = $config['message'];
        }

        if (isset($config['exit']) && $config['exit']) {
          break;
        }
      }
      if (!empty($fieldErrorMessages)) {
        $this->setFieldErrorMessages($fieldName, $fieldErrorMessages);
      }
    }
  }

  public function getErrorMessages() {
    return $this->errorMessages;
  }

  public function execute($formInput, $formName) {
    $this->setErrorMessages($formInput, $formName);
    $isValid = empty($this->errorMessages);
    return $isValid;
  }
}
```

app\Service\UserService.php
```php
<?php

include('../Config/sql.php');
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');
App::uses('User', 'Model');

class UserService {
  private static $lastError = null;
  public static function getLastError() {
    return self::$lastError;
  }

  public static function register($formInput) {
    $sql = require '../Config/sql.php';

    $passwordHasher = new BlowfishPasswordHasher();
    $params = [
      'uid' => CakeText::uuid(),
      'display_name' => $formInput['display_name'],
      'email' => $formInput['email'],
      'password_hash' => $passwordHasher->hash($formInput['password']),
    ];

    $userModel = new User();

    try {
      $result = $userModel->insertUser($sql, $params);
      // insertUser() 内部の Model::query() は、失敗するほとんどの場合、上流で例外をスローするのだが、
      // ドキュメントに失敗時にはfalseと返すと明記がある以上念のためハンドリングする必要がある
      if ($result === false) {
        self::$lastError = [
          'code' => 'db_error',
          'message' => 'DB実行に失敗しました。'
        ];
      }
      return $result;
    } catch (PDOException $e) {
      $msg = $e->getMessage();
      
      // email重複（既存ユーザー）は別途処理
      $errorInfo = $e->errorInfo;
      $sqlState = $errorInfo[0];
      $driverErrorCode = $errorInfo[1];
      $driverErrorMessage = $errorInfo[2];
      CakeLog::debug('PDOException in UserService::register(), $e->errorInfo: ' . print_r($errorInfo, true));

      // 判定条件
      $emailDuplication = (
        // UNIQUE制約違反（MySQL）
        $sqlState === '23000' && $driverErrorCode === 1062
        // emailカラムのUNIQUE制約違反
        && strpos($driverErrorMessage, 'uk_users_email') !== false
      );
      CakeLog::debug('$emailDuplication: ' . ($emailDuplication ? 'true' : 'false'));

      if ($emailDuplication) {
        CakeLog::error('DB ERROR in UserService::register(): ' . $msg);
        self::$lastError = [
          'code' => 'duplicate_email',
          'message' => 'このメールアドレスは既に登録されています。'
        ];
        return false;
      }

      // email 重複以外の PDOException は汎用DB例外
      CakeLog::error('DB ERROR in UserService::register(): ' . $msg);
      self::$lastError = [
        'code' => 'db_exception',
        'message' => 'サーバーでエラーが発生しました。'
      ];
      return false;
    } catch (Exception $e) {
      // PDOException 以外の例外はひとまとめにハンドリングするぐらいが現実解
      CakeLog::error('DB ERROR in UserService::register(): ' . $e->getMessage());
      self::$lastError = [
        'code' => 'db_exception',
        'message' => 'サーバーでエラーが発生しました。'
      ];
      return false;
    }
  }
}
```

app\Config\database.php
```php
class DATABASE_CONFIG {

	public $default = [
		'datasource' => 'Database/Mysql',
		'persistent' => false,
		'host' => 'localhost',
		'login' => 'training',
		'password' => 'password',
		'database' => 'message_board',
		'prefix' => '',
		'encoding' => 'utf8mb4',
	];

	public $test = array(
		'datasource' => 'Database/Mysql',
		'persistent' => false,
		'host' => 'localhost',
		'login' => 'user',
		'password' => 'password',
		'database' => 'test_database_name',
		'prefix' => '',
		//'encoding' => 'utf8',
	);
}
```

app\View\Registrations\form.ctp
```ctp
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
```

app\View\Registrations\confirm.ctp
```ctp
<pre>
  <?php isset($userInput) && var_dump($userInput); ?>
</pre>

<h1>入力確認画面</h1>

<div>
  <ul>
    <?php foreach($userInput as $fieldName => $value): ?>
    <li>
      <span><?= $fieldName;?></span>
      <span><?= $value; ?></span>
    </li>
    <?php endforeach;?>
  </ul>
</div>

<form id="testForm" action="/cake_tutorial/registrations/complete" method="post"></form>
<div class="submit">
  <button form="testForm" type="submit"><?= __('登録'); ?></button>
</div>
```

app\View\Registrations\complete.ctp
```ctp
<h1>ようこそ掲示板アプリへ！</h1>

<h2>何からはじめますか？</h2>

<ul>
  <li><a href="#">サイトホームへ</a></li>
  <li><a href="#">マイページへ</a></li>
  <li><a href="#">さっそくスレッドを立てる</a></li>
</ul>
```

app\View\Elements\Form\errorMessages.ctp
```ctp
<?php if(isset($errorMessages)): ?>
<div class="error">
  <ul>
    <?php foreach($errorMessages as $message): ?>
      <li><?= $message; ?></li>
    <?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>
```

app\tmp\logs\debug.log
```plaintext
2025-10-05 06:04:01 Info: SQL RESULT: $result = false
```