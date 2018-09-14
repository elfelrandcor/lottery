<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace common\models;


use yii\db\ActiveRecord;

/**
 * Class Fund
 * @package common\models
 * @property integer id
 * @property integer amount
 */
class Fund extends ActiveRecord
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['amount'], 'safe'],
        ]);
    }
}
