<?php

use yii\db\Migration;

class m170327_071747_client extends Migration
{
    public function up()
    {
        $this->createTable('client',[
                'id'=>$this->primaryKey(),
                'fio' => $this->string(86),
                'phone' => $this->string()->notNull(),
                'email' => $this->string(50)->unique(),
                'address' => $this->string(150),
            ]);
    }

    public function down()
    {
        $this->dropTable('client');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
