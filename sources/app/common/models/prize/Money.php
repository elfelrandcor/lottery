<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace common\models\prize;


use common\models\Prize;

/**
 * Class Money
 * @package common\prize
 * @property integer amount
 */
class Money extends Prize
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['amount'], 'safe'],
        ]);
    }

    public function description(): string
    {
        return 'Real money: ' . $this->amount;
    }
}
