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

    /** @var PrizeInterface[] */
    public $types;

    /**
     * @param User $user
     * @return Prize
     * @throws Exception
     */
    public function reserve(User $user): Prize
    {
        return $this->tryReserve($user) ?: $this->createPoints($user);
    }

    /**
     * @param Prize $prize
     * @return Prize
     * @throws \yii\base\InvalidArgumentException
     */
    public function accept(Prize $prize): Prize
    {
        $prize->setAsAccepted();

        return $prize;
    }

    /**
     * @param Prize $prize
     * @return Prize
     * @throws \yii\base\InvalidArgumentException
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

    protected function createDeliverTask(Prize $prize): Deliver
    {
        //todo
    }

    protected function tryReserve(User $user)
    {
        if (!$this->types) {
            return null;
        }
        $max = \count($this->types) - 1;
        $num = mt_rand(0, $max);


        /** @var PrizeInterface $type */
        $type = \Yii::createObject($this->types[$num]);

        $class = $type->getModelClass();
        if (!class_exists($class)) {
            throw new Exception(sprintf('Prize class `%s` doesn\'t exist', $class));
        }

        $method = sprintf('create%s', static::$forceType ?: (new \ReflectionClass($class))->getShortName());

        return $this->{$method}($user);
    }

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

    public function createGift(User $user)
    {
        if (!$item = $this->findFreeItem()) {
            return null;
        }

        $prize = null;
        $transaction = \Yii::$app->db->beginTransaction();

        try {
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

    protected function getPointsAmount(): int
    {
        return random_int(1, 100);
    }

    protected function getMoneyAmount(): int
    {
        return random_int(1, 10);
    }

    protected function findActualFund(): Fund
    {
        return Fund::find()->one();
    }

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
