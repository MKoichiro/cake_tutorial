<header>
  <h1>掲示板</h1>
  <nav>
    <ul>
      <li><a href="/cake_tutorial/">ホーム</a></li>
      <li><a href="/cake_tutorial/user/:id">マイページ</a></li>
      <li>
        <form id="logout" action="/cake_tutorial/logout" method="post">
          <?= $this->element('Form/methodImplier', ['method' => 'DELETE']); ?>
        </form>
        <button type="submit" form="logout">ログアウト</button>
      </li>
    </ul>
  </nav>
</header>
