<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\PacklistSchema */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Packlist Schema', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="packlist-schema-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= 'Packlist Schema'.' '. Html::encode($this->title) ?></h2>
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
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]);
?>
    </div>
    
    <div class="row">
<?php
if($providerPacklistSchemaItem->totalCount){
    $gridColumnPacklistSchemaItem = [
        ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id', 'visible' => false],
            'name',
            'color',
                ];
    echo Gridview::widget([
        'dataProvider' => $providerPacklistSchemaItem,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-packlist-schema-item']],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<span class="glyphicon glyphicon-book"></span> ' . Html::encode('Packlist Schema Item'),
        ],
        'export' => false,
        'columns' => $gridColumnPacklistSchemaItem
    ]);
}
?>

    </div>
</div>
