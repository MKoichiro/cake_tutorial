<?php

App::uses('Component', 'Controller');
App::uses('CakeLog', 'Log');
App::uses('InternalErrorException', 'Error');
App::uses('PublicError', 'Lib/PublicError');

class AuthorizeComponent extends Component {
    public $components = [
        'Flash' => ['className' => 'CustomizedFlash'],
        'Session',
        'Login',
    ];

    private $controller;

    public const DEFAULT_WHITE_LIST = [
        'public' => [
            'users' => ['register', 'confirm', 'complete'],
            'authentications' => ['login', 'auth'],
            'threads' => ['home', 'show'],
            'comments' => [],
        ],
        'loginUser' => [
            'users' => [],
            'authentications' => ['logout'],
            'threads' => ['register', 'confirm', 'complete'],
            'comments' => ['complete'],
        ],
        // 'owner' => [
        //     'users' => [],
        //     'authentications' => [],
        //     'threads' => ['show', 'edit', 'update', 'delete'],
        //     'comments' => ['edit', 'update'],
        // ],
    ];

    private $defaultViolationRedirect = [
        'loginUser' => '/login',
        'owner' => '/home',
        // 'admin' => '', // 未運用
    ];

    private $defaultViolationMessage = [
        'loginUser' => 'ログインしてください。',
        'owner' => 'アクセス権限がありません。',
        // 'admin' => '', // 未運用
    ];

    public function __construct(ComponentCollection $collection, $settings = []) {
        parent::__construct($collection, $settings);
    }

    /**
     * ライフサイクルの最初でコントローラーを捕捉する。
     */
    public function initialize(Controller $controller) {
        $this->controller = $controller;
    }

    /**
     * 認可チェック。$requester として認可が降りるかを判定する。
     *
     * @param string $requester 'public' | 'loginUser' | 'owner' | 'admin'
     * @param array $payload 認可判定に必要な追加情報（例: ['user_uid' => '...']）
     * @return bool
     */
    public function isAuthorizedAs($requester, $payload = []) {
        switch ($requester) {
            case 'public':
                return true;
            case 'loginUser':
                return $this->Login->isLoggedIn();
            case 'owner':
                if (!$this->Login->isLoggedIn()) {
                    return false;
                }
                $payloadUserUid = null;
                if (array_key_exists('user_uid', $payload)) {
                    $payloadUserUid = $payload['user_uid'];
                } elseif (array_key_exists('uid', $payload)) {
                    $payloadUserUid = $payload['uid'];
                }

                if ($payloadUserUid === null) {
                    return false;
                }

                $loginUserUid = $this->Login->getLoginUserValue('uid');
                return $loginUserUid === $payloadUserUid;
            default:
                return false;
        }
    }

    /**
     * AppController::APP_WHITE_LIST または self::DEFAULT_WHITE_LIST から全体設定のホワイトリストを取得。
     *
     * @param Controller $controller
     * @return array
     */
    private function getAppWhiteList(Controller $controller) {
        $appWhiteList = self::DEFAULT_WHITE_LIST;
        $controllerClass = get_class($controller);
        $whiteListConstant = $controllerClass . '::APP_WHITE_LIST';

        if (defined($whiteListConstant)) {
            $appWhiteList = constant($whiteListConstant);
            if ($appWhiteList === null || !is_array($appWhiteList)) {
                throw new InternalErrorException($whiteListConstant . ' is invalid.');
            }
        }

        return $appWhiteList;
    }

    /**
     * 全体設定のホワイトリストからコントローラー固有のホワイトリストを抽出。
     *
     * @param array $appWhiteList
     * @param string $controllerName
     * @return array
     */
    private function extractControllerWhiteList(array $appWhiteList, $controllerName) {
        $result = [];

        foreach ($appWhiteList as $requester => $actions) {
            if (!array_key_exists($controllerName, $actions)) {
                $message = sprintf(
                    'Controller name "%s" not found in appWhiteList for requester "%s".',
                    $controllerName,
                    $requester
                );
                throw new InternalErrorException($message);
            }

            $result[$requester] = array_values($actions[$controllerName]);
        }

        return $result;
    }

    /**
     * 以下の優先順位でコントローラー固有のホワイトリストを取得。
     * 1. allow() の引数で渡されるコントローラーホワイトリスト
     * 2. AppController::APP_WHITE_LIST から取得されるコントローラーホワイトリスト
     * 3. self::DEFAULT_WHITE_LIST から取得されるコントローラーホワイトリスト
     *
     * @param array|null $controllerWhiteList
     * @param Controller $controller
     * @return array
     */
    private function getControllerWhiteList($controllerWhiteList, Controller $controller) {
        if ($controllerWhiteList !== null) {
            return $controllerWhiteList;
        }

        $controllerName = strtolower($controller->name);
        $appWhiteList = $this->getAppWhiteList($controller);

        return $this->extractControllerWhiteList($appWhiteList, $controllerName);
    }

    /**
     * ホワイトリストに基づく認可チェックと処理。
     *
     * @param array|null $whiteList コントローラー固有のホワイトリスト。null の場合はデフォルト設定を使用。
     * @return void
     */
    public function allow($whiteList = null) {
        $controller = $this->controller;
        $currentAction = $controller->request->action;
        $whiteList = $this->getControllerWhiteList($whiteList, $controller);

        $matched = false;
        foreach ($whiteList as $requester => $actions) {
            if (!in_array($currentAction, $actions, true)) {
                continue;
            }

            $matched = true;

            if ($requester === 'public') {
                return;
            }

            if ($this->isAuthorizedAs($requester)) {
                return;
            }

            CakeLog::write(
                'warning',
                sprintf(
                    'Authorization as requester "%s" was not granted. action=%s payload=%s',
                    $requester,
                    $currentAction,
                    print_r($this->Session->read(), true)
                )
            );

            $this->Flash->error($this->defaultViolationMessage[$requester] ?? 'アクセス権限がありません。');

            $redirect = $this->defaultViolationRedirect[$requester] ?? '/';
            return $controller->redirect($redirect);
        }

        // whiteListで未指定のアクションは現状 許可 扱い。
        if (!$matched) {
            return;
        }
    }
}
