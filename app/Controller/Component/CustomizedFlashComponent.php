<?php

App::uses('FlashComponent', 'Controller/Component');

class CustomizedFlashComponent extends FlashComponent {
    public function success($message, $options = []) {
        $options += ['element' => 'flash', 'params' => ['type' => 'success']];
        return parent::set($message, $options);
    }
    public function error($message, $options = []) {
        $options += ['element' => 'flash', 'params' => ['type' => 'error']];
        return parent::set($message, $options);
    }
    // 追加：info
    public function info($message, $options = []) {
        $options += ['element' => 'flash', 'params' => ['type' => 'info']];
        return parent::set($message, $options);
    }
}
