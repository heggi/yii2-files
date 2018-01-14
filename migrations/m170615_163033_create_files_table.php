<?php

use yii\db\Migration;

class m170615_163033_create_files_table extends Migration {

    public function up() {

        $this->createTable('files', [
            'id' => $this->primaryKey(),
            'filePath' => $this->string(400)->notNull(),
            'itemId' => $this->integer(),
            'modelName' => $this->string(150)->notNull(),
            'mimetype' => $this->string(100),
            'name' => $this->string(100),
            'category' => $this->string(50)->defaultValue(null),
            'order' => $this->integer(),
        ]);
    }

    public function down() {
        $this->dropTable('files');
    }
}
