<?php

class Validator {

    /** @var array フォーム全体を通して発生したバリデーションエラーに該当するエラーメッセージを格納した連想配列 */
    private $errorMessages;

    /**
     * @var array $config バリデーション設定読み込みの連想配列。以下のような構成
     * @example [
     * ... 'fieldName1' => [
     * ... ... 'message' => 'エラーメッセージ',
     * ... ... 'checker' => 'checkerFunctionName', // チェック関数
     * ... ... 'exit' => true/false, // オプショナルでデフォルトはfalse。true だと以降のそのフィールドでのバリデーションをスキップする
     * ... ],
     * ... 'fieldName2' => [
     * ... ... 'message' => '...',
     * ... ... 'checker' => '...',
     * ... ... 'exit' => ...
     * ... ],
     * ... ...
     * ]
     */
    private $configs;

    /**
     * @var array[
     * ... 'strict': 生データ 'rawData' のキーが設定 'configs' のキーと過不足がある場合、例外をスロー
     * ... 'allowRawDataLack': 生データのキーが設定のキーに対して不足することを許容
     * ... 'allowConfigsLack': 生データのキーが設定のキーに対して超過することを許容
     * ]
     */
    private const EXECUTE_MODES = [
        'strict',
        'allowRawDataLack',
    ];


    /**
     * プライベートメンバーを初期化するコンストラクタ
     * $configs は設定値を設定ファイルから読み込み
     */
    public function __construct() {
        $this->errorMessages = [];
        $this->configs = require(APP . 'Lib' . DS . 'Validation' . DS . 'configs.php');
    }


    /**
     * $errorMessages に1つフィールドで発生したエラーメッセージを追記するセッター
     * @param string $fieldName ... エラーメッセージを追加するフィールドの名前
     * @param string $fieldErrorMessages ... エラーメッセージ
     */
    private function setFieldErrorMessage($fieldName, $fieldErrorMessages) {
        $this->errorMessages[$fieldName] = $fieldErrorMessages;
    }


    /**
     * $errorMessages 配列に、フォーム 'formName' 形式でフィールドだけの指定を可能にするために
     * フィールド 'fieldName' のバリデーション設定を
     * @param string $formName フォームまたはフィールドの識別子 ('formName' または 'formName.fieldName')
     * @param string $configKey
     * @return array
     */
    private function extractConfig($configKey) {
        if (!strpos($configKey, '.')) {
            return $this->configs[$configKey];
        }
        list($formName, $fieldName) = explode('.', $configKey, 2);

        if (!isset($this->configs[$formName][$fieldName])) {
            CakeLog::write(
                'error',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'The specified configKey `'.$configKey.'` does not exist in configs.'
            );
            throw new InvalidArgumentException();
        }
        return [
            $fieldName => $this->configs[$formName][$fieldName]
        ];
    }


    /**
     * バリデーション処理の本体
     *
     * @param array $rawData ユーザーの入力データ
     * @param string $configKey フォームまたはフィールドの識別子 ('formName' または 'formName.fieldName')
     * @param string $mode = 'strict'
     * @return void
     */
    private function setErrorMessages($rawData, $configKey, $mode) {
        $configs = $this->extractConfig($configKey);
        CakeLog::write(
            'debug',
            'Validation for `'.$configKey.'` start...'."\n".
            'Read the following configs:'."\n".
            print_r($configs, true)
        );

        // count(設定) の不足
        if (count($rawData) < count($configs)) {
            $diff = array_keys(array_diff_key($configs, $rawData));
            CakeLog::write(
                'debug',
                'Validation: The following key does not exist in configs:'."\n".
                print_r($diff, true)
            );
            throw new InvalidArgumentException();
        }

        // count(生データ) の不足
        if (count($rawData) > count($configs)) {
            $diff = array_keys(array_diff_key($rawData, $configs));
            CakeLog::write(
                'debug',
                'Validation: The following key does not exist in rawData:'."\n".
                print_r($diff, true)
            );
            if ($mode === 'strict') {
                throw new InvalidArgumentException();
            }
        }

        // $configs のキーでループ 
        foreach ($configs as $fieldName => $fieldConfigs) {
            $fieldErrorMessages = [];
            foreach ($fieldConfigs as $fieldConfigs) {
                $checkerFunction = $fieldConfigs['checker'] ?: null;
                if (!is_callable($checkerFunction)) {
                    CakeLog::write(
                        'error',
                        '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                        'The checker function is not callable for `'.$fieldName.'`.'
                    );
                    throw new InvalidArgumentException();
                }
                if ($checkerFunction($rawData, $fieldName, $mode)) {
                    $fieldErrorMessages[] = $fieldConfigs['message'];
                    // exit フラグが立っていたら以降のバリデーションをスキップ
                    if (isset($fieldConfigs['exit']) && $fieldConfigs['exit']) {
                        break;
                    }
                }
            }
            CakeLog::write('debug', 'Validation: The following errors were detected for `'.$fieldName.'`.'."\n".print_r($fieldErrorMessages, true));
            if (count($fieldErrorMessages) > 0) {
                $this->setFieldErrorMessage($fieldName, $fieldErrorMessages);
            }
        }
    }


    /**
     * errorMessages のゲッター
     * アクションから呼び出し、(execute 実行後に) set でビューに渡す想定
     *
     * @return array フォーム全体を通して発生したエラーメッセージの配列
     */
    public function getErrorMessages() {
        return $this->errorMessages;
    }


    /**
     * バリデーション実行および成否判定
     * アクションから呼び出し、判定する想定
     *
     * @param array $rawData フォームのキーと値。例: フォーム名、
     * @param string $configKey フォームまたはフィールドの識別子 ('formName' または 'formName.fieldName')
     * @param string $mode = 'strict'
     * 'allowRawDataLack': 生データ 'rawData' のキーが設定 'configs' のキーに対して不足することを許容
     * 'allowConfigsLack': 生データのキーが設定のキーに対して超過することを許容
     * @return bool 成功なら true、失敗なら false
     */
    public function execute($rawData, $configKey, $mode = 'strict') {
        if (!in_array($mode, self::EXECUTE_MODES)) {
            CakeLog::write(
                'error',
                '...' . __CLASS__ . '#' . __FUNCTION__ . '...' . "\n" .
                'Unknown mode `'.$mode.'` is passed.'
            );
            throw new InvalidArgumentException();
        }
        $this->setErrorMessages($rawData, $configKey, $mode);
        $isValid = $this->getErrorMessages() === [];
        return $isValid;
    }
}
