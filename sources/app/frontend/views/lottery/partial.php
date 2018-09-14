<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */
/** @var \common\models\User $user */

$this->registerJsFile(
    '@web/js/lottery.js',
    ['depends' => [\yii\web\JqueryAsset::class]]
);
?>

<div class="container">
    <div class="row">
        <div class="text-center">
            <button class="btn btn-success btn-lg get-prize">Get prize!</button>
        </div>
    </div>

    <div class="row result">

    </div>
</div>
