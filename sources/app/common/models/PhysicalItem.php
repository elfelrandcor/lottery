<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace common\models;


use yii\db\ActiveRecord;

/**
 * Class Gift
 * @package common\models
 * @property integer id
 * @property string name
 * @property integer amount
 */
class PhysicalItem extends ActiveRecord
{

    public static function tableName()
    {
        return '{{%phys_item}}';
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['name', 'amount'], 'safe']
        ]);
    }

}
