<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\HallGroupCost */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Hall Group Cost', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hall-group-cost-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= 'Hall Group Cost'.' '. Html::encode($this->title) ?></h2>
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
            'attribute' => 'hallGroup.name',
            'label' => 'Hall Group',
        ],
        'cost',
        'name',
        'currency',
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]);
?>
    </div>
    <div class="row">
        <h4>HallGroup<?= ' '. Html::encode($this->title) ?></h4>
    </div>
    <?php 
    $gridColumnHallGroup = [
        ['attribute' => 'id', 'visible' => false],
        'name',
        'area',
        'width',
        'length',
        'height',
        'main_photo',
        'description',
    ];
    echo DetailView::widget([
        'model' => $model->hallGroup,
        'attributes' => $gridColumnHallGroup    ]);
    ?>
</div>
