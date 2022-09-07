<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\TasksSchemaCat */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Tasks Schema Cat', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tasks-schema-cat-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= 'Tasks Schema Cat'.' '. Html::encode($this->title) ?></h2>
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
        [
            'attribute' => 'tasksSchema.name',
            'label' => 'Tasks Schema',
        ],
        'name',
        'order',
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]);
?>
    </div>
    
    <div class="row">
<?php
if($providerTaskSchema->totalCount){
    $gridColumnTaskSchema = [
        ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id', 'visible' => false],
                        'name',
            'description:ntext',
            'order',
    ];
    echo Gridview::widget([
        'dataProvider' => $providerTaskSchema,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-task-schema']],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<span class="glyphicon glyphicon-book"></span> ' . Html::encode('Task Schema'),
        ],
        'export' => false,
        'columns' => $gridColumnTaskSchema
    ]);
}
?>

    </div>
    <div class="row">
        <h4>TasksSchema<?= ' '. Html::encode($this->title) ?></h4>
    </div>
    <?php 
    $gridColumnTasksSchema = [
        ['attribute' => 'id', 'visible' => false],
        'name',
        'default',
        'type',
    ];
    echo DetailView::widget([
        'model' => $model->tasksSchema,
        'attributes' => $gridColumnTasksSchema    ]);
    ?>
</div>
