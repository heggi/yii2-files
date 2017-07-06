<?php

namespace heggi\yii2files;

use Yii;

class Module extends \yii\base\Module {

    public $filesBasePath = '@app/web/';
    public $filesBaseUrl = '';
    public $imageCompressionQuality = 85;
    //public $controllerNamespace = 'common\modules\files\controllers';

    public function init() {
        parent::init();
    }

    public function getModelSubDir($model) {
        $modelName = $this->getShortClass($model);
        $modelDir = \yii\helpers\Inflector::pluralize($modelName).'/'. $model->id;
        return $modelDir;
    }

    public function getShortClass($obj) {
        $className = get_class($obj);
        if (preg_match('@\\\\([\w]+)$@', $className, $matches)) {
            $className = $matches[1];
        }
        return $className;
    }

    public function getStorePath() {
        return Yii::getAlias($this->filesBasePath . DIRECTORY_SEPARATOR . 'store');
    }

    public function getCachePath() {
        return Yii::getAlias($this->filesBasePath . DIRECTORY_SEPARATOR . 'cache');
    }

    public function getStoreUrl() {
        return $this->filesBaseUrl . '/store/';
    }

    public function getCacheUrl() {
        return $this->filesBaseUrl . '/cache/';
    }

    public function parseSize($notParsedSize) {
        $sizeParts = explode('x', $notParsedSize);
        $part1 = (isset($sizeParts[0]) and $sizeParts[0] != '');
        $part2 = (isset($sizeParts[1]) and $sizeParts[1] != '');
        if($part1 && $part2) {
            if(intval($sizeParts[0]) > 0 && intval($sizeParts[1]) > 0) {
                $size = [
                    'width' => intval($sizeParts[0]),
                    'height' => intval($sizeParts[1])
                ];
            } else {
                $size = null;
            }
        } elseif($part1 && !$part2) {
            $size = [
                'width' => intval($sizeParts[0]),
                'height' => null
            ];
        } elseif(!$part1 && $part2) {
            $size = [
                'width' => null,
                'height' => intval($sizeParts[1])
            ];
        } else {
            throw new \Exception('Something bad with size, sorry!');
        }
        return $size;
    }

}
