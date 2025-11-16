<?php

App::uses('StringUtil', 'Lib/Utility');


/**
 * 入力データとフィールド名を受け取り、バリデーションの判定結果を真偽値で返す関数群
 */
class Checker {

    /**
     * 各チェッカーが返す真偽値で分岐制御するガード関数
     *
     * @param array $rawData ... 対象の生データ
     * @param string $fieldName ... 当該のフィールド名
     * @param string $mode ... 'strict': $rawData にキー $fieldName が存在しない場合、false。バリデーションスキップ対象
     * @throws InvalidArgumentException
     */
    private static function argsGuard($rawData, $fieldName, $mode, $functionName) {
        CakeLog::write('debug', 'Validation: '.$functionName.' called.');
        CakeLog::write('debug', 'Validation: $rawData:'."\n". print_r($rawData, true));
        if (!is_array($rawData)) {
            CakeLog::write('warning', 'Invalid Argument: $rawData should be type of array.');
            throw new InvalidArgumentException();
        }
        if (!is_string($fieldName)) {
            CakeLog::write('warning', 'Invalid Argument: $fieldName should be type of string.');
            throw new InvalidArgumentException();
        }

        // 'strict' (存在性)
        if (!array_key_exists($fieldName, $rawData)) {
            if ($mode === 'strict') {
                CakeLog::write('warning', 'Invalid Argument: $rawData[$fieldName] is undefined.');
                throw new InvalidArgumentException();
            } else if ($mode === 'allowRawDataLack') {
                CakeLog::write('notice', 'skip this validation');
                return false;
            }
        }
        // キーが存在するのに中身が null
        if ($rawData[$fieldName] === null) {
            CakeLog::write('warning', 'Invalid Argument: $rawData[$fieldName] is null.');
            throw new InvalidArgumentException();
        }
        return true;
    }


    /**
     * @param int $length 取りうる最小文字列長
     * @return callable 違反時に true を返すバリデーションチェッカー
     */
    public static function min($length) {
        $functionName = __FUNCTION__;
        return function ($rawData, $fieldName, $mode) use ($functionName, $length) {
            if (self::argsGuard($rawData, $fieldName, $mode, $functionName)) {
                $target = $rawData[$fieldName];
                if (!is_string($target)) {
                    CakeLog::write('error', 'Validation('.$functionName.'): target value is not string');
                    throw new InvalidArgumentException();
                }
                if (!is_numeric($length)) {
                    CakeLog::write('error', 'Validation('.$functionName.'): $length is not numeric');
                    throw new InvalidArgumentException();
                }
                return mb_strlen($target) < (int) $length;
            }
            return false;
        };
    }


    /**
     * @param int $length 取りうる最大文字列長
     * @return callable 違反時に true を返すバリデーションチェッカー
     */
    public static function max($length) {
        $functionName = __FUNCTION__;
        return function ($rawData, $fieldName, $mode) use ($functionName, $length) {
            if (self::argsGuard($rawData, $fieldName, $mode, $functionName)) {
                $target = $rawData[$fieldName];
                if (!is_string($target)) {
                    CakeLog::write('error', 'Validation('.$functionName.'): target value is not string');
                    throw new InvalidArgumentException();
                }
                if (!is_numeric($length)) {
                    CakeLog::write('error', 'Validation('.$functionName.'): $length is not numeric');
                    throw new InvalidArgumentException();
                }
                return mb_strlen($target) > (int) $length;
            }
            return false;
        };
    }


    /**
     * 正規表現検証
     * @param string $regExp 正規表現
     * @return callable 違反時に true を返すバリデーションチェッカー
     */
    public static function notMatch($regExp) {
        $functionName = __FUNCTION__;
        return function ($rawData, $fieldName, $mode) use ($functionName, $regExp) {
            if (self::argsGuard($rawData, $fieldName, $mode, $functionName)) {
                $target = $rawData[$fieldName];
                if (!is_string($target)) {
                    CakeLog::write('error', 'Validation('.$functionName.'): target value is not string');
                    throw new InvalidArgumentException();
                }
                if (!is_string($regExp)) {
                    CakeLog::write('error', 'Validation('.$functionName.'): $regExp is not string');
                    throw new InvalidArgumentException();
                }
                return preg_match($regExp, $target) === 0;
            }
            return false;
        };
    }


    /**
     * 他フィールドとの一致判定
     * @param string $anotherFieldName 比較したいフィールドの識別子
     * @return callable 違反時に true を返すバリデーションチェッカー
     */
    public static function notEqualTo($anotherFieldName) {
        $functionName = __FUNCTION__;
        return function ($rawData, $fieldName, $mode) use ($functionName, $anotherFieldName) {
            if (self::argsGuard($rawData, $fieldName, $mode, $functionName)) {
                if (!is_string($anotherFieldName)) {
                    CakeLog::write('error', 'Validation('.$functionName.'): $anotherFieldName is not string');
                    throw new InvalidArgumentException();
                }
                return $rawData[$fieldName] === $rawData[$anotherFieldName];
            }
            return false;
        };
    }


    /**
     * スペースや改行文字のみをNGとする
     * @return callable 違反時に true を返すバリデーションチェッカー
     */
    public static function notBlank() {
        $functionName = __FUNCTION__;
        return function ($rawData, $fieldName, $mode) use ($functionName) {
            if (self::argsGuard($rawData, $fieldName, $mode, $functionName)) {
                $target = $rawData[$fieldName];
                if ($target === '') {
                    return false;
                }
                if (!is_string($target)) {
                    CakeLog::write('error', 'Validation('.$functionName.'): target value is not string');
                    throw new InvalidArgumentException();
                }

                return StringUtil::mbTrim($target) === '';
            }
            return false;
        };
    }


    /**
     * 空文字を許容する
     * @return callable 違反時に true を返すバリデーションチェッカー
     */
    public static function notEmpty() {
        $functionName = __FUNCTION__;
        return function ($rawData, $fieldName, $mode) use ($functionName) {
            if (self::argsGuard($rawData, $fieldName, $mode, $functionName)) {
                $target = $rawData[$fieldName];
                if (!is_string($target)) {
                    CakeLog::write('error', 'Validation('.$functionName.'): target value is not string');
                    throw new InvalidArgumentException();
                }
                return $target === '';
            }
            return false;
        };
    }
}