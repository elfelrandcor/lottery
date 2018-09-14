<?php

use yii\db\Migration;

/**
 * Class m180912_152816_add_balance_column_into_user_table
 */
class m180912_152816_add_balance_column_into_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'balance', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'balance');
    }
}
