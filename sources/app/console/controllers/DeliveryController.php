<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace console\controllers;


use common\exception\DeliveryException;
use common\gifter\Gifter;
use common\models\Deliver;
use yii\base\InvalidConfigException;

class DeliveryController extends ConsoleController
{

    public function actionRun($batch = 100)
    {
        /** @var Gifter $gifter */
        $gifter = \Yii::$app->get('gifter');

        foreach (Deliver::find()->where(['status' => Deliver::STATUS_NEW])->batch($batch) as $delivers) {
            /** @var Deliver $deliver */
            foreach ($delivers as $deliver) {
                try {
                    $gifter->deliver($deliver);
                } catch (DeliveryException $e) {
                    \Yii::error('Delivery error: ' . $e->getMessage());
                } catch (InvalidConfigException $e) {
                    \Yii::error('Delivery error: ' . $e->getMessage());
                }
            }
        }
    }
}
