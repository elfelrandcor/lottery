<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace common\gifter\form;


use yii\base\BaseObject;

class Money extends BaseObject implements \common\gifter\PrizeInterface
{
    public $deliveries = [];

    public function getDeliveries(): array
    {
        return $this->deliveries;
    }

    public function getModelClass(): string
    {
        return \common\models\prize\Money::class;
    }
}
