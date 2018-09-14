<?php

use yii\db\Migration;

/**
 * Handles the creation of table `prize`.
 */
class m180912_150925_create_prize_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%prize}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'status' => $this->integer(),
            'amount' => $this->integer(),
            'item_id' => $this->integer(),
            'type' => $this->string(),
        ]);

        $this->createIndex('user_id', '{{%prize}}', 'user_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%prize}}');
    }
}
