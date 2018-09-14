<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace common\delivery;


use common\models\Prize;
use common\models\prize\Points;

class Account extends Delivery
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

    public function description(): string
    {
        return 'Loyality points';
    }
}
