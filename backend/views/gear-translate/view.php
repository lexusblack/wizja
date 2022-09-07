<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\GearTranslate */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Gear Translate', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gear-translate-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= 'Gear Translate'.' '. Html::encode($this->title) ?></h2>
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
        'gear_id',
        'name',
        'info:ntext',
        'language_id',
        ['attribute' => 'id', 'visible' => false],
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]);
?>
    </div>
</div>
