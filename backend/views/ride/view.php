<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Ride */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => (Yii::$app->params['companyID']=="imagination")?Yii::t('app','Kilometrówka') : Yii::t('app','Przejazdy'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ride-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= 'Przejazd'.' '. Html::encode($this->title) ?></h2>
        </div>
        <div class="col-sm-3" style="margin-top: 15px">
            
            <?= Html::a(Yii::t('app','Edytuj'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('app','Usuń'), ['delete', 'id' => $model->id], [
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
            'attribute' => 'vehicle.name',
            'label' => Yii::t('app','Pojazd'),
        ],
        [
            'attribute' => 'user.username',
            'label' => Yii::t('app','Dodał'),
        ],
        [
            'attribute' => 'event.name',
            'label' => Yii::t('app','Wydarzenie'),
        ],
        'start',
        'end',
        'km_start',
        'km_end',
        'start_place',
        'end_place',
        'description:ntext',
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]);
?>
    </div>

</div>
