
salt, chipher seedの生成用コマンド
```gitbash
koichiro@DESKTOP-D150Q3Q MINGW64 /c/xampp/htdocs/cake_tutorial (main)
$ php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
f037ca428113d578908871e91b9c84738a9ba7b02d1c4966ce8a1bc1b62a247d

koichiro@DESKTOP-D150Q3Q MINGW64 /c/xampp/htdocs/cake_tutorial (main)
$ php -r 'for($i=0;$i<30;$i++) echo random_int(0,9); echo PHP_EOL;'
774501271925430360217240952762
```

これらの文字列は、パスワードほどの機密情報でもないが、公開リポジトリにむやみに公開するものでもない。
