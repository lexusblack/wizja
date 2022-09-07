<?php
use kartik\form\ActiveForm;
use kartik\helpers\Html;
use yii\helpers\Url;
use kartik\select2\Select2;

?>

<div class="offer-extra-item-form">

        <?php $form = ActiveForm::begin(['id' => 'offer-statut-form']); ?>
        <?php
        echo $form->field($model, 'status')->widget(Select2::classname(), [
                    'data' => \common\models\Offer::getStatusList(),
                    'options' => [
                    'placeholder' => Yii::t('app', 'Status oferty'),
                    ],
                    'language' => 'pl',
                    'pluginOptions' => [
                        'allowClear' => false,
                        'multiple' => false,
                    ],
            
            ])->label(Yii::t('app', 'Status oferty'));
            ?>
         <div class="form-group">
            <?= Html::submitButton('Zapisz', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

<script type="text/javascript">
    function changeOfferStatus(status)
    {
        $("#offer-status-edit").modal("hide");
        $.ajax({
            url: '<?=Url::to(['/offer/default/change-status'])?>?id=<?=$model->id?>'+'&status='+status,
                    success: function(response){
                        $("#offer-status-edit").modal("hide");
                        loadTabsAfterChange();
                                  }
            });
    }
</script>

<?php

$this->registerJs('
    $("#offer-statut-form").submit(function(e){
        e.preventDefault();
        changeOfferStatus($("#offer-status").val());
    });
    ');