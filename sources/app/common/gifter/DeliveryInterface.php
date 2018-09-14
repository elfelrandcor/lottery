<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace common\gifter;


use common\models\Prize;

interface DeliveryInterface
{
    public function deliver(Prize $prize): bool;
}
