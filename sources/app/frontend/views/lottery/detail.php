<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */
/** @var \common\models\Prize $prize */
/** @var \common\gifter\PrizeFormInterface $form */
?>


<div class="col-xs-12 text-center">
    <div class="alert alert-success ">
        You won <strong><?=$prize->description()?></strong>
    </div>

</div>
<div class="col-xs-6 text-center">
    <?php
        foreach ($form->getDeliveries() as $delivery) {
            ?>
            <button class="btn btn-success btn-lg accept-prize"
                    onclick="window.accept(<?=$prize->id?>, '<?=$delivery->name()?>')">Accept(<?=$delivery->description()?>)
            </button>
            <?php
        }
    ?>
</div>
<div class="col-xs-6 text-center">
    <button class="btn btn-danger btn-lg decline-prize" onclick="window.decline(<?=$prize->id?>)">Decline</button>
</div>



