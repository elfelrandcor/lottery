<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace common\models\prize;


use common\models\PhysicalItem;
use common\models\Prize;

/**
 * Class Gift
 * @package common\models\prize
 *
 * @property integer item_id
 * @property PhysicalItem item
 */
class Gift extends Prize
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['item_id'], 'safe'],
        ]);
    }

    public function description(): string
    {
        return 'Gift:' . $this->item->name;
    }

    public function getItem(): \yii\db\ActiveQuery
    {
        return $this->hasOne(PhysicalItem::class, ['id' => 'item_id']);
    }
}
