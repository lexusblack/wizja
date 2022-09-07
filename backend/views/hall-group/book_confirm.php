<?php

use yii\bootstrap\Modal;
use common\components\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>

<p><?=Yii::t('app', 'Próbujesz zarezerwować powierzchnię ').$hall->name?></p>
<?php if (count($events)){
    ?>
    <?=Yii::t('app', 'W tym czasie jest ona zarezerowana lub częściowo zarezerwowana na następujące wydarzenia:')?><br/>
<?php foreach ($events as $e)
{
    echo "<i class='fa fa-circle' style='color:".$e->statut->color."'></i>".$e->event->name." ".substr($e->start_time, 0, 16)." - ".substr($e->end_time, 0, 16);
} ?>
</p>
<?php }else{ ?>

<?php } ?>


<div class="hall-group-cost-form">

    <?php $form = ActiveForm::begin(['id'=>'eventhallgroupform']); ?>
    <?= $form->field($model, 'end_time', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>
    <?= $form->field($model, 'start_time', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>
    <?= $form->field($model, 'hall_group_id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>
    <?= $form->field($model, 'event_id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->errorSummary($model); ?>

            <?php echo $form->field($model, 'statut_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\helpers\ArrayHelper::map(\common\models\HallGroupStatut::find()->orderBy(['position'=>SORT_ASC])->asArray()->all(), 'id', 'name'),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => false,
                    'multiple' => false,
                ],
            ]);
            ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs('
    $("#eventhallgroupform").on("beforeSubmit", function(e){
            e.preventDefault();
    var form = $(this);
    var formData = form.serialize();
    $.ajax({
        url: form.attr("action"),
        type: form.attr("method"),
        data: formData,
        success: function (data) {
                    var modal = $("#hall_modal");
                    modal.find(".modalContent").empty();
                    modal.modal("hide");
                toastr.success("Zapisano!");
        },
        error: function () {
        }
    })
    }).on("submit", function(e){
    e.preventDefault();
});');

