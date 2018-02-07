<?php
namespace heggi\yii2files\widgets;

use yii\widgets\InputWidget;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

class MultipleFilesWidget extends InputWidget {
    
    public $delete;
    public $key;
    public $options;

    public function run() {
        $key = $this->key !== null ? $this->key : $this->attribute;

        if($this->model->hasFile($key)) {
            $files = $this->model->getFiles($key);
            echo '<div class="clearfix">';
            foreach($files as $file) {
                echo '<div class="pull-left" style="margin-right: 20px">';
                if($file->isImage) {
                    echo '<div class="thumbnail">';
                    echo Html::img($file->getUrl('x200'));
                    if($this->delete !== null) {
                        echo '<div class="checkbox">';
                        echo Html::activeCheckbox($this->model, $this->delete . "[{$file->id}]", ['label' => 'Удалить', 'title' => 'Отметить для удаления']);
                        echo '</div>';
                    }
                    echo '</div>';
                } else {
                    echo '<div class="checkbox">';
                    echo Html::activeCheckbox($this->model, $this->delete . "[{$file->id}]", ['label' => $file->name, 'title' => 'Отметить для удаления']);
                    echo '</div>';
                }
                
                echo '</div>';
            }
            echo '</div>';
        }

        echo Html::activeFileInput($this->model, $this->attribute . '[]', ArrayHelper::merge(['multiple' => true], $this->options));
    }
}
