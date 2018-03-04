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
            [['description'], 'string', 'max' => 255],
        ];
    }

    public function getUrl($sizeString = false, $fill = false) {
        if($this->isImage && !empty($sizeString)) {
            $origFile = $this->getModule()->getStorePath() . DIRECTORY_SEPARATOR . $this->filePath;
            $fullPath = $this->getModule()->getCachePath() . DIRECTORY_SEPARATOR . $sizeString . ($fill?'_f':'');
            $fullName = $fullPath . DIRECTORY_SEPARATOR . $this->filePath;
            $fullName = BaseFileHelper::normalizePath($fullName);
            if(!file_exists($fullName)) {
                BaseFileHelper::createDirectory(dirname($fullName), 0777, true);
                $size = $this->getModule()->parseSize($sizeString);
                $image = new \Imagick($origFile);
                $image->setImageCompressionQuality($this->getModule()->imageCompressionQuality);
                if($size){
                    if($size['height'] && $size['width']) {
                        if($fill) {
                            $width = $image->getImageWidth();
                            $height = $image->getImageHeight();
                            if($width/$size['width'] > $height/$size['height']) {
                                $image->scaleImage($size['width'], 0);
                            } else {
                                $image->scaleImage(0, $size['height']);
                            }
                        } else {
                            $image->cropThumbnailImage($size['width'], $size['height']);
                        }
                    }elseif($size['height']){
                        $image->scaleImage(0, $size['height']);
                    }elseif($size['width']){
                        $image->scaleImage($size['width'], 0);
                    }else{
                        throw new \Exception('Something wrong with this->module->parseSize($sizeString)');
                    }
                }
                $image->writeImage($fullName);
            }

            return $this->getModule()->getCacheUrl() . $sizeString . ($fill?'_f':'') . '/' . $this->filePath;
        }
        return $this->getModule()->getStoreUrl() . $this->filePath;
    }

    public function getDownloadUrl() {
        return Url::toRoute(['files/download', 'filePath' => $this->filePath]);
    }
    
    public function getIsImage() {
        return substr($this->mimetype, 0, 6) === 'image/';
    }
}
