<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace common\gifter;


interface PrizeFormInterface
{

    /**
     * @return DeliveryInterface[]
     */
    public function getDeliveries(): array;

    public function getModelClass(): string;
}
