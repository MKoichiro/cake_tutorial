<?=
  $this->element('Debug/debug', [
    'arrays' => [
      '$loginUser' => $loginUser,
    ],
  ]);
?>

<p><?= $loginUser['display_name'] ?>さん、ようこそ掲示板アプリへ！</p>

<p>何からはじめますか？</p>

<ul>
  <li><a href="#">サイトホームへ</a></li>
  <li><a href="#">マイページへ</a></li>
  <li><a href="#">さっそくスレッドを立てる</a></li>
</ul>
