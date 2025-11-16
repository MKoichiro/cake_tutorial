<?php

App::uses('Checker', 'Lib/Validation');


$regExps = [
    'alphanumeric' => '/\A[a-zA-Z0-9]+\z/',
    'email'        => '/\A[^@]+@[^@]+\z/',
    'display_name' => '/\A[^\p{Z}\p{C}](?:[\x{0020}\x{3000}]*[^\p{Z}\p{C}])*?\z/u',
];

// $dbCommon = [
//     'datetime' => [
//         'message' => '不正な日付のフォーマットです。', 'checker' => Checker::notMatch($regExps['datetime'])
//     ],
//     'created_by' => [
//         'message' => '最大36文字です。', 'checker' => Checker::max(36),
//     ],
//     'updated_datetime' => [
//         'message' => '不正な日付のフォーマットです。', 'checker' => Checker::notMatch($regExps['datetime'])
//     ],
//     'updated_by' => [
//         'message' => '最大36文字です。', 'checker' => Checker::max(36),
//     ],
// ];

// formName, fieldName について: 小文字
return $config = [
    'registerUser' => [
        'display_name' => [
            ['message' => '表示名は必須です。', 'checker' => Checker::notEmpty(), 'exit' => true],
            ['message' => '表示名は最大30文字です。', 'checker' => Checker::max(30)],
            ['message' => '無効な表示名です。前後のスペースを削除してください。', 'checker' => Checker::notMatch($regExps['display_name'])],
        ],
        'email' => [
            ['message' => 'メールアドレスは必須です。', 'checker' => Checker::notEmpty(), 'exit' => true],
            ['message' => 'メールアドレスは最大254文字です。', 'checker' => Checker::max(254)],
            ['message' => '不正なメールアドレスです。', 'checker' => Checker::notMatch($regExps['email'])],
        ],
        'password' => [
            ['message' => 'パスワードは必須です。', 'checker' => Checker::notEmpty(), 'exit' => true],
            ['message' => 'パスワードは8文字以上です。', 'checker' => Checker::min(8)],
            ['message' => 'パスワードは最大72文字です。', 'checker' => Checker::max(72)],
            ['message' => 'パスワードは半角英数字のみです。', 'checker' => Checker::notMatch($regExps['alphanumeric'])],
        ],
        'password_confirmation' => [
            ['message' => 'パスワードと一致しません。', 'checker' => Checker::notEqualTo('password')],
        ]
    ],
    'login' => [
        'email' => [
            ['message' => 'メールアドレスは必須です。', 'checker' => Checker::notEmpty(), 'exit' => true],
            ['message' => '不正なメールアドレスです。', 'checker' => Checker::notMatch($regExps['email'])],
        ],
    ],
    'registerThread' => [
        'thread_title' => [
            ['message' => 'スレッドタイトルは必須です。', 'checker' => Checker::notEmpty(), 'exit' => true],
            ['message' => 'スレッドタイトルは最大100文字です。', 'checker' => Checker::max(100)],
        ],
        'thread_description' => [
            ['message' => 'スレッド説明は最大5000文字です。', 'checker' => Checker::max(5000)],
        ],
        'comment_body' => [
            ['message' => 'コメント内容は最大5000文字です。', 'checker' => Checker::max(5000)],
            ['message' => 'コメント内容は必須です。', 'checker' => Checker::notBlank()],
        ],
    ],
    'registerComment' => [
        'comment_body' => [
            ['message' => 'コメント内容は必須です。', 'checker' => Checker::notEmpty(), 'exit' => true],
            ['message' => 'コメント内容は最大5000文字です。', 'checker' => Checker::max(5000)],
            ['message' => 'コメント内容は必須です。', 'checker' => Checker::notBlank()],
        ],
    ],
];