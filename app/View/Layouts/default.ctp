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
	<?= $this->Html->charset() ?>
	<title>
		<?= $cakeDescription ?>
	</title>
    <link href="<?= $rootPath; ?>/favicon.ico" type="image/x-icon" rel="icon">
    <link href="<?= $rootPath; ?>/favicon.ico" type="image/x-icon" rel="shortcut icon">
    <link rel="stylesheet" type="text/css" href="<?= $rootPath; ?>/css/custom.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <script type="text/javascript" src="<?= $rootPath ?>/js/main.js"></script>
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
            <?php if (Configure::read('debug') > 0): ?>
                <p>
                    <?php
                        echo $this->Html->link(
                            $this->Html->image('cake.power.gif', ['alt' => $cakeDescription, 'border' => '0']),
                            'https://cakephp.org/',
                            ['target' => '_blank', 'escape' => false, 'id' => 'cake-powered']
                        );
                    ?>
                </p>
                <p>
                    <?= $cakeVersion; ?>
                </p>
            <?php endif; ?>
        </footer>
	</div>
    <?php if (Configure::read('debug') > 0): ?>
	    <?= $this->element('sql_dump') ?>
    <?php endif; ?>
</body>
</html>