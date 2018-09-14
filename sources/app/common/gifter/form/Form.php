<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace common\gifter\form;


use common\gifter\PrizeFormInterface;
use yii\base\BaseObject;

abstract class Form extends BaseObject implements PrizeFormInterface
{
    public $deliveries = [];

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        foreach ($this->deliveries as &$delivery) {
            $delivery = \Yii::createObject($delivery);
        }
    }

    public function getDeliveries(): array
    {
        return $this->deliveries;
    }

}
