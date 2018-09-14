<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace common\gifter;


use common\exception\DeliveryException;
use common\exception\Exception;
use common\models\Deliver;
use common\models\Fund;
use common\models\PhysicalItem;
use common\models\Prize;
use common\models\prize\Gift;
use common\models\prize\Money;
use common\models\prize\Points;
use common\models\User;
use yii\base\Component;
use yii\db\Expression;

class Gifter extends Component
{
    public static $forceType;

    /** @var PrizeFormInterface[] */
    public $types;

    /**
     * @param Prize $prize
     * @return PrizeFormInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function getForm(Prize $prize): PrizeFormInterface
    {
        return \Yii::createObject($this->types[\get_class($prize)]);
    }

    /**
     * @param User $user
     * @return Prize
     * @throws Exception
     * @throws \Exception
     */
    public function reserve(User $user): Prize
    {
        if ($prize = $user->getActivePrize()) {
            return $prize;
        }

        return $this->tryReserve($user) ?: $this->createPoints($user);
    }

    /**
     * @param Prize $prize
     * @param \common\gifter\DeliveryInterface $delivery
     * @return Prize
     * @throws \yii\base\InvalidArgumentException
     */
    public function accept(Prize $prize, DeliveryInterface $delivery): Prize
    {
        $prize->setAsAccepted();

        $this->createDeliverTask($prize, $delivery);

        return $prize;
    }

    /**
     * @param Prize $prize
     * @return Prize
     * @throws \yii\base\InvalidArgumentException
     * @throws \yii\db\Exception
     */
    public function decline(Prize $prize): Prize
    {
        $prize->setAsDeclined();

        if ($prize instanceof Money) {
            $this->freeFunds($prize);
        }

        if ($prize instanceof Gift) {
            $this->freeItem($prize);
        }

        return $prize;
    }

    /**
     * @param Deliver $task
     * @throws DeliveryException
     * @throws \yii\base\InvalidConfigException
     */
    public function deliver(Deliver $task)
    {
        if (!class_exists($task->delivery)) {
            throw new DeliveryException(sprintf('Delivery class `%s` doesn\'t exist', $task->delivery));
        }
        /** @var DeliveryInterface $delivery */
        $delivery = \Yii::$app->get($task->delivery);

        if (!$delivery->deliver($task->prize)) {
            throw new DeliveryException('Deliver failed');
        }
        $task->setAsDelivered();
    }

    /**
     * @param Prize $prize
     * @param DeliveryInterface $delivery
     * @return Deliver
     */
    protected function createDeliverTask(Prize $prize, DeliveryInterface $delivery): Deliver
    {
        $task = new Deliver([
            'prize_id' => $prize->id,
            'delivery' => \get_class($delivery),
        ]);
        $task->save();

        return $task;
    }

    /**
     * @param User $user
     * @return null
     * @throws Exception
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     * @throws \Exception
     */
    protected function tryReserve(User $user)
    {
        if (!$this->types) {
            return null;
        }
        $max = \count($this->types) - 1;
        $num = random_int(0, $max);

        $list = array_values($this->types);
        /** @var PrizeFormInterface $type */
        $type = \Yii::createObject($list[$num]);

        $class = $type->getModelClass();
        if (!class_exists($class)) {
            throw new Exception(sprintf('Prize class `%s` doesn\'t exist', $class));
        }

        $method = sprintf('create%s', static::$forceType ?: (new \ReflectionClass($class))->getShortName());

        return $this->{$method}($user);
    }

    /**
     * @param User $user
     * @return Points
     * @throws \Exception
     */
    protected function createPoints(User $user): Points
    {
        $prize = new Points([
            'user_id' => $user->id,
            'status' => Prize::STATUS_RESERVED,
            'amount' => $this->getPointsAmount(),
        ]);
        $prize->save();

        return $prize;
    }

    /**
     * @param User $user
     * @return Money|null
     * @throws \yii\db\Exception
     * @throws \Exception
     */
    public function createMoney(User $user)
    {
        $amount = $this->getMoneyAmount();
        $fund = $this->findActualFund();
        $prize = null;

        $fund->amount -= $amount;
        if ($fund->amount < 0) {
            return null;
        }
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            //Напрямую, чтобы избежать race condition
            $sql = sprintf('update %s set amount = amount - %s where id = %s', Fund::tableName(), $amount, $fund->id);
            \Yii::$app
                ->db
                ->createCommand()
                ->setSql($sql)
                ->execute()
            ;

            $prize = new Money([
                'user_id' => $user->id,
                'status' => Prize::STATUS_RESERVED,
                'amount' => $amount,
            ]);
            $prize->save();

            $transaction->commit();

        } catch (\Exception $e) {
            $transaction->rollBack();
        }

        return $prize;
    }

    /**
     * @param User $user
     * @return Gift|null
     * @throws \yii\db\Exception
     */
    public function createGift(User $user)
    {
        if (!$item = $this->findFreeItem()) {
            return null;
        }

        $prize = null;
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            //Напрямую, чтобы избежать race condition
            $sql = sprintf('update %s set amount = amount - 1 where id = %s', PhysicalItem::tableName(), $item->id);
            \Yii::$app
                ->db
                ->createCommand()
                ->setSql($sql)
                ->execute()
            ;

            $prize = new Gift([
                'user_id' => $user->id,
                'status' => Prize::STATUS_RESERVED,
                'item_id' => $item->id,
            ]);
            $prize->save();

            $transaction->commit();

        } catch (\Exception $e) {
            $transaction->rollBack();
        }

        return $prize;
    }

    protected function findFreeItem()
    {
        /** @var PhysicalItem $item */
        if (!$item =
            PhysicalItem::find()
                        ->where('amount > 0')
                        ->orderBy(new Expression('rand()'))
                        ->limit(1)
                        ->one()
        ) {
            return null;
        }

        return $item;
    }

    /**
     * @return int
     * @throws \Exception
     */
    protected function getPointsAmount(): int
    {
        return random_int(1, 100);
    }

    /**
     * @return int
     * @throws \Exception
     */
    protected function getMoneyAmount(): int
    {
        return random_int(1, 10);
    }

    protected function findActualFund(): Fund
    {
        return Fund::find()->one();
    }

    /**
     * @param Money $prize
     * @throws \yii\db\Exception
     */
    protected function freeFunds(Money $prize)
    {
        $fund = $this->findActualFund();

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $sql = sprintf('update %s set amount = amount + %s where id = %s', Fund::tableName(), $prize->amount, $fund->id);
            \Yii::$app
                ->db
                ->createCommand()
                ->setSql($sql)
                ->execute()
            ;

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
        }

    }

    /**
     * @param Gift $prize
     * @throws \yii\db\Exception
     */
    protected function freeItem(Gift $prize)
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $sql = sprintf('update %s set amount = amount + 1 where id = %s', PhysicalItem::tableName(), $prize->item_id);
            \Yii::$app
                ->db
                ->createCommand()
                ->setSql($sql)
                ->execute()
            ;

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
        }
    }
}
