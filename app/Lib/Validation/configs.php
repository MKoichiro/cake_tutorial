<?php

App::uses('Checker', 'Lib/Validation');

$regExps = [
  'alphaNumeric' => '/^[a-zA-Z0-9]+$/',
  'email' => '/^[^@]+@[^@]+\.[^@]+$/',
];

return $configs = [
  'registerUser' => [
    'display_name' => [
      ['message' => '最大30文字です。',                 'checker' => Checker::max(30)],
    ],
    'email' => [
      ['message' => '最大254文字です。',                'checker' => Checker::max(254)],
      ['message' => '4文字以上です。',                  'checker' => Checker::min(3)],
      ['message' => 'メールアドレスの形式が不正です。', 'checker' => Checker::notMatch($regExps['email'])],
    ],
    'password' => [
      ['message' => '8文字以上です。',                  'checker' => Checker::min(8)],
      ['message' => '最大72文字です。',                 'checker' => Checker::max(72)],
      ['message' => '半角英数字のみ。',                 'checker' => Checker::notMatch($regExps['alphaNumeric'])],
    ],
    'password_confirmation' => [
      ['message' => 'パスワードと一致しません。',       'checker' => Checker::notEqualTo('password')],
    ],
  ],
  'loginUser' => [
    'email' => [
      ['message' => 'メールアドレスの形式が不正です。', 'checker' => Checker::notMatch($regExps['email'])],
    ],
  ],
];
