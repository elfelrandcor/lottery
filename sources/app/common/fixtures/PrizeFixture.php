<?php
namespace common\fixtures;

use common\models\Prize;
use common\models\prize\Gift;
use common\models\prize\Money;
use common\models\prize\Points;
use yii\test\ActiveFixture;

class PrizeFixture extends ActiveFixture
{
    public $modelClass = Prize::class;

    protected function getData()
    {
        return [
            'reserved_fund' => [
                'user_id' => 1,
                'status' => Prize::STATUS_RESERVED,
                'type' => Money::class,
                'amount' => 10,
            ],
            'reserved_points' => [
                'user_id' => 1,
                'status' => Prize::STATUS_RESERVED,
                'type' => Points::class,
                'amount' => 20,
            ],
            'reserved_gift' => [
                'user_id' => 1,
                'status' => Prize::STATUS_RESERVED,
                'type' => Gift::class,
                'item_id' => 1,
            ],
            'accept' => [
                'user_id' => 1,
                'status' => Prize::STATUS_ACCEPTED,
                'type' => Points::class,
                'amount' => 20,
            ],
            'decline' => [
                'user_id' => 1,
                'status' => Prize::STATUS_DECLINED,
                'type' => Gift::class,
                'item_id' => 1,
            ],
        ];
    }
}
