<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Gear */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="event-outer-gear-form">

    <?php $form = ActiveForm::begin(['id'=>'EventOuterForm']); ?>

    <div class="row">
        <div class="col-md-12">

    <?= $form->field($model, 'outer_gear_model_id')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map(\common\models\OuterGearModel::find()->where(['active'=>1])->orderBy('name')->asArray()->all(), 'id', 'name'),
        'options' => ['placeholder' => Yii::t('app', 'Wybierz')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ])->label(Yii::t('app', 'Sprzęt/Mat. eksploatacyjny')); ?>

            <?= $form->field($model, 'quantity')->textInput() ?> 
        </div>
    </div>





    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

$this->registerJs("
    $('#EventOuterForm').on('beforeSubmit', function(e) {
    var form = $(this);
    var formData = form.serialize();
    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: formData,
        success: function (data) {
            $('#add-purchase').find('.modalContent').empty();
            $('#add-purchase').modal('hide');
        },
        error: function () {
            alert('Something went wrong');
        }
    });
}).on('submit', function(e){
    e.preventDefault();
});");

?>





