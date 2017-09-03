<?php

namespace heggi\yii2files\migrations;

use yii\db\Migration;

class m170903_192700_add_description_column_to_files_table extends Migration {

    public function up() {
        $this->addColumn('files', 'description', $this->string(255));
    }

    public function down() {
        $this->dropColumn('files', 'description');
    }
}
