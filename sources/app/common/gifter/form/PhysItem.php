<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace common\gifter\form;


class PhysItem extends Form
{
    public function getModelClass(): string
    {
        return \common\models\prize\Gift::class;
    }
}
