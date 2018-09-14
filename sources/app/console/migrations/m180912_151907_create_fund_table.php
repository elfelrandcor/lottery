<?php

use yii\db\Migration;

/**
 * Handles the creation of table `fund`.
 */
class m180912_151907_create_fund_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%fund}}', [
            'id' => $this->primaryKey(),
            'amount' => $this->integer()->unsigned(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%fund}}');
    }
}
