<?php

class ArrayUtil {
    /**
     * 配列から特定キーの値を抽出
     * 
     * @param array $array 対象配列
     * @param array $keys  抽出キー
     * @return array 抽出結果配列
     */
    public static function extract($array, $keys) {
        $result = [];
        foreach ($keys as $key) {
            if (array_key_exists($key, $array)) {
                $result[$key] = $array[$key];
            }
        }
        return $result;
    }

    // TODO: 機密情報に関しては、参照渡しの方が安全（？）
    /**
     * 配列から特定キーを削除して返す
     * 
     * @param array $arrayList 対象配列
     * @param array $keys      削除キー
     * @return array 抽出結果配列
     */
    public static function remove($array, $keys) {
        foreach ($keys as $key) {
            if (array_key_exists($key, $array)) {
                unset($array[$key]);
            }
        }
        return $array;
    }
}
