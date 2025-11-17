<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc.
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');
App::uses('CakeLog', 'Log');
App::uses('UserService', 'Service');

/**
 * Application Controller
 *
 * @package     app.Controller
 * @link        https://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
    public $components = [
        'Flash' => ['className' => 'CustomizedFlash'],
        'Session',
        'Login',
        'Authorize',
    ];

    // private function setCsrfToken() {
    //     if (!$this->Session->check('csrfToken')) {
    //         $token = bin2hex(random_bytes(32));
    //         $this->Session->write('csrfToken', $token);
    //     }
    //     $this->set('csrfToken', $token);
    // }

    protected function renderError($statusCode) {
        $this->response->statusCode($statusCode);               // ステータスを明示
        return $this->render('/Errors/error' . $statusCode);    // error400.ctp をレンダ
    }

    // 各アクション直前の処理
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Authorize->allow();
    }

    // ビューレンダリング直前の処理
    public function beforeRender() {
        parent::beforeRender();

        // セッションからログインユーザーの uid を取得
        $loginUserUid = $this->Login->getLoginUserUid();
        CakeLog::write(
            'debug',
            __CLASS__ . '#' . __FUNCTION__ . " -- \n"
            . "log-in user uid from session: " . print_r($loginUserUid, true)
        );
        $loginUserInfo = [];
        if ($loginUserUid !== null) {
            // uid で検索して display_name とともに取得
            $userService = new UserService();
            $result = $userService->getUserValuesByUid($loginUserUid, 'user_uid', 'display_name');
            if ($result !== []) {
                $loginUserInfo = $result;
            } else {
                // ログアウトの共通処理を呼び出す
                $this->Login->logout('/home');
            }
        }

        $this->set([
            'loginUserInfo' => $loginUserInfo,  // ログインユーザー情報
            'rootPath'      => ROOT_URL,        // プロジェクトルートをビューに配布
        ]);

        CakeLog::write(
            'debug',
            __CLASS__ . '#' . __FUNCTION__ . " -- \n"
            . "log-in user info passed to view: \n" . print_r($loginUserInfo, true)
        );
    }
}
