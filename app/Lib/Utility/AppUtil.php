<?php

App::uses('ArrayUtil', 'Lib/Utility');


class AppUtil {

    /**
     * @const array SECRET_KEYS ユーザー認証において機密情報として扱うカラム名の配列
     */
    private const SECRET_KEYS = ['password_hash', 'user_id'];


    /**
     * ユーザー配列を受け取って機密情報を削除して返す関数
     *
     * @param array $user
     */
    public static function secretsRemover($user) {
        foreach (self::SECRET_KEYS as $key) {
            unset($user[$key]);
        }
        return $user;
    }


    /**
     * 'datetime' カラムの値を DateTime インスタンス変換する
     *
     * @param array $array select 文の sql の結果セット
     * @return array 変換後の配列
     */
    // public static function instantiateData($array) {
    //     foreach ($array as $record) {
    //         if (array_key_exists('created_datetime', $entity)) {
    //             $entity['created_datetime'] = new DateTime($entity['created_datetime']);
    //         }
    //         if (array_key_exists('updated_datetime', $entity)) {
    //             $entity['updated_datetime'] = new DateTime($entity['updated_datetime']);
    //         }
    //         // ...
    //         unset($entity);
    //     }

    //     return $array;
    // }
}