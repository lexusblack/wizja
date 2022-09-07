<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Gear */
/* @var $form yii\widgets\ActiveForm */
$offerGearUrl = Url::to(['offer/default/manage-gear', 'offer_id'=>$offer->id]);
?>

<div class="gear-form">

<?php

    $form = ActiveForm::begin([
        'options' => [
            'class'=>'gear-assignment-form',
            'data' => [
                'action' => $offerGearUrl,
            ]
        ],
        'action' =>['assign-gear', 'id'=>$offer->id],
        'type'=>ActiveForm::TYPE_INLINE,
        'formConfig' => [
            'showErrors' => true,
        ],

    ]);
    echo Html::activeHiddenInput($model, 'id');
    echo Html::activeHiddenInput($model, 'gear_id');
    echo Html::activeHiddenInput($model, 'offer_id');?>
    <?php if ($type2=='gear'){
        $model->offer_gear_id = $item;
        echo Html::activeHiddenInput($model, 'offer_gear_id');
        }?>
    <?php if ($type2=='outerGear'){
        $model->offer_outer_gear_id = $item;
        echo Html::activeHiddenInput($model, 'offer_outer_gear_id');
        }?>
    <?php if ($type2=='extraGear'){
        $model->offer_group_id = $item;
        echo Html::activeHiddenInput($model, 'offer_group_id');
        }?>
    <?php if ($type2){
        $model->type = 2;
        echo Html::activeHiddenInput($model, 'type');
        }?>
    <?= $form->field($model, 'quantity')->textInput(['class'=>'gear-quantity-field col-xs-2'])
    ?>
    <?php ActiveForm::end();
?>

</div>

<?php
$u = Url::to(['/offer/default/assign-gear', 'id' => $offer->id]);
$this->registerJs('
$(".gear-assignment-form").on("submit", function(e){
    e.preventDefault();
    var form = $(this); //.closest("form");
    
    var quantity = form.find(".gear-quantity-field").val();
    var data = form.serialize();
    $.post("'.$offerGearUrl.'", data, function(response){
         
        var error = "";
        if (response.success==0)
        {
            var error = [response.error];
            toastr.error(error);
        }
        else
        {
            toastr.success("'.Yii::t('app', 'Sprzęt dodany do oferty').'");
                if (response.connected.length)
                {
                    showConnectedModal(response.connected);
                }
        }        
    });
    return false;
});
');



