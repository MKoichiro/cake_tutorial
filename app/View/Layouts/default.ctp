<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development framework');
$cakeVersion = __d('cake_dev', 'CakePHP %s', Configure::version())
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>掲示板アプリ</title>
    <link href="<?= $rootPath; ?>/favicon.ico" type="image/x-icon" rel="icon">
    <link href="<?= $rootPath; ?>/favicon.ico" type="image/x-icon" rel="shortcut icon">
    <link rel="stylesheet" type="text/css" href="<?= $rootPath; ?>/css/custom.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <script type="module" src="<?= $rootPath ?>/js/main.js"></script>
    <?= $this->Html->script(str_replace('text/javascript', 'module', $scripts_for_layout)); ?>
</head>
<body>
	<div id="container">
        <?= $this->element('header'); ?>

		<main>
			<?= $this->Flash->render() ?>
			<?= $this->fetch('content') ?>
		</main>

		<footer>
        </footer>
	</div>
    <?php if (Configure::read('debug') > 0): ?>
    <?php endif; ?>
</body>
</html>