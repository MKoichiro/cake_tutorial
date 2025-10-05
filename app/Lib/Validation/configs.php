<?php

require __DIR__ . '/Checker.php';

$regExps = [
  'alphaNumeric' => '/^[a-zA-Z0-9]+$/',
  'email' => '/^[^@]+@[^@]$/',
];

return $configs = [
  'registerUser' => [
    'display_name' => [
      ['message' => '最大30文字です。', 'checker' => Checker::max(30)],
    ],
    'email' => [
      ['message' => '最大254文字です。', 'checker' => Checker::max(254)],
      ['message' => '4文字以上です。', 'checker' => Checker::min(3)],
    ],
    'password' => [
      ['message' => '8文字以上です。', 'checker' => Checker::min(8)],
      ['message' => '最大72文字です。', 'checker' => Checker::max(72)],
      ['message' => '半角英数字のみ。', 'checker' => Checker::notMatch($regExps['alphaNumeric'])],
    ],
    'password_confirmation' => [
      ['message' => 'パスワードと一致しません。', 'checker' => Checker::notEqualTo('password')],
    ],
  ],
];
