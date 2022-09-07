<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\PriceGroup */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stawki obsługi'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="price-group-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= Html::encode($this->title) ?></h2>
        </div>
        <div class="col-sm-3" style="margin-top: 15px">
            
            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ])
            ?>
        </div>
    </div>

    <div class="row">
<?php 
    $gridColumn = [
        ['attribute' => 'id', 'visible' => false],
        'name',
        'unit',
        'currency',
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]);
?>
    </div>
</div>
<?php $form = ActiveForm::begin(); ?>
<table class="table">
<tr><th><?=Yii::t('app', 'Rola')?></th><th><?=Yii::t('app', 'Cena')?></th><th><?=Yii::t('app', 'Koszt')?></th><th><?=Yii::t('app', 'Koszt godzinowy')?></th><th><?=Yii::t('app', 'Domyślna')?></th></tr>
<?php 
$i=0;
$prices = $model->prices;
$roles = \common\helpers\ArrayHelper::map(\common\models\UserEventRole::find()->asArray()->all(), 'id', 'name');
for ($i=0; $i<count($prices); $i++){ ?>
<tr><td><?=$roles[$prices[$i]['role_id']]?></td>
<td><?php
    echo $form->field($model, 'prices['.$i.'][price]')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ])->label(false); ?>
</td>
<td><?php
    echo $form->field($model, 'prices['.$i.'][cost]')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ])->label(false); ?>
</td>
<td><?php
    echo $form->field($model, 'prices['.$i.'][cost_hour]')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ])->label(false); ?>
</td>
<td><?php
     echo $form->field($model,'prices['.$i.'][default]')->dropDownList([1=>Yii::t('app', 'TAK'), 0=>Yii::t('app', 'NIE')])->label(false); ?> 
</td>
</tr>
 <?php   } ?>
</table>
<div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

