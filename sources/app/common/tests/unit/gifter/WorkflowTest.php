<?php

namespace common\tests\unit\gifter;

use common\delivery\Account;
use common\fixtures\FundFixture;
use common\fixtures\ItemsFixture;
use common\fixtures\PrizeFixture;
use common\gifter\Gifter;
use common\models\Deliver;
use common\models\Fund;
use common\models\PhysicalItem;
use common\models\Prize;
use common\fixtures\UserFixture;
use common\models\prize\Gift;
use common\models\prize\Money;
use common\models\prize\Points;
use common\models\User;


class WorkflowTest extends \Codeception\Test\Unit
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

    /**
     * @return array
     */
    public function _fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php',
            ],
            'fund' => FundFixture::class,
            'gift' => ItemsFixture::class,
            'prizes' => PrizeFixture::class,
        ];
    }

    /**
     * @throws \common\exception\Exception
     */
    public function testSelectPrize()
    {
        /** @var User $user */
        $user = $this->tester->grabFixture('user', 'user1');
        $prize = $this->gifter->reserve($user);

        $this->assertEquals(Prize::STATUS_RESERVED, $prize->status);
        $this->assertEquals($user->id, $prize->user_id);
    }

    /**
     * @throws \common\exception\Exception
     */
    public function testSelectMoney()
    {
        /** @var User $user */
        $user = $this->tester->grabFixture('user', 'user1');
        /** @var Fund $funds */
        $funds = $this->tester->grabFixture('fund', 'actual');
        $balance = $funds->amount;

        Gifter::$forceType = 'Money';

        /** @var Money $prize */
        $prize = $this->gifter->reserve($user);

        $funds->refresh();
        $this->assertEquals($balance - $prize->amount, $funds->amount);
    }

    /**
     * @throws \common\exception\Exception
     */
    public function testSelectInsufficientMoney()
    {
        /** @var User $user */
        $user = $this->tester->grabFixture('user', 'user1');

        /** @var Fund $funds */
        $funds = $this->tester->grabFixture('fund', 'actual');

        $funds->amount = 0;
        $funds->save();

        Gifter::$forceType = 'Money';

        $prize = $this->gifter->reserve($user);

        $this->assertInstanceOf(Points::class, $prize);
    }

    /**
     * @throws \common\exception\Exception
     */
    public function testSelectGift()
    {
        /** @var User $user */
        $user = $this->tester->grabFixture('user', 'user1');
        /** @var PhysicalItem $item */
        $item = $this->tester->grabFixture('gift', 1);
        $count = $item->amount;

        Gifter::$forceType = 'Gift';

        /** @var Gift $prize */
        $prize = $this->gifter->reserve($user);

        $item->refresh();

        $this->assertEquals($item->id, $prize->item_id);
        $this->assertEquals($count - 1, $item->amount);
    }

    public function testDeclineGift()
    {
        /** @var PhysicalItem $item */
        $item = $this->tester->grabFixture('gift', 0);
        $amount = $item->amount;

        /** @var \common\models\prize\Money $prize */
        $prize = $this->tester->grabFixture('prizes', 'reserved_gift');
        $prize = $this->gifter->decline($prize);

        $this->assertEquals(Prize::STATUS_DECLINED, $prize->status);

        $item->refresh();

        $this->assertEquals($amount + 1, $item->amount);
    }

    /**
     * @throws \common\exception\DeliveryException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\InvalidArgumentException
     */
    public function testAcceptPrize()
    {
        /** @var User $user */
        $user = $this->tester->grabFixture('user', 'user1');
        $balance = $user->balance;

        /** @var \common\models\prize\Points $prize */
        $prize = $this->tester->grabFixture('prizes', 'reserved_points');

        $prize = $this->gifter->accept($prize, new Account());

        $this->assertEquals(Prize::STATUS_ACCEPTED, $prize->status);

        /** @var Deliver $deliveryTask */
        $deliveryTask = Deliver::find()->where(['prize_id' => $prize->id])->one();
        $this->gifter->deliver($deliveryTask);

        $user->refresh();
        $this->assertEquals($balance + $prize->amount, $user->balance);
    }

    public function testDeclinePrize()
    {
        /** @var User $user */
        $user = $this->tester->grabFixture('user', 'user1');
        /** @var Fund $funds */
        $funds = $this->tester->grabFixture('fund', 'actual');
        $balance = $funds->amount;

        /** @var \common\models\prize\Money $prize */
        $prize = $this->tester->grabFixture('prizes', 'reserved_fund');
        $prize = $this->gifter->decline($prize);

        $this->assertEquals(Prize::STATUS_DECLINED, $prize->status);

        $funds->refresh();

        $user->refresh();
        $this->assertEquals($balance + $prize->amount, $funds->amount);
    }
}
