<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\ProvisionGroup */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Grupy prowizyjne'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provision-group-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= Html::encode($this->title) ?></h2>
        </div>
        <div class="col-sm-3" style="margin-top: 15px">
            
            <?= Html::a(Yii::t('app', 'Edycja'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Czy na pewno chcesz usunąć?'),
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
        [
            'attribute' => 'team.name',
            'label' => 'Team',
        ],
        'level',
        'provision',
        [
            'attribute'=>'type',
            'value'=>function($model){
                return \common\models\ProvisionGroup::getTypes()[$model->type];
            }
        ],
        [
            'attribute'=>'main_only',
            'value'=>function($model){
                if ($model->main_only)
                {
                    return Yii::t('app', 'TAK'); 
                }else{
                    return Yii::t('app', 'NIE'); 
                }
            }
        ],
        [
            'attribute'=>'add_to_users',
            'value'=>function($model){
                if ($model->add_to_users)
                {
                    return Yii::t('app', 'TAK'); 
                }else{
                    return Yii::t('app', 'NIE'); 
                }
            }
        ],
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]);
?>
    </div>
    
    <div class="row">
    <h2><?= Yii::t('app', 'Prowizje w konkretnych sekcjach') ?></h2>
<?php
if($providerProvisionGroupProvision->totalCount){
    $gridColumnProvisionGroupProvision = [
        ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id', 'visible' => false],
                        'section',
            'value',
            'type',
    ];
    echo Gridview::widget([
        'dataProvider' => $providerProvisionGroupProvision,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-provision-group-provision']],
        'export' => false,
        'columns' => $gridColumnProvisionGroupProvision
    ]);
}
?>

    </div>
</div>
