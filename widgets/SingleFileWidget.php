<?php
namespace heggi\yii2files\widgets;

use yii\widgets\InputWidget;
use yii\bootstrap\Html;

class SingleFileWidget extends InputWidget {
    
    public $delete;
    public $key;
    public $options;

    public function run() {
        $key = $this->key !== null ? $this->key : $this->attribute;

        if($this->model->hasFile($key)) {
            $file = $this->model->getFile($key);
            echo '<div class="thumbnail">';
            if($file->isImage) {
                echo Html::img($file->getUrl('x200'));
                if($this->delete !== null) {
                    echo '<div class="checkbox">';
                    echo Html::activeCheckbox($this->model, $this->delete, ['label' => 'Удалить', 'title' => 'Отметить для удаления']);
                    echo '</div>';
                }
            } else {
                echo '<div class="checkbox">';
                echo Html::activeCheckbox($this->model, $this->delete, ['label' => $file->name, 'title' => 'Отметить для удаления']);
                echo '</div>';
            }
            
            echo '</div>';
        }

        echo Html::activeFileInput($this->model, $this->attribute, $this->options);
    }
}
