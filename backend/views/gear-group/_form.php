<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\GearGroup */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gear-group-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'weight')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'width')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'height')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'depth')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'volume')->textInput() ?>
            <?= $form->field($model, 'warehouse')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'location')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'rfid_code')->textInput() ?>
            <?= $form->field($model, 'description')->widget(\yii\redactor\widgets\Redactor::className(), [
                'clientOptions' => [
                    'buttons' => ['html','formatting', 'bold', 'italic', 'deleted',
                        'unorderedlist', 'orderedlist','outdent', 'indent', 'alignment', 'link', 'horizontalrule'],
                ]
            ]);?>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
        </div>

        <div class="col-md-6">
            <h3><?= Yii::t('app', 'Egzemplarze') ?></h3>
            <div class="panel_mid_blocks">
                <div class="panel_block">
            <?= GridView::widget([
                'dataProvider' => new \yii\data\ActiveDataProvider([
                    'query'=>$model->getGearItems(),
                    'pagination' => false,
                    'sort'=>false,
                ]),
                'tableOptions' => [
                    'class' => 'kv-grid-table table table-condensed kv-table-wrap'
                 ],
                'filterModel' => null,
                'columns' => [
                    'name',
                    'number',
                    'code',
                    'serial',
                ],
            ]); ?>
                </div>
            </div>
        </div>
    </div>

</div>

<?php

$this->registerJs('


$(".table-bordered").each(function(){
    $(this).removeClass("table-bordered");
});
$(".table-striped").each(function(){
    $(this).removeClass("table-striped");
});

$("#'.Html::getInputId($model, 'height').'").change(function(e){
    volume = countVolume();
    $("#'.Html::getInputId($model, 'volume').'").val(volume);
});
$("#'.Html::getInputId($model, 'width').'").change(function(e){
    volume = countVolume();
    $("#'.Html::getInputId($model, 'volume').'").val(volume);
});
$("#'.Html::getInputId($model, 'depth').'").change(function(e){
    volume = countVolume();
    $("#'.Html::getInputId($model, 'volume').'").val(volume);
});

function countVolume()
{
    height = $("#'.Html::getInputId($model, 'height').'").val();
    width = $("#'.Html::getInputId($model, 'width').'").val();
    depth = $("#'.Html::getInputId($model, 'depth').'").val();
    return height*width*depth/1000000;
}
');