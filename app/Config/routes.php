<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
 
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
	// Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
	// Router::connect('/', ['controller' => 'posts', 'action' => 'index']);

// 以下記
// UUIDの正規表現(例)
// 例: d5e72908-2862-492f-96db-1c36be17556a
$uuidRegExp = '[0-9a-z]{8}-[0-9a-z]{4}-[0-9a-z]{4}-[0-9a-z]{4}-[0-9a-z]{12}';

// UsersController
Router::connect(
	'/users/register',
	['[method]' => 'GET', 'controller' => 'users', 'action' => 'register']
);
Router::connect(
	'/users/confirm',
	['[method]' => 'POST', 'controller' => 'users', 'action' => 'confirm']
);
Router::connect(
	'/users/complete',
	['[method]' => 'POST', 'controller' => 'users', 'action' => 'complete']
);
Router::connect(
	'/users/:user_uid',
	['[method]' => 'GET', 'controller' => 'users', 'action' => 'show'],
	['pass' => ['user_uid'], 'user_uid' => $uuidRegExp]
);

// AuthenticationsController
Router::connect(
	'/login',
	['[method]' => 'GET', 'controller' => 'authentications', 'action' => 'login']
);
Router::connect(
	'/login',
	['[method]' => 'POST', 'controller' => 'authentications', 'action' => 'auth']
);
Router::connect(
	'/logout',
	['[method]' => 'DELETE', 'controller' => 'authentications', 'action' => 'logout']
);

// ThreadsController
Router::connect(
	'/home',
	['[method]' => 'GET', 'controller' => 'threads', 'action' => 'home']
);
Router::connect(
	'/threads/register',
	['[method]' => 'GET', 'controller' => 'threads', 'action' => 'register']
);
Router::connect(
	'/threads/confirm',
	['[method]' => 'POST', 'controller' => 'threads', 'action' => 'confirm']
);
Router::connect(
	'/threads/complete',
	['[method]' => 'POST', 'controller' => 'threads', 'action' => 'complete']
);
Router::connect(
	'/threads/:thread_uid',
	['[method]' => 'GET', 'controller' => 'threads', 'action' => 'show'],
	['pass' => ['thread_uid'], 'thread_uid' => $uuidRegExp]
);
Router::connect(
	'/threads/:thread_uid/edit',
	['[method]' => 'GET', 'controller' => 'threads', 'action' => 'edit'],
	['pass' => ['thread_uid'], 'thread_uid' => $uuidRegExp]
);
Router::connect(
	'/threads/:thread_uid/update',
	['[method]' => 'PUT', 'controller' => 'threads', 'action' => 'update'],
	['pass' => ['thread_uid'], 'thread_uid' => $uuidRegExp]
);
Router::connect(
	'/threads/:thread_uid/delete',
	['[method]' => 'DELETE', 'controller' => 'threads', 'action' => 'delete'],
	['pass' => ['thread_uid'], 'thread_uid' => $uuidRegExp]
);

// CommentsController
Router::connect(
	'/threads/:thread_uid/comments/complete',
	['[method]' => 'POST', 'controller' => 'comments', 'action' => 'complete'],
	['pass' => ['thread_uid'], 'thread_uid' => $uuidRegExp]
);
Router::connect(
	'/threads/:thread_uid/comments/:comment_uid/edit',
	['[method]' => 'GET', 'controller' => 'comments', 'action' => 'edit'],
	[
		'pass' => ['thread_uid', 'comment_uid'],
		'thread_uid' => $uuidRegExp,
		'comment_uid' => $uuidRegExp
	]
);
Router::connect(
	'/threads/:thread_uid/comments/:comment_uid/update',
	['[method]' => 'PUT', 'controller' => 'comments', 'action' => 'update'],
	[
		'pass' => ['thread_uid', 'comment_uid'],
		'thread_uid' => $uuidRegExp,
		'comment_uid' => $uuidRegExp
	]
);

/**
 * ...and connect the rest of 'Pages' controller's URLs.
 */
	// Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	// CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
	// require CAKE . 'Config' . DS . 'routes.php';
