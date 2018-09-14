<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace common\delivery;


use common\gifter\DeliveryInterface;
use yii\base\BaseObject;

abstract class Delivery extends BaseObject implements DeliveryInterface
{

    public function name(): string
    {
        return str_replace('\\', '\\\\', static::class);
    }
}
