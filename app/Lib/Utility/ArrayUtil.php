<?php

class ArrayUtil {

    /**
     * 配列から特定キーの値を抽出
     *
     * @param array $array ... 対象配列
     * @param string ... $keys ... 抽出キー
     * @return array 抽出結果配列
     */
    public static function extract($array, ...$keys) {
        // キーの指定が無ければ $array をそのまま返す
        if ($keys === []) {
            return $array;
        }

        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $array[$key];
        }

        return $result;
    }


    /**
     * 配列から特定キーを削除して返す(未使用)
     *
     * @param array $arrayList 対象配列
     * @param array $keys 削除キー
     * @return array 抽出結果配列
     */
    // public static function remove(array $array, $keys) {
    // ... foreach ($keys as $key) {
    // ... ... unset($array[$key]);
    // ... }
    // ...
    // ... return $array;
    // }
}