<?php
namespace common\fixtures;

use common\models\Fund;
use yii\test\ActiveFixture;

class FundFixture extends ActiveFixture
{
    public $modelClass = Fund::class;

    protected function getData()
    {
        return [
            'actual' => [
                'id' => 1,
                'amount' => 100,
            ],
        ];
    }
}
