<?php

App::uses('Component', 'Controller');
App::uses('ArrayUtil', 'Lib/Utility');
App::uses('CakeLog', 'Log');
App::uses('PublicError', 'Lib/PublicError');
App::uses('UserService', 'Service');

class LoginComponent extends Component {
    public $components = [
        'Flash' => ['className' => 'CustomizedFlash'],
        'Session',
    ];

    private $controller;

    public function __construct(ComponentCollection $collection, $settings = []) {
        parent::__construct($collection, $settings);
    }

    public function initialize(Controller $controller) {
        parent::initialize($controller);
        $this->controller = $controller;
    }

    /**
     * 認証済みユーザーの uid を受け取りログインする。
     *
     * @param string $authenticatedUserUid 認証済みユーザーの uid
     * @return void
     */
    public function login($authenticatedUserUid) {
        $this->Session->renew();
        $this->Session->write(['Auth.User' => $authenticatedUserUid]);

        CakeLog::write(
            'info',
            sprintf('[%s] User (UID: %s) logged in successfully.', __CLASS__, $authenticatedUserUid)
        );
    }

    /**
     * ログアウト
     *
     * @param string $redirectTo
     * @param string $flashMessage
     * @return bool|void
     */
    public function logout($redirectTo = '/home', $flashMessage = 'ログアウトしました。') {
        $this->Session->destroy();
        $this->Flash->info($flashMessage);

        return $this->controller->redirect($redirectTo);
    }

    /**
     * ログイン済みかどうか判定
     *
     * @return bool
     */
    public function isLoggedIn() {
        return $this->getLoginUserUid() !== null;
    }

    /**
     * ログインユーザーの uid をセッションから取得
     *
     * @return string|null ログインユーザー uid、未ログイン時は null
     */
    public function getLoginUserUid() {
        return $this->Session->read('Auth.User');
    }

    /**
     * ログインユーザーの情報を外部依存の任意の情報を取得
     *
     * @param mixed ...$keys
     * @return array|null
     */
    public function getLoginUserValues(...$keys) {
        $userUid = $this->getLoginUserUid();
        if ($userUid === null) {
            return null;
        }

        $userService = new UserService();
        $userData = $userService->getUserValuesByUid($userUid, ...$keys);

        return $userData;
    }

    /**
     * ログインユーザーの情報を外部依存の任意の情報を取得（単一属性）
     *
     * @param string $key
     * @return mixed|null
     */
    public function getLoginUserValue($key) {
        $loginUserValues = $this->getLoginUserValues($key);
        if ($loginUserValues === null) {
            return null;
        }

        return $loginUserValues[$key];
    }
}
