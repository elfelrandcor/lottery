<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace common\models;


use yii\db\ActiveRecord;

/**
 * Class Deliver
 * @package common\models
 * @property integer prize_id
 * @property string delivery
 * @property integer status
 *
 * @property Prize prize
 */
class Deliver extends ActiveRecord
{

    const STATUS_NEW   = 10;
    const STATUS_DONE  = 20;
    const STATUS_ERROR = 100;

    public static function tableName()
    {
        return '{{%deliver_task}}';
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['prize_id', 'delivery'], 'required'],
            [['status'], 'default', 'value' => static::STATUS_NEW],
        ]);
    }

    public function getPrize(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Prize::class, ['id' => 'prize_id']);
    }

    public function setAsDelivered($save = true)
    {
        $this->status = static::STATUS_DONE;
        if ($save) {
            $this->save(false, ['status']);
        }

        return $this;
    }
}
