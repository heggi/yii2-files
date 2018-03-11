<?php

namespace heggi\yii2files\behaviors;

use Yii;
use yii\base\Behavior;
use yii\helpers\BaseFileHelper;

use heggi\yii2files\models\Files;
use heggi\yii2files\ModuleTrait;

class FilesBehave extends Behavior {

    use ModuleTrait;

    public function setFile($file, $category = null) {
        if(!$file) return false;

        if (!$this->owner->primaryKey) {
            throw new \Exception('Owner must have primaryKey when you attach image!');
        }

        $fileName = substr(md5(microtime(true) . $file->baseName), 4, 6) . '.' . $file->extension;
        $subDir = $this->getModule()->getModelSubDir($this->owner);
        $storePath = $this->getModule()->getStorePath($this->owner);

        $absolutePath = $storePath . DIRECTORY_SEPARATOR . $subDir . DIRECTORY_SEPARATOR . $fileName;

        BaseFileHelper::createDirectory($storePath . DIRECTORY_SEPARATOR . $subDir, 0775, true);
        $file->saveAs($absolutePath);

        if (!file_exists($absolutePath)) {
            throw new \Exception('Cant copy file! ' . $file->tempName . ' to ' . $absolutePath);
        }

        $f = new Files;
        $f->itemId = $this->owner->primaryKey;
        $f->filePath = $subDir . '/' . $fileName;
        $f->modelName = $this->getModule()->getShortClass($this->owner);
        $f->mimetype = $file->type;
        $f->category = $category;
        $f->name = $file->name;

        if(!$f->save()){
            return false;
        }

        return $f;
    }

    public function setFiles($files, $category = null) {
        if(!is_array($files)) return false;

        foreach($files as $file) {
            $this->setFile($file, $category);
        }
    }

    public function getFile($category = null) {
        $query = Files::find();
        $finder = $this->getFilesFinder(['category' => $category]);
        $query->where($finder);
        $query->orderBy(['order' => SORT_ASC]);
        $file = $query->one();
        if(!$file) {
            $file = new Files;
        }
        return $file;
    }

    public function getFiles($category = null) {
        $query = Files::find();
        $finder = $this->getFilesFinder(['category' => $category]);
        $query->where($finder);
        $query->orderBy(['order' => SORT_ASC]);
        $file = $query->all();
        if(!$file) {
            $file = [new Files];
        }
        return $file;
    }

    public function hasFile($category = null) {
        $query = Files::find();
        $finder = $this->getFilesFinder(['category' => $category]);
        $query->where($finder);
        return $query->count();
    }

    public function removeFile($category = null, $id = null) {
        $query = Files::find();
        $finder = $this->getFilesFinder(['category' => $category]);
        $query->where($finder);
        if($id !== null) {
            $query->andWhere(['id' => $id]);
        }
        $file = $query->one();
        if($file) {
            $filePath = $this->getModule()->getStorePath($this->owner) . '/' . $file->filePath;
            @unlink(BaseFileHelper::normalizePath($filePath));
            $file->delete();
            return true;
        }
        return false;
    }

    public function removeFiles($category = null, $ids = []) {
        foreach($ids as $id) {
            $this->removeFile($category, $id);
        }
    }

    public function setFileParam($id, $param, $value) {
        $finder = $this->getFilesFinder(['id' => $id]);
        $query = Files::find();
        $query->where($finder);
        $file = $query->one();
        if(!$file) return false;
        $file->{$param} = $value;
        return $file->save();
    }

    private function getFilesFinder($additionWhere = false) {
        $base = [
            'itemId' => $this->owner->primaryKey,
            'modelName' => $this->getModule()->getShortClass($this->owner),
        ];
        if ($additionWhere) {
            $base = \yii\helpers\BaseArrayHelper::merge($base, $additionWhere);
        }
        return $base;
    }

    public function getImageUrl($category = null, $size = false, $fill = false, $noimage = '') {
        if(!$this->hasFile($category)) return $noimage;

        $file = $this->getFile($category);
        if(!$file->isImage) return $noimage;
        
        return $file->getUrl($size, $fill);
    }

    public function getImagesUrl($category = null, $size = false, $fill = false, $noimage = '') {
        if(!$this->hasFile($category)) return empty($noimage)?[]:[$noimage];

        $images = [];
        $files = $this->getFiles($category);
        foreach($files as $file) {
            if(!$file->isImage) continue;
            $images[] = $file->getUrl($size, $fill);
        }
        
        return empty($images) ? (empty($noimage)?[]:[$noimage]) : $images;
    }

    public function getImages($category = null) {
        if(!$this->hasFile($category)) return [];

        $images = [];
        $files = $this->getFiles($category);
        foreach($files as $file) {
            if(!$file->isImage) continue;
            $images[] = $file;
        }

        return $images;
    }
}
