<?php

use yii\db\Migration;

class m170823_065936_zakaz_tag extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%zakaz_tag}}', [
            'tag_id' => $this->integer()->comment('ID Tag'),
            'zakaz_id' => $this->integer()->comment('ID Zakaz'),
        ]);

        $this->createIndex('idx_tag', '{{%zakaz_tag}}', 'tag_id');
        $this->addForeignKey(
            'FK_zakaz_tag', '{{%zakaz_tag}}', 'tag_id', '{{%tag}}', 'id'
        );
        $this->createIndex('idx_zakaz', '{{%zakaz_tag}}', 'zakaz_id');
        $this->addForeignKey(
            'FK_zakaz_tag', '{{%zakaz_tag}}', 'zakaz_id', '{{%zakaz}}', 'id'
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%zakaz_tag}}');
    }
}
