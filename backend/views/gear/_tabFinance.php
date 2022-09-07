<?php
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
$user = Yii::$app->user;
/* @var $model \common\models\Gear; */
?>
<div class="panel-body">
<?php if ($user->can('gearWarehousePrices')){ ?>
<h3><?php echo Yii::t('app', 'Stawki'); ?></h3>
<div class="row">
    <div class="col-md-12">
        <div class="ibox">
        <?php
            echo Html::a(Yii::t('app', 'Dodaj'), ['gears-price/create', 'gear_id' => $model->id], ['class' => 'btn btn-success']);
        ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
<?php $form = ActiveForm::begin(['id'=>'price-form']); ?>
<table class="table">
<tr><td><?=Yii::t('app', 'Nazwa stawki')?></td><td><?=Yii::t('app', 'Cena')?></td><td><?=Yii::t('app', 'Koszt')?></td><td><?=Yii::t('app', 'Nazwa kosztu')?></td><td><?=Yii::t('app', 'Mnóż koszt przez przelicznik/liczbę dni na evencie')?></td></tr>
        <?php 

        foreach ($groups as $group){

        ?>
        <tr><td><?=$group->name." (".$group->currency.")"?> </td>
        <?php 
            $baseIndex = 'prices['.$model->id.']['.$group->id.']';
        ?>  
        <td><?php echo $form->field($priceForm, $baseIndex.'[price]')->textInput(['class'=>'price-input'])->label(false); ?></td>
        <?php 
        ?>  
        <td><?php echo $form->field($priceForm, $baseIndex.'[cost]')->textInput(['class'=>'price-input'])->label(false); ?></td>   
                <?php 
        ?>  
        <td><?php echo $form->field($priceForm, $baseIndex.'[cost_name]')->textInput(['class'=>'price-input'])->label(false); ?></td>      
        <td><?php echo $form->field($priceForm, $baseIndex.'[one_per_event]')->checkbox(['label'=>false])->label(false); ?></td>  
        </tr>
        <?php } ?>
    </table>
<div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
<?php ActiveForm::end(); ?>
    </div>
</div>
<?php } ?>
</div>