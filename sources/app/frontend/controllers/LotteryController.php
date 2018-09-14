<?php

namespace frontend\controllers;

use common\gifter\Gifter;
use common\models\Prize;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Lottery controller
 */
class LotteryController extends Controller
{
    /** @var Gifter */
    protected $gifter;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * @param $action
     * @return bool
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if (!$result = parent::beforeAction($action)) {
            return $result;
        }
        $this->gifter = Yii::$app->get('gifter');

        return $result;
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     * @throws \common\exception\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionTry()
    {
        if (!Yii::$app->getRequest()->getIsPost()) {
            throw new NotFoundHttpException('Page not found');
        }

        return $this->renderAjax('detail', [
            'prize' => $prize = $this->gifter->reserve(Yii::$app->user->identity),
            'form' => $this->gifter->getForm($prize),
        ]);
    }

    /**
     * @return string
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidArgumentException
     */
    public function actionAccept()
    {
        if (!$id = Yii::$app->getRequest()->post('id')) {
            throw new NotFoundHttpException('Page not found');
        }
        if (!$prize = Prize::findOne($id)) {
            throw new NotFoundHttpException('Prize not found');
        }
        if ($prize->user_id !== Yii::$app->user->id) {
            throw new ForbiddenHttpException();
        }
        $delivery = Yii::$app->getRequest()->post('delivery');
        if (!class_exists($delivery)) {
            throw new NotFoundHttpException('Delivery not found');
        }

        $this->gifter->accept($prize, new $delivery());

        return $this->renderAjax('accepted', [
            'prize' => $prize,
        ]);
    }

    /**
     * @return string
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidArgumentException
     * @throws \yii\db\Exception
     */
    public function actionDecline()
    {
        if (!$id = Yii::$app->getRequest()->post('id')) {
            throw new NotFoundHttpException('Page not found');
        }
        if (!$prize = Prize::findOne($id)) {
            throw new NotFoundHttpException('Prize not found');
        }
        if ($prize->user_id !== Yii::$app->user->id) {
            throw new ForbiddenHttpException();
        }

        $this->gifter->decline($prize);

        return $this->renderAjax('declined', [
            'prize' => $prize,
        ]);
    }

}
