<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\GearService;
use kartik\widgets\DatePicker;

/* @var $this yii\web\View */
/* @var $model common\models\GearService */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gear-service-form">

    <?php $form = ActiveForm::begin(); ?>
    <?php if ($model->gearItem->gear->no_items){ ?>
    <h3><?php echo Html::a($model->gearItem->gear->name, ['/gear/view', 'id'=>$model->gearItem->gear_id]); ?></h3>
    <?php }else{ ?>
    <h3><?php echo Html::a($model->gearItem->name, ['/gear-item/view', 'id'=>$model->gear_item_id]); ?> [<?php echo $model->gearItem->gear->name; ?>]</h3>
    <?php } ?>
    <?php                 
       echo $form->field($model, 'warehouse_to')->widget(\kartik\widgets\Select2::className(), [
                'data' => yii\helpers\ArrayHelper::map(\common\models\Warehouse::find()->asArray()->all(), 'id' ,'name'),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz magazyn...')

                ],
                'pluginOptions' => [
                    'allowClear' => false,
                    'multiple' => false,
                ],
            ])->label(Yii::t('app', 'Przesunięcie magazynowe')); ?>
    <?= $form->field($model, 'priority')->dropDownList(GearService::getPriorityList()) ?>
    <?= $form->field($model, 'description')->widget(\common\widgets\RedactorField::className()); ?>


    <?= $form->field($model, 'status')->dropDownList(GearService::getStatusList()) ?>


    <?= $form->field($model, 'info')->textarea(['rows' => 6]) ?>

    <label class="control-label"><?= Yii::t('app', 'Deadline') ?></label>
    <?php
            echo DatePicker::widget([
                'model' => $model,
                'attribute' => 'deadline',
                'options' => ['placeholder' => Yii::t('app', 'Wybierz...')],
                'pluginOptions' => [
                    'format' => 'dd/mm/yyyy',
                    'todayHighlight' => true,
                    'autoclose' => true,
                ],
            ]);

            ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
