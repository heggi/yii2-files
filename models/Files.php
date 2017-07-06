<?php

namespace heggi\yii2files\models;

use Yii;
use yii\helpers\Url;
use yii\helpers\BaseFileHelper;
use heggi\yii2files\ModuleTrait;

class Files extends \yii\db\ActiveRecord {

    use ModuleTrait;

    public static function tableName() {
        return 'files';
    }

    public function rules() {
        return [
            [['filePath', 'modelName'], 'required'],
            [['itemId', 'order'], 'integer'],
            [['filePath'], 'string', 'max' => 400],
            [['modelName'], 'string', 'max' => 150],
            [['category'], 'string', 'max' => 50],
            [['mimetype', 'name'], 'string', 'max' => 100],
        ];
    }

    public function getUrl($sizeString = false) {
        if(in_array($this->mimetype, ['image/png', 'image/jpeg']) && !empty($sizeString)) {
            $origFile = $this->getModule()->getStorePath() . DIRECTORY_SEPARATOR . $this->filePath;
            $fullPath = $this->getModule()->getCachePath() . DIRECTORY_SEPARATOR . $sizeString;
            $fullName = $fullPath . DIRECTORY_SEPARATOR . $this->filePath;
            $fullName = BaseFileHelper::normalizePath($fullName);
            if(!file_exists($fullName)) {
                BaseFileHelper::createDirectory(dirname($fullName), 0777, true);
                $size = $this->getModule()->parseSize($sizeString);
                $image = new \Imagick($origFile);
                $image->setImageCompressionQuality($this->getModule()->imageCompressionQuality);
                if($size){
                    if($size['height'] && $size['width']){
                        $image->cropThumbnailImage($size['width'], $size['height']);
                    }elseif($size['height']){
                        $image->thumbnailImage(0, $size['height']);
                    }elseif($size['width']){
                        $image->thumbnailImage($size['width'], 0);
                    }else{
                        throw new \Exception('Something wrong with this->module->parseSize($sizeString)');
                    }
                }
                $image->writeImage($fullName);
            }

            return $this->getModule()->getCacheUrl() . $sizeString . '/' . $this->filePath;
        }
        return $this->getModule()->getStoreUrl() . $this->filePath;
    }
}
