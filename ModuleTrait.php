<?php

namespace heggi\yii2files;

use yii\base\Exception;

trait ModuleTrait {

    private $_module;

    protected function getModule()
    {
        if ($this->_module == null) {
            $this->_module = \Yii::$app->getModule('files');
        }
        if(!$this->_module){
            throw new Exception("Files module not found, may be you didn't add it to your config?");
        }
        return $this->_module;
    }
}