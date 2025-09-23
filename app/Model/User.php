<?php

class User extends AppModel {
  public $validate = [
    'username' => [
      'required' => [
        'rule' => 'notBlank',
        'message' => 'A username is required',
      ],
    ],
    'password' => [
      'required' => [
        'rule' => 'notBlank',
        'message' => 'A password is required',
      ],
    ],
    'role' => [
      'valid' => [
        'rule' => [
          ['inList', ['admin', 'author']],
          'message' => 'Please enter a valid role',
          'allowEmpty' => false
        ],
      ],
    ],
  ];
  public function beforeSave($options = []) {
    if (isset($this->data[$this->alias]['password'])) {
      $passwordHasher = new BlowfishPasswordHasher();
      $this->data[$this->alias]['password'] = $passwordHasher->hash(
        $this->data[$this->alias]['password']
      );
    }
    return true;
  }
}
