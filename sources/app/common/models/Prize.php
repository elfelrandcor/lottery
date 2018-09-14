<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace common\models;


use common\models\prize\ActiveQuery;
use yii\base\InvalidArgumentException;

/**
 * Class Transaction
 * @package common\models
 * @property integer id
 * @property integer user_id
 * @property integer status
 * @property User user
 */
class Prize extends StiActiveRecord
{
    const STATUS_RESERVED = 100;
    const STATUS_ACCEPTED = 200;
    const STATUS_DECLINED = 300;

    public static function tableName()
    {
        return '{{%prize}}';
    }

    public static function find(): ActiveQuery
    {
        return new ActiveQuery(static::class);
    }

    /**
     * @param bool $save
     * @return Prize
     * @throws InvalidArgumentException
     */
    public function setAsAccepted($save = true): Prize
    {
        return $this->setAs(static::STATUS_ACCEPTED, $save);
    }

    /**
     * @param bool $save
     * @return Prize
     * @throws InvalidArgumentException
     */
    public function setAsDeclined($save = true): Prize
    {
        return $this->setAs(static::STATUS_DECLINED, $save);
    }

    /**
     * @param $status
     * @param bool $save
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setAs($status, $save = true): Prize
    {
        if (!\in_array($status, [static::STATUS_ACCEPTED, static::STATUS_DECLINED, static::STATUS_RESERVED], true)) {
            throw new InvalidArgumentException('Status incorrect');
        }

        $this->status = $status;
        if ($save) {
            $this->save(false, ['status']);
        }

        return $this;
    }

    public function getUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
