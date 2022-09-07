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
    <?php if ($model->gearItem->gear->no_items){ ?>
                <?php $warehouses = \common\models\Warehouse::getList();

            if (count($warehouses)>1){ ?>

            <?php  echo $form->field($model, 'warehouse_from')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\Warehouse::getList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ])->label(Yii::t('app', 'Magazyn, z którego sprzęt idzie na serwis')); ?>

            <?php }else{
                $w = \common\models\Warehouse::find()->where(['type'=>1])->one();
                $model->warehouse_from = $w->id;

                 echo $form->field($model, 'warehouse_from')->hiddenInput(['maxlength' => true])->label(false);

             } ?>

    <?= $form->field($model, 'quantity')->textInput(['maxlength' => true]) ?> 
    <?php }else{ 
        $model->warehouse_from = $model->gearItem->warehouse_id;
            echo $form->field($model, 'warehouse_from')->hiddenInput(['maxlength' => true, 'autocomplete'=>"off"])->label(false);
            echo "<p>Magazyn, z którego sprzęt idzie na serwis: ".yii\helpers\ArrayHelper::map(\common\models\Warehouse::find()->asArray()->all(), 'id' ,'name')[$model->gearItem->warehouse_id]."</p>";
    } ?>
    <?php  
       echo $form->field($model, 'warehouse_to')->widget(\kartik\widgets\Select2::className(), [
                'data' => yii\helpers\ArrayHelper::map(\common\models\Warehouse::find()->where(['type'=>2])->asArray()->all(), 'id' ,'name'),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz magazyn...')

                ],
                'pluginOptions' => [
                    'allowClear' => false,
                    'multiple' => false,
                ],
            ])->label(Yii::t('app', 'Magazyn serwisowy')); ?>
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
