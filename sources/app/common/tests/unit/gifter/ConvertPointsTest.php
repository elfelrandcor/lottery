<?php

namespace common\tests\unit\gifter;


use common\delivery\Convert;
use common\fixtures\FundFixture;
use common\fixtures\PrizeFixture;
use common\fixtures\UserFixture;
use common\gifter\Gifter;
use common\models\Deliver;
use common\models\prize\Points;
use common\models\User;

class ConvertPointsTest extends \Codeception\Test\Unit
{

    /**
     * @var \common\tests\UnitTester
     */
    protected $tester;

    /** @var Gifter */
    protected $gifter;

    protected function _before()
    {
        parent::_before();

        $this->gifter = \Yii::$app->get('gifter');
    }

    public function _fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php',
            ],
            'fund' => FundFixture::class,
            'prize' => PrizeFixture::class,
        ];
    }

    /**
     * @throws \common\exception\DeliveryException
     * @throws \yii\base\InvalidConfigException
     */
    public function testConvert()
    {
        /** @var User $user */
        $user = $this->tester->grabFixture('user', 'user1');
        $balance = $user->balance;

        /** @var Points $prize */
        $prize = $this->tester->grabFixture('prize', 'reserved_points');

        $deliveryTask = new Deliver([
            'prize_id' => $prize->id,
            'delivery' => Convert::class,
        ]);
        $this->assertTrue($deliveryTask->save());

        $this->gifter->deliver($deliveryTask);

        $user->refresh();
        /** @var Convert $delivery */
        $delivery = \Yii::$app->get(Convert::class);

        $value = $balance + ($prize->amount * $delivery->ratio);
        $value = round($value);
        $this->assertEquals($value, $user->balance);
    }

}
