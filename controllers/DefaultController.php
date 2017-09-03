<?php

namespace heggi\yii2files\controllers;

use yii\web\Controller;
use heggi\yii2files\models\Files;
use yii\web\NotFoundHttpException;

class DefaultController extends Controller {

    public function actionIndex($filePath) {
        $file = Files::find(['filePath' => $filePath])->one();
        if(!$file) {
            throw new NotFoundHttpException();
        }

        $origFile = $this->getModule()->getStorePath() . DIRECTORY_SEPARATOR . $file->filePath;

        return Yii::$app->response->sendFile($origFile, $file->name);
    }
}
