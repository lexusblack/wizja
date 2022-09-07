<?php

use yii\bootstrap\Html;
use yii\widgets\DetailView;
use kartik\tabs\TabsX;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UserEventRole */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Role na wydarzeniu'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\mootensai\components\JsBlock::widget(['viewFile' => '_script', 'pos'=> \yii\web\View::POS_END, 
    'viewParams' => [
        'class' => 'RolePrice', 
        'relID' => 'role-price', 
        'value' => \yii\helpers\Json::encode($model->rolePrices),
        'isNewRecord' => ($model->isNewRecord) ? 1 : 0
    ]
]);
?>
<div class="user-event-role-view">

    <p>
        <?= Html::a('<i class="fa fa-pencil"></i> ' . Yii::t('app', 'Edycja'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fa fa-trash"></i> ' . Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <div class="row">
    <div class="col-md-4">
        <div class="ibox float-e-margins"> 
        <div class="ibox-content">
    <?= DetailView::widget([
        'model' => $model,
        'options' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap\'',
        ],
        'attributes' => [
            'name',
            [
                'attribute' => 'compatibility',
                'value'=> $model->compatibility==1 ? Yii::t('app', 'Tak') : Yii::t('app', 'Nie'),
            ]
        ],
    ]) ?>
    </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="ibox float-e-margins"> 
        <div class="tabs-container">
        
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>
                <?php
                $tabItems = [];
                $tabItems[] = [
                        'label' => Yii::t('app', 'Stawki'),
                        'content' => $this->render('_formRolePrice', [
                            'row' => \yii\helpers\ArrayHelper::toArray($model->rolePrices),
                            'model'=>$model,
                            'active'=>true,
                        ]),
                    ];
                    
                                $tabItems[] = [
                    'label'=> Yii::t('app', 'Tłumaczenia'),
                    'content'=>$this->render('_tabTranslate', ['model'=>$model]),
                    
                ];

                echo TabsX::widget([
                    'items'=>$tabItems,
                    'encodeLabels'=>false,
                    'enableStickyTabs'=>true,
                ]);
                ?>
               <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?> 
            </div>
        </div>
    </div>
    </div>
</div>
