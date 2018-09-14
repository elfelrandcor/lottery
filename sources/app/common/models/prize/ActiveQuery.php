<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace common\models\prize;


use common\models\User;

class ActiveQuery extends \yii\db\ActiveQuery
{

    public function byUser(User $user)
    {
        return $this->andWhere(['user_id' => $user->id]);
    }
}
