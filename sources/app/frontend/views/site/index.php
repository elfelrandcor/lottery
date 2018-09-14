<?php

/* @var $this yii\web\View */

$this->title = 'Unique lottery';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Unique lottery!</h1>

        <p class="lead">Push the button</p>

        <?php
            if (Yii::$app->user->isGuest) {
        ?>
                <p>
                    <a class="btn btn-lg btn-success" href="<?=\yii\helpers\Url::to('/site/signup')?>">SignUp</a>
                    <a class="btn btn-lg btn-success" href="<?=\yii\helpers\Url::to('/site/login')?>">Login</a>
                </p>
        <?php
            }
        ?>

    </div>

    <div class="body-content">
        <?=Yii::$app->user->isGuest ? '' : $this->render('/lottery/partial');?>
    </div>
</div>
