<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\components\grid\GridView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\VehicleModel */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Modele pojazdów'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\mootensai\components\JsBlock::widget(['viewFile' => '_script', 'pos'=> \yii\web\View::POS_END, 
    'viewParams' => [
        'class' => 'VehiclePrice', 
        'relID' => 'vehicle-price', 
        'value' => \yii\helpers\Json::encode($model->vehiclePrices),
        'isNewRecord' => ($model->isNewRecord) ? 1 : 0
    ]
]);
?>
<div class="vehicle-model-view">

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
        'capacity',
        'volume',
        'capacity_people',
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]);
?>
    </div>
    
    <div class="row">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>
    <?php
        $forms = [
        [
            'label' => Yii::t('app', 'Stawki'),
            'content' => $this->render('_formVehiclePrice', [
                'row' => \yii\helpers\ArrayHelper::toArray($model->vehiclePrices),
            ]),
        ]
    ];
    echo kartik\tabs\TabsX::widget([
        'items' => $forms,
        'position' => kartik\tabs\TabsX::POS_ABOVE,
        'encodeLabels' => false,
        'pluginOptions' => [
            'bordered' => true,
            'sideways' => true,
            'enableCache' => false,
        ],
    ]);
    ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    </div>
    <div class="row">
    <?php echo $this->render('_tabTranslate', ['model'=>$model]); ?>
    </div>
</div>
