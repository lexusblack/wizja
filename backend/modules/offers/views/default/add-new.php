<?php

use backend\modules\offers\models\OfferExtraItem;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\offers\models\OfferExtraItem */
/* @var $form yii\widgets\ActiveForm */

?>
    
    <div class="row">

        <?php $form = ActiveForm::begin(); ?>
        <div class="col-md-2 small-padding"  style="width:100px">
        <?php echo $form->field($model, 'type')->dropDownList(['gear'=>Yii::t('app', "Sprzęt z magazynu"), 'gear'=>Yii::t('app', "Sprzęt zewnętrzny"), 'extraItem'=>Yii::t('app', "Grupa sprzętu")]) ?>
        </div>
        <?= $form->field($model, 'offer_id')->hiddenInput()->label(false) ?>
        <div class="col-md-2 small-padding"  style="width:150px">
        <?php if ($type=='gear'){
            echo     $form->field($model, 'gear_id')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map(\common\models\Gear::find()->where(['active'=>1])->orderBy('id')->asArray()->all(), 'id', 'name'),
        'options' => ['placeholder' => Yii::t('app', 'Wybierz sprzęt'), 'id'=>'offergear-gear-id-'.mktime(),],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ])->label(false);
            }?>
        </div>
        <div class="col-md-2 small-padding"  style="width:100px">
        <?= $form->field($model, 'description')->textInput(['placeholder' => Yii::t('app', 'Opis')])->label(false) ?>
        </div>
        <div class="col-md-1 small-padding" style="width:100px">
        <?= $form->field($model, 'price', ['labelOptions' => ['id' => 'label_price']])->textInput(['placeholder' => Yii::t('app', 'Cena')])->label(false) ?>
        </div>
        <div class="col-md-1 small-padding" style="width:100px">
        <?= $form->field($model, 'quantity')->textInput(['placeholder' => Yii::t('app', 'Liczba')])->label(false) ?>
        </div>
        <div class="col-md-1 small-padding" style="width:100px">
        <?= $form->field($model, 'discount', ['labelOptions' => ['id' => 'label_discount']])->textInput(['placeholder' => Yii::t('app', 'Rabat')])->label(false) ?>
        </div>
        <div class="col-md-1 small-padding" style="width:100px">
        <?= $form->field($model, 'duration', ['labelOptions' => ['id' => 'label_duration']])->textInput(['placeholder' => Yii::t('app', 'Liczba dni')])->label(false) ?>
        </div>
        <div class="col-md-1 small-padding" style="width:100px">
        <?= $form->field($model, 'first_day_percent', [])->textInput(['placeholder' => Yii::t('app', '% dnia pierwszego')])->label(false) ?>
        </div>
        <div class="col-md-1 small-padding price" style="width:100px">
            
        </div>
        <div class="col-md-1 small-padding" style="width:50px">
            <?= Html::submitButton('<i class="fa fa-save"></i>', ['class' => 'btn btn-sm btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
        
    </div>


<?php 
$this->registerCss(
    '
    .small-padding{
        padding:3px;
    }
    ');
