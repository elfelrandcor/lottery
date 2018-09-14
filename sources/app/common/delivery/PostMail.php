<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace common\delivery;


use common\gifter\DeliveryInterface;
use common\models\Prize;

class PostMail implements DeliveryInterface
{

    public function deliver(Prize $prize): bool
    {
        return true;
    }
}
