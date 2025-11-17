<?php

/**
 * 頻出ログのテンプレート
 */
class Logger {
    // Controller
    public static function startAction($className, $functionName) {
        CakeLog::write('info', '******************** ' . $className . '#' . $functionName . ' START ********************');
    }

    public static function postData($requestData, $className, $functionName) {
        CakeLog::write(
            'info',
            '--- ' . $className . '#' . $functionName . ' ---' . "\n" .
            'Post data:'."\n".
            print_r($requestData, true)
        );
    }

    public static function invalidRequestData($className, $functionName) {
        CakeLog::write(
            'error',
            '--- ' . $className . '#' . $functionName . ' ---' . "\n" .
            'Invalid Request Data given.'
        );
    }

    public static function sessionValue($dataFromSession, $className, $functionName) {
        CakeLog::write(
            'info',
            '--- ' . $className . '#' . $functionName . ' ---' . "\n" .
            'Read session to get user-submitted thread data and comment data:'."\n".
            print_r($dataFromSession, true)
        );
    }

    public static function violateOwner($className, $functionName) {
        CakeLog::write(
            'error',
            '--- ' . $className . '#' . $functionName . ' ---' . "\n" .
            'Authorization error: No permission as owner.'
        );
    }

    // Service
    public static function sqlKey($sqlKey, $className, $functionName) {
        CakeLog::write(
            'info',
            '--- '.$className.'#'.$functionName.' ---'."\n".
            'Executing query identified by the key '.$sqlKey.'. See "app/Config/Sql/*.php" to check raw queries.'
        );
    }

    public static function dbFailure($params, $message, $className, $functionName) {
        CakeLog::write(
            'error',
            '--- '.$className.'#'.$functionName.' ---'."\n".
            'Failure occurred during DB operation with params:'."\n".
            print_r($params, true)."\n".
            'The following error occurred: '."\n". $message
        );
    }

    public static function notFound($entityName, $fieldName, $fieldValue, $className, $functionName) {
        CakeLog::write(
            'error',
            '--- '.$className.'#'.$functionName.' ---'."\n".
            $entityName . ' (.' . strtoupper($fieldName) . '=' . $fieldValue . ') is not found.'
        );
    }

    public static function duplicate($entityName, $fieldName, $fieldValue, $className, $functionName) {
        CakeLog::write(
            'error',
            '--- '.$className.'#'.$functionName.' ---'."\n".
            $entityName . ' (.' . strtoupper($fieldName) . '=' . $fieldValue . ') is duplicated.'
        );
    }
}