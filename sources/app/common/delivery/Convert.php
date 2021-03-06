<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace common\delivery;


use common\models\Prize;
use common\models\prize\Money;

class Convert extends Delivery
{

    public $ratio = 1.;

    /**
     * @param Prize|Money $prize
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function deliver(Prize $prize): bool
    {
        $prize->amount *= $this->ratio;

        /** @var Account $account */
        $account = \Yii::$app->get(Account::class);

        return $account->deliver($prize);
    }

    public function description(): string
    {
        return 'Convert to loyality points';
    }
}
