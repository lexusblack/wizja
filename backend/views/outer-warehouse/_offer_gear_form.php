<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Gear */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gear-form">

<?php

    $form = ActiveForm::begin([
        'options' => [
            'class'=>'gear-assignment-form',
        ],
        'action' =>['assign-gear', 'id'=>$offer->id],
        'type'=>ActiveForm::TYPE_INLINE,
        'formConfig' => [
            'showErrors' => true,
        ]
    ]);

    echo Html::activeHiddenInput($model, 'gear_id');
    echo Html::activeHiddenInput($model, 'offer_id');?>

    <?= $form->field($model, 'quantity')->textInput(['class'=>'gear-quantity-field col-xs-2'])->hint( Yii::t('app', '"Enter", żeby zapisać.'));?>

    <?php ActiveForm::end();
?>

</div>

<?php
$offerGearUrl = Url::to(['offer/default/manage-gear', 'offer_id'=>$offer->id]);
$this->registerJs('
$(".gear-assignment-form").on("submit", function(e){
    e.preventDefault();
    var form = $(this); //.closest("form");
    
    var quantity = form.find(".gear-quantity-field").val();
    quantity = parseInt(quantity) || 0;
    console.log(quantity);
    
    var data = form.serialize();
    console.log(data);
    $.post("'.$offerGearUrl.'", data, function(response){
         
        var error = "";
        if (response.success==0)
        {
            var error = [response.error];
        }
        else
        {
            var container = $("[data-pjax-container]");
            $.pjax.reload("#" + container.attr("id"), {
                push: false,
                replace: true,
            });
        }
        form.yiiActiveForm("updateAttribute", "offergear-quantity", error);
        
    });
    return false;
});

$(".submit-all").on("click", function(e){
    e.preventDefault();
    console.log("click");
    $(".gear-assignment-form").trigger("submit");
    return false;
});

');



