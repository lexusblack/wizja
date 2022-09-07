<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\OfferDraft */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="offer-draft-form">

    <?php $form = ActiveForm::begin(["id"=>"offer-draft-form"]); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Nazwa')]) ?>

    <?php if (\common\models\Firm::getList()){
                echo $form->field($model, 'firm_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\Firm::getList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Firma domyślna'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
            } ?>
<div class="row">
<div class="col-lg-6">
    <?php echo $form->field($model, 'gear_section')->dropDownList([1=>Yii::t('app', "Pokazuj poszczególne pozycje"), 2=>Yii::t('app', "Tylko podsumowanie")]) ?>

    <?php 
                echo $form->field($model, 'gear_fields')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\OfferDraft::getGearFieldList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Pola widoczne na pdf'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
            ]);
     ?>
     <?php echo $form->field($model, 'crew_section')->dropDownList([1=>Yii::t('app', "Pokazuj poszczególne pozycje"), 2=>Yii::t('app', "Tylko podsumowanie")]) ?>
     <?php 
                echo $form->field($model, 'crew_fields')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\OfferDraft::getCrewFieldList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Pola widoczne na pdf'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
            ]);
     ?>
     <?php echo $form->field($model, 'transport_section')->dropDownList([1=>Yii::t('app', "Pokazuj poszczególne pozycje"), 2=>Yii::t('app', "Tylko podsumowanie")]) ?>
     <?php 
                echo $form->field($model, 'transport_fields')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\OfferDraft::getTransportFieldList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Pola widoczne na pdf'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
            ]);
     ?>
     <?php echo $form->field($model, 'other_section')->dropDownList([1=>Yii::t('app', "Pokazuj poszczególne pozycje"), 2=>Yii::t('app', "Tylko podsumowanie")]) ?>
     <?php 
                echo $form->field($model, 'other_fields')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\OfferDraft::getOtherFieldList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Pola widoczne na pdf'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
            ]);
     ?>



     <?php echo $form->field($model, 'footer_section')->dropDownList([1=>Yii::t('app', "Pokazuj w pdf na każdej stronie"), 2=>Yii::t('app', "Nie pokazuj w pdf"), 3=>Yii::t('app', 'Pokazuj w PDF tylko na ostatniej stronie')]) ?>
     <?php 
                echo $form->field($model, 'footer_fields')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\OfferDraft::getFooterFieldList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Pola widoczne na pdf'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
            ]);
     ?>
     <?php echo $form->field($model, 'header_section')->dropDownList([1=>Yii::t('app', "Pokazuj w pdf na każdej stronie"), 2=>Yii::t('app', "Nie pokazuj w pdf"), 3=>Yii::t('app', 'Pokazuj w PDF tylko na pierwszej stronie')]) ?>
     <?php 
                echo $form->field($model, 'header_fields')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\OfferDraft::getHeaderFieldList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Pola widoczne na pdf'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
            ]);
     ?>
     <?php 
                echo $form->field($model, 'title_fields')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\OfferDraft::getTitleFieldList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Pola widoczne na pdf'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
            ]);
     ?>
     </div>
<div class="col-lg-6">
<div class="row" id="preview-offer-draft">
</div>
</div>
</div>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

$this->registerJs('


$("#'.Html::getInputId($model, 'gear_section').'").change(function(e){
    type = $(this).val();
    if (type==1)
    {
         $(".field-'.Html::getInputId($model, 'gear_fields').'").show();
    }else{
        $(".field-'.Html::getInputId($model, 'gear_fields').'").hide();
    }
    });
$("#'.Html::getInputId($model, 'crew_section').'").change(function(e){
    type = $(this).val();
    if (type==1)
    {
         $(".field-'.Html::getInputId($model, 'crew_fields').'").show();
    }else{
        $(".field-'.Html::getInputId($model, 'crew_fields').'").hide();
    }
    });

$("#'.Html::getInputId($model, 'transport_section').'").change(function(e){
    type = $(this).val();
    if (type==1)
    {
         $(".field-'.Html::getInputId($model, 'transport_fields').'").show();
    }else{
        $(".field-'.Html::getInputId($model, 'transport_fields').'").hide();
    }
    });

$("#'.Html::getInputId($model, 'other_section').'").change(function(e){
    type = $(this).val();
    if (type==1)
    {
         $(".field-'.Html::getInputId($model, 'other_fields').'").show();
    }else{
        $(".field-'.Html::getInputId($model, 'other_fields').'").hide();
    }
    });

$("#'.Html::getInputId($model, 'footer_section').'").change(function(e){
    type = $(this).val();
    if (type==1)
    {
         $(".field-'.Html::getInputId($model, 'footer_fields').'").show();
    }else{
        $(".field-'.Html::getInputId($model, 'footer_fields').'").hide();
    }
    });

$("#'.Html::getInputId($model, 'header_section').'").change(function(e){
    type = $(this).val();
    if (type==1)
    {
         $(".field-'.Html::getInputId($model, 'header_fields').'").show();
    }else{
        $(".field-'.Html::getInputId($model, 'header_fields').'").hide();
    }
    
    });

$(".form-control").change(function(e){
reloadPreview();
});

    ');
?>
<script type="text/javascript">
function reloadPreview(){
    var $form = $("#offer-draft-form");
    data = $form.serialize();
    $.post("/admin/offer-draft/preview", data, function(sucess){
                    $("#preview-offer-draft").html(sucess);
                });
    }
</script>

