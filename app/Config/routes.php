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

// UUIDの正規表現（ハイフン区切り、大小許容）
$UUIDRegExp = '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}';

Router::connect(
	'/registrations',
	['[method]' => 'GET', 'controller' => 'registrations', 'action' => 'displayForm']
);
Router::connect(
	'/registrations/confirm',
	['[method]' => 'POST', 'controller' => 'registrations', 'action' => 'displayConfirm']
);
Router::connect(
	'/registrations/complete',
	['[method]' => 'POST', 'controller' => 'registrations', 'action' => 'displayComplete']
);

/**
 * Users（RESTful）
 */
// // コレクション
// Router::connect('/users',
//   array('[method]' => 'GET',  'controller' => 'users', 'action' => 'index')
// );
// Router::connect('/users/register',
//   array('[method]' => 'GET', 'controller' => 'users', 'action' => 'displayRegister')
// );
// Router::connect('/users',
//   array('[method]' => 'POST', 'controller' => 'users', 'action' => 'register')
// );

// // メンバー（uid）
// Router::connect('/users/:uid',
//   array('[method]' => 'GET',    'controller' => 'users', 'action' => 'view'),
//   array('pass' => array('uid'), 'uid' => $UUIDRegExp)
// );
// Router::connect('/users/:uid',
//   array('[method]' => 'PUT',    'controller' => 'users', 'action' => 'edit'),
//   array('pass' => array('uid'), 'uid' => $UUIDRegExp)
// );
// Router::connect('/users/:uid',
//   array('[method]' => 'PATCH',  'controller' => 'users', 'action' => 'edit'),
//   array('pass' => array('uid'), 'uid' => $UUIDRegExp)
// );
// Router::connect('/users/:uid',
//   array('[method]' => 'DELETE', 'controller' => 'users', 'action' => 'delete'),
//   array('pass' => array('uid'), 'uid' => $UUIDRegExp)
// );

// （任意）トップをどこかに割り当てる場合
// Router::connect('/', array('controller' => 'posts', 'action' => 'index'));

// 規定ルートは読み込まない
// require CAKE . 'Config' . DS . 'routes.php';

/**
 * ...and connect the rest of 'Pages' controller's URLs.
 */
	// Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
	// require CAKE . 'Config' . DS . 'routes.php';
