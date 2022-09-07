<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\GearItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gear-item-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?php echo $form->field($model, 'gear_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\helpers\ArrayHelper::map( \common\models\Gear::find()->where(['active'=>1])->orderBy(['name'=>SORT_ASC])->asArray()->all(), 'id', 'name') ,
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                    'id' => 'model-name',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
                'pluginEvents' => [
                    "change" => "function() {
                        $('#item-name').val($('#model-name option[value=\"'+$('#model-name').val()+'\"]').html());
                        $.get('/admin/gear/count?id='+$(this).val(), function(data){
                            $('#item-number').val(data);
                        });
                        
                    }",
                ],
            ]); ?>

            <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'id'=>"item-name"]) ?>

            <?= $form->field($model, 'number')->textInput(['maxlength' => true, 'id'=>'item-number']) ?>

            <?= $form->field($model, 'serial')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'warehouse')->textInput(['maxlength' => true]) ?>

            <?php $warehouses = \common\models\Warehouse::getList();

            if (count($warehouses)>1){ ?>

            <?php  echo $form->field($model, 'warehouse_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\Warehouse::getList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ])->label(Yii::t('app', 'Wybierz magazyn')); ?>

            <?php }else{
                $w = \common\models\Warehouse::find()->where(['type'=>1])->one();
                $model->warehouse_id = $w->id;

                 echo $form->field($model, 'warehouse_id')->hiddenInput(['maxlength' => true])->label(false);

             } ?>

            <?= $form->field($model, 'location')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'description')->widget(\yii\redactor\widgets\Redactor::className(), [
                'clientOptions' => [
                    'buttons' => ['html','formatting', 'bold', 'italic', 'deleted',
                        'unorderedlist', 'orderedlist','outdent', 'indent', 'alignment', 'link', 'horizontalrule'],
                ]
            ]);?>

            <?php if ($model->type == \common\models\GearItem::TYPE_NORMAL) { echo $form->field($model, 'rfid_code')->textInput(); } ?>

            <div class="form-group">
                <?php echo Html::activeHiddenInput($model, 'photo'); ?>
                <?php echo Html::activeLabel($model, 'photo'); ?>
                <?php echo devgroup\dropzone\DropZone::widget([
                    'url'=>\common\helpers\Url::to(['upload']),
                    'name'=>'file',
                    'options'=>[
                        'maxFiles' => 1,
                    ],
                    'eventHandlers' => [
                        'success' => 'function(file, response) {
               $("#gearitem-photo").val(response.filename);

            }',
                    ]
                ]); ?>
                <?php echo Html::error($model, 'photo'); ?>
            </div>
        </div>
        <div class="col-md-6">

            <?php echo $form->field($model, 'one_in_case')->checkbox(); ?>
            <?php if ($model->one_in_case) { $style= ""; }else{ $style ='style="display:none"'; }?>
            <div id="caseSize" <?=$style?>>
            <?= $form->field($model, 'weight_case')->textInput() ?>

            <?= $form->field($model, 'height_case')->textInput() ?>

            <?= $form->field($model, 'depth_case')->textInput() ?>

            <?= $form->field($model, 'width_case')->textInput() ?>

            <?= $form->field($model, 'volume')->textInput() ?>
            </div>



            <?= $form->field($model, 'purchase_price')->textInput(['maxlength' => true]) ?>


            <?php  echo $form->field($model, 'group_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\helpers\ArrayHelper::map(\common\models\GearGroup::find()->where(['active'=>1])->asArray()->all(), 'id', 'name'),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]); ?>
        </div>
    </div>






    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$this->registerJs('
$("#'.Html::getInputId($model, 'one_in_case').':checkbox").change(function(e){
    var checked = $(e.target).prop("checked");
    var t = $("#caseSize");
    if (checked == true)
    {
        t.show();
    }
    else 
    {
        t.hide();
    }
}).trigger("change");

$("#'.Html::getInputId($model, 'height_case').'").change(function(e){
    volume = countVolume();
    $("#'.Html::getInputId($model, 'volume').'").val(volume);
});
$("#'.Html::getInputId($model, 'width_case').'").change(function(e){
    volume = countVolume();
    $("#'.Html::getInputId($model, 'volume').'").val(volume);
});
$("#'.Html::getInputId($model, 'depth_case').'").change(function(e){
    volume = countVolume();
    $("#'.Html::getInputId($model, 'volume').'").val(volume);
});

function countVolume()
{
    height = $("#'.Html::getInputId($model, 'height_case').'").val();
    width = $("#'.Html::getInputId($model, 'width_case').'").val();
    depth = $("#'.Html::getInputId($model, 'depth_case').'").val();
    return height*width*depth/1000000;
}
');