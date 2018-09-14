<?php

use yii\db\Migration;

/**
 * Handles the creation of table `deliver_task`.
 */
class m180912_152327_create_deliver_task_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%deliver_task}}', [
            'id' => $this->primaryKey(),
            'status' => $this->integer(),
            'prize_id' => $this->integer(),
            'delivery' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%deliver_task}}');
    }
}
