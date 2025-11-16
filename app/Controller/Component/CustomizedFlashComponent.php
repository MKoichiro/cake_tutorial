<?php

App::uses('FlashComponent', 'Controller/Component');

class CustomizedFlashComponent extends FlashComponent {
    // flash.ctp に type を通知できるようにオーバーライド
    public function success($message, $options = []) {
        $options += ['element' => 'flash', 'params' => ['type' => 'success']];
        return parent::set($message, $options);
    }
    public function error($message, $options = []) {
        $options += ['element' => 'flash', 'params' => ['type' => 'error']];
        return parent::set($message, $options);
    }
    // info は独自に追加
    public function info($message, $options = []) {
        $options += ['element' => 'flash', 'params' => ['type' => 'info']];
        return parent::set($message, $options);
    }
}
