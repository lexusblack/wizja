<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\CustomerNote */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="customer-note-form">

    <?php $form = ActiveForm::begin(['id'=>'CustomerNoteForm']); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'name')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'datetime')->widget(\kartik\datecontrol\DateControl::classname(), [
        'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
        'saveFormat' => 'php:Y-m-d H:i:s',
        'ajaxConversion' => true,
        'options' => [
            'pluginOptions' => [
                'placeholder' => 'Choose Datetime',
                'autoclose' => true,
            ]
        ],
    ]); ?>

    <?= $form->field($model, 'type')->textInput(['maxlength' => true, 'placeholder' => 'Typ notatki']) ?>

    <?php if (!$model->customer_id){ ?>
    <?= $form->field($model, 'customer_id')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map(\common\models\Customer::find()->where(['id'=>$model->customer_id])->orderBy('id')->asArray()->all(), 'id', 'name'),
        'options' => ['placeholder' => Yii::t('app', 'Wybierz klienta')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>
    <?php } ?>
    <?php if (!$model->contact_id){ ?>
    <?= $form->field($model, 'contact_id')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map(\common\models\Contact::find()->where(['customer_id'=>$model->customer_id])->orderBy('id')->asArray()->all(), 'id', 'last_name'),
        'options' => ['placeholder' => Yii::t('app', 'Wybierz kontakt')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>
    <?php } ?>
    <?php if (!$model->offer_id){ ?>
    <?php if (!isset($event_id)){ ?>
    <?= $form->field($model, 'event_id')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map(\common\models\Event::find()->where(['customer_id'=>$model->customer_id])->orderBy('id')->asArray()->all(), 'id', 'name'),
        'options' => ['placeholder' => Yii::t('app', 'Wybierz event')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?= $form->field($model, 'rent_id')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map(\common\models\Rent::find()->where(['customer_id'=>$model->customer_id])->orderBy('id')->asArray()->all(), 'id', 'name'),
        'options' => ['placeholder' => Yii::t('app', 'Wybierz wypożyczenie')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>
    <?= $form->field($model, 'meeting_id')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map(\common\models\Meeting::find()->where(['customer_id'=>$model->customer_id])->orderBy('id')->asArray()->all(), 'id', 'name'),
        'options' => ['placeholder' => Yii::t('app', 'Wybierz spotkanie')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>
    <?php }else{ ?>
    <?php echo $form->field($model, 'permissions')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\AuthItem::getList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
            ])->label(Yii::t('app', 'Ogranicz widoczność tej notatki'));
            ?>
    <?php } ?>
    <?php } ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Dodaj') : Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<?php
if ($ajax){
$this->registerJs("
$('#CustomerNoteForm').on('beforeSubmit', function(e) {
    var form = $(this);
    var formData = form.serialize();
    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: formData,
        success: function (data) {
            $('#offer-notes').modal('hide');
            location.reload();
        },
        error: function () {
            alert('Something went wrong');
        }
    });
}).on('submit', function(e){
    e.preventDefault();
});");
}
?>