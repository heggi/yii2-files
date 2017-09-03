<?php

namespace heggi\yii2files\migrations;

use yii\db\Migration;

class m170903_194700_add_index_to_files_table extends Migration {

    public function up() {
        $this->createIndex('filepath_index', 'files', 'filePath');
    }

    public function down() {
        $this->dropIndex('filepath_index', 'files');
    }
}
