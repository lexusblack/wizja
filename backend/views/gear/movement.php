<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Gear */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gear-form">

    <?php $form = ActiveForm::begin(['id'=>'movement-final-form']); ?>

    <div class="row">
        <div class="col-md-12">

            <?php  

                echo "<p>Magazyn, z którego usuwamy sprzęt: ".yii\helpers\ArrayHelper::map(\common\models\Warehouse::find()->asArray()->all(), 'id' ,'name')[$w]."</p>";

                    echo $form->field($model, 'warehouse_to')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\Warehouse::getList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz magazyn...'),

                ],
                'pluginOptions' => [
                    'allowClear' => false,
                    'multiple' => false,
                ],
            ])->label(Yii::t('app', 'Magazyn, do którego dodajemy sprzęt'));
            
            ?>

            <?= $form->field($model, 'info')->widget(\yii\redactor\widgets\Redactor::className(), [
                'clientOptions' => [
                    'buttons' => ['html','formatting', 'bold', 'italic', 'deleted',
                        'unorderedlist', 'orderedlist','outdent', 'indent', 'alignment', 'link', 'horizontalrule'],
                ]
            ])->label(Yii::t('app', 'Przyczyna/Komentarz'));?>

        </div>
        
    </div>





    <div class="form-group">
        <?= Html::a(Yii::t('app', 'Zapisz'),'#', ['class' =>  'btn btn-success save-movement-final']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<div>
<h3>Sprzęt do przeniesienia</h3>
<table class="table">
<?php foreach ($gears as $gear){
    if ($gear){
?>
<tr><td><?=$gear['gear']->name?></td><td><?=$gear['quantity']." ".Yii::t('app', 'szt.')?></td><td><?php if (!$gear['gear']->no_items){ echo "["; foreach ($gear['items'] as $item){ echo $item->number.",";} echo "]";} ?></td><td><?=Html::a('Usuń', ['gear/delete-from-movement', 'gear_id'=>$gear['gear']->id, 'warehouse_id'=>$w], ['class'=>'btn btn-danger btn-xs delete-from-movement'])?></td></tr>
<?php
    } }?>
</table>
</div>
<?php
$moveUrl = Url::to(['gear/movement', 'w'=>$w]);

$this->registerJS('
$(".save-movement-final").click(function(e){
    e.preventDefault();
    var data = $("#movement-final-form").serialize();
    $.post("'.$moveUrl.'", data, function(response){
                        var modal = $("#gear-movement");
                        modal.find(".modalContent").empty();
                        modal.find(".modalContent").append(response);
                    
                });
});
$(".delete-from-movement").click(function(e){
    e.preventDefault();
    var but = $(this);
    $.post($(this).attr("href"), [], function(response){
                        
                    but.parent().parent().remove();
                    toastr.error("Sprzęt usunięty z przeniesienia");
                        $(".gear-movement-label").html(response.total);
                });
});
    ');