<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace common\delivery;


use common\gifter\DeliveryInterface;
use common\models\Prize;
use common\models\prize\Points;
use yii\base\BaseObject;

class Account extends BaseObject implements DeliveryInterface
{
    /**
     * @param Prize|Points $prize
     * @return bool
     */
    public function deliver(Prize $prize): bool
    {
        $user = $prize->user;
        $user->balance += $prize->amount;
        return $user->save(true, ['balance']);
    }
}
