<pre>
  <?php isset($loginUser) && var_dump($loginUser); ?>
  <?php var_dump($_SESSION); ?>
</pre>
<h1><?= $loginUser['display_name'] ?>さん、ようこそ掲示板アプリへ！</h1>

<h2>何からはじめますか？</h2>

<ul>
  <li><a href="#">サイトホームへ</a></li>
  <li><a href="#">マイページへ</a></li>
  <li><a href="#">さっそくスレッドを立てる</a></li>
</ul>
