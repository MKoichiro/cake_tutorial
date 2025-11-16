<?php

class StringUtil {

    public static function generateUuid() {
        return CakeText::uuid();
    }


    /**
     * 文字列前後の制御文字 (空白(全角/半角スペース・改行など)) を削除
     * \s: 以下の空白文字(半角スペース、\t、\n、\r、\f、\v)
     * \p{Z}: Unicode の区切り文字 (空白・改行・全角スペースなど)
     * \p{C}: 制御文字
     * \u: マッチング UTF-8 文字
     * @param string $str
     *
     * @see https://www.php.net/manual/ja/regexp.reference.unicode.php#120703
     */
    public static function mbTrim($str) {
        if (!is_string($str)) {
            throw new InvalidArgumentException();
        }

        $regExp = '/\A[\p{Z}\p{C}]+|[\p{Z}\p{C}]+\z/u';
        return preg_replace($regExp, '', $str);
    }

    public static function tabToNbsp($str, $tabWidth = 4) {
        if (!is_string($str) || !is_int($tabWidth) || $tabWidth < 1) {
            throw new InvalidArgumentException();
        }

        return preg_replace('/\t/', str_repeat('&nbsp;', $tabWidth), $str);
    }

    public static function spToNbsp($str) {
        if (!is_string($str)) {
            throw new InvalidArgumentException();
        }
        return preg_replace('/\x{0020}/', '&nbsp;', $str);
    }

    public static function displayFormat($str) {
        if (!is_string($str)) {
            throw new InvalidArgumentException();
        }

        return nl2br(
            self::tabToNbsp(
                self::spToNbsp(
                    h($str)
                )
            )
        );
    }
}