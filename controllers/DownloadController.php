<?php

namespace heggi\yii2files\controllers;

use yii\web\Controller;
use heggi\yii2files\models\Files;
use yii\web\NotFoundHttpException;
use heggi\yii2files\ModuleTrait;

class DownloadController extends Controller {

    use ModuleTrait;

    public function actionIndex($filePath) {
        $file = Files::find()->where(['filePath' => $filePath])->one();
        if(!$file) {
            throw new NotFoundHttpException();
        }

        $origFile = $this->getModule()->getStorePath() . DIRECTORY_SEPARATOR . $file->filePath;

        return \Yii::$app->response->sendFile($origFile, $file->name);
    }
}
