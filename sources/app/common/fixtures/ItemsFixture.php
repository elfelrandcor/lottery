<?php
namespace common\fixtures;

use common\models\PhysicalItem;
use yii\test\ActiveFixture;

class ItemsFixture extends ActiveFixture
{
    public $modelClass = PhysicalItem::class;

    protected function getData()
    {
        return [
            ['id' => 1, 'name' => 'gift1', 'amount' => 0],
            ['id' => 2, 'name' => 'gift2', 'amount' => 3],
        ];
    }
}
