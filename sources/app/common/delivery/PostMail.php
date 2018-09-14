<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace common\delivery;


use common\models\Prize;

class PostMail extends Delivery
{

    public function deliver(Prize $prize): bool
    {
        return true;
    }

    public function description(): string
    {
        return 'Post mail delivery';
    }
}
