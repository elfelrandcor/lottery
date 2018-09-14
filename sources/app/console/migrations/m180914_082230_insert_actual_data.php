<?php

use yii\db\Migration;

/**
 * Class m180914_082230_insert_actual_data
 */
class m180914_082230_insert_actual_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        for ($i = 1; $i <= 10; $i++) {
            $item = new \common\models\PhysicalItem([
                'name' => 'item_' . $i,
                'amount' => mt_rand(1, 3),
            ]);
            $item->save();
        }

        $fund = new \common\models\Fund(['amount' => 100]);
        $fund->save();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180914_082230_insert_actual_data cannot be reverted.\n";

        return false;
    }
}
