<?php

App::uses('Component', 'Controller');
App::uses('PublicError', 'Lib/PublicError');

class AuthorizeComponent extends Component {
    public $components = ['Session', 'Flash', 'Authenticate'];
    private $controller;
    public const DEFAULT_WHITE_LIST = [
        'public' => [
            'users'           => ['register', 'confirm', 'complete'],
            'authentications' => ['login', 'logout'],
            'threads'         => ['home'],
            'comments'        => [],
        ],
        'loginUser' => [
            'users'           => [],
            'authentications' => [],
            'threads'         => ['createThread', 'create'],
            'comments'        => ['create'],
        ],
    ];

    private $defaultViolationRedirect = [
        'loginUser' => '/login',
        'owner'     => '/home',
        // 'admin'     => '', // 未運用
    ];

    public function __construct(ComponentCollection $collection, $settings = []) {
        parent::__construct($collection, $settings);
    }

    /**
     * ライフサイクルの最初でコントローラーを捕捉
     */
    public function initialize(Controller $controller) {
        $this->controller = $controller;
    }


    /**
     * 認可チェック。$requester として認可が降りるかを判定する。
     * @param string $requester 'public' | 'loginUser' | 'owner' | 'admin'
     * @param array $payload 認可判定に必要な追加情報
     */
    public function isAuthorizedAs($requester, $payload = []) {
        switch ($requester) {
            case 'public':
                return true;
            case 'loginUser':
                return $this->Authenticate->isLoggedIn();
            case 'owner':
                $loginUserUid = $this->Authenticate->getLoginUserValue('uid');
                $requesterUid = $payload['user']['uid'];
                return $loginUserUid === $requesterUid;
            // case 'admin': // 未運用
            default:
                return false;
        }
    }

    /**
     * AppController::APP_WHITE_LIST または self::APP_WHITE_LIST から全体設定のホワイトリストを取得
     * 
     * @param Controller $controller
     * @return array
     */
    private function getAppWhiteList($controller) {
        // AppController::$appWhiteList > self::$appWhiteList の順位でホワイトリストを取得
        $appWhiteList = self::DEFAULT_WHITE_LIST;

        if (defined(get_class($controller) . '::APP_WHITE_LIST')) {
            $appWhiteList = constant(get_class($controller) . '::APP_WHITE_LIST');
            if (is_null($appWhiteList) || !is_array($appWhiteList)) {
                throw new Exception('AppController::$appWhiteList is invalid.');
            }
        }

        return $appWhiteList;
    }

    /**
     * 全体設定のホワイトリストからコントローラー固有のホワイトリストを抽出
     * 
     * @param array $appWhiteList
     * @param string $controllerName
     * @return array
     */
    private function extractControllerWhiteList($appWhiteList, $controllerName) {
        // 現在のコントローラー分のホワイトリスト抽出
        $result = [];
        foreach ($appWhiteList as $requester => $actions) {
            // TODO: 要確認, 例外を投げるでいいか？、!isset(...) ではなく array_key_exists() の方が良いか？
            if (!isset($actions[$controllerName])) {
                // CakeErrorControllerなど、想定外の組み込みコントローラーの場合は false で抜ける
                throw new Exception('Controller name not found in appWhiteList: ' . $controllerName);
            }
            $result[$requester] = array_values($actions[$controllerName]);
        }
        return $result;
    }

    /**
     * 以下の優先順位でコントローラー固有のホワイトリストを取得
     * 1. allow() の引数で渡されるコントローラーホワイトリスト
     * 2. AppController::APP_WHITE_LIST から取得されるコントローラーホワイトリスト
     * 3. self::APP_WHITE_LIST から取得されるコントローラーホワイトリスト
     * 
     * @param array|null $controllerWhiteList
     * @param Controller $controller
     * @return array
     */
    private function getControllerWhiteList($controllerWhiteList, $controller) {
        $controllerName = strtolower($controller->name);
        if (!is_null($controllerWhiteList)) {
            // コントローラーから、コントローラー固有のホワイトリストが渡されていればそれを優先
            return $controllerWhiteList;
        } else {
            // 全体設定のホワイトリストを取得
            $appWhiteList = $this->getAppWhiteList($controller);
            // 現在のコントローラー分のホワイトリスト抽出して返却
            return $this->extractControllerWhiteList($appWhiteList, $controllerName);
        }
    }

    /**
     * ホワイトリストに基づく認可チェックと処理
     * 
     * @param array|null $whiteList コントローラー固有のホワイトリスト。null の場合は AppController::APP_WHITE_LIST または self::APP_WHITE_LIST を使用。
     * @return void
     */
    public function allow($whiteList = null) {
        $controller = $this->controller;
        $currentAction = $controller->request->action;
        $whiteList = $this->getControllerWhiteList($whiteList, $controller);

        $matched = false;
        foreach ($whiteList as $requester => $actions) {
            if (in_array($currentAction, $actions, true)) {
                $matched = true;
                // public のアクションに設定されていれば早期許可
                if ($requester === 'public') {
                    return;
                }
                // 認可不通過
                if (!$this->isAuthorizedAs($requester)) {
                    $this->Flash->error('アクセス権限がありません。');
                    return $controller->redirect($this->defaultViolationRedirect[$requester]);
                }
                // 認可通過
                return;
            }
        }

        // whiteListで未指定のアクションは現状 許可 扱いとする
        if (!$matched) {
            return;
        }
    }
}
