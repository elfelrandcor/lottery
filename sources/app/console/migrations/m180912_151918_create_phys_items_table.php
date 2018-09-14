<?php

use yii\db\Migration;

/**
 * Handles the creation of table `phys_items`.
 */
class m180912_151918_create_phys_items_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%phys_item}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'amount' => $this->integer()->unsigned(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%phys_item}}');
    }
}
