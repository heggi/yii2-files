<?php
namespace heggi\yii2files\widgets;

use yii\widgets\InputWidget;
use yii\helpers\Html;

class SingleFile extends InputWidget {


    public function run() {
        $attr_id = Html::getInputId($this->model, $this->attribute);

    }
}