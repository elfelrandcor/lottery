<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace common\gifter\form;


class Money extends Form
{
    public function getModelClass(): string
    {
        return \common\models\prize\Money::class;
    }
}
