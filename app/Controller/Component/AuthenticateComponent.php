<?php

App::uses('Component', 'Controller');
App::uses('AuthenticateService', 'Service');
App::uses('ArrayUtil', 'Lib/Utility');
App::uses('PublicError', 'Lib/PublicError');

class AuthenticateComponent extends Component {
    public $components = ['Flash', 'Session'];
    private $authenticateService;
    private $controller;

    public function __construct(ComponentCollection $collection, $settings = []) {
        parent::__construct($collection, $settings);
        $this->authenticateService = new AuthenticateService();
    }

    public function initialize(Controller $controller) {
        parent::initialize($controller);
        $this->controller = $controller;
    }

    /**
     * ログイン処理
     * 
     * @param array{ 'email' => string, 'password' => string } $credentials
     * @return bool 処理の成否
     */
    public function login($credentials) {
        // 既にログイン済み
        if ($this->isLoggedIn()) {
            return true;
        }

        // 認証試行
        if (!($user = $this->authenticateService->authenticate($credentials))) {
            // 失敗
            $this->Flash->error('メールアドレスまたはパスワードが正しくありません。');
            // return $this->controller->redirect(['[method]' => 'GET', 'action' => 'displayForm']);
            return $this->controller->redirect('/login');
            // return false;
        } else {
            // 成功: セッションを開始
            $this->Session->renew();
            $this->Session->write('Auth.User', $user);
            return true;
        }
    }

    /**
     * ログアウト
     * @return bool
     */
    public function logout() {
        $this->Session->destroy();
        return true;
    }

    /**
     * ログイン済みかどうか判定
     * 
     * @return bool
     */
    public function isLoggedIn() {
        return !is_null($this->loginUser());
    }

    /**
     * ログインユーザー情報取得
     * 
     * @return array|null ログインユーザー情報、未ログイン時は null
     */
    public function loginUser() {
        return $this->Session->read('Auth.User');
    }

    /**
     * ログインユーザー情報の特定属性取得
     * 
     * @param mixed ...$attrs 取得したい属性名の可変引数リスト
     * @return array|null 指定された属性のみを含む連想配列、未ログイン時は null
     */
    public function getLoginUserValues(...$attrs) {
        $user = $this->loginUser();
        CakeLog::write('debug', 'loginUser: ' . print_r($user, true));
        CakeLog::write('debug', 'getLoginUserValues called with attrs: ' . print_r($attrs, true));

        if (is_null($user)) {
            return null;
        } elseif (empty($attrs)) {
            return $user;
        }

        return ArrayUtil::extract($user, $attrs);
    }

    /**
     * ログインユーザー情報の特定属性取得（単一属性版）
     * 
     * @param string $attr 取得したい属性名
     * @return mixed|null 指定された属性の値、未ログイン時は null
     */
    public function getLoginUserValue($attr) {
        return $this->getLoginUserValues($attr)[$attr];
    }
}
