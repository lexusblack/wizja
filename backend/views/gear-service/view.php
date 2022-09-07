<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\GearService */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Serwis'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gear-service-view">

    <p>
        <?= Html::a('<i class="fa fa-pencil"></i> ' . Yii::t('app', 'Edycja'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php if ($model->type != 10) { ?>
        
        <?php } ?>
        <?php
//        echo Html::a('<i class="fa fa-trash"></i> ' . 'Usuń', ['delete', 'id' => $model->id], [
//            'class' => 'btn btn-danger',
//            'data' => [
//                'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
//                'method' => 'post',
//            ],
//        ]) ?>
    </p>

<div class="row">
    <div class="col-md-12">
        <div class="ibox">
            <div class="ibox-title">
                <h5><?php echo Yii::t('app', 'Serwis ').$model->id ?></h5>
            </div>
        <div class="ibox-content">
    <?= DetailView::widget([
        'model' => $model,
        'options' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap\'',
        ],
        'attributes' => [
            [
                'attribute'=>'gear_item_id',
                'value'=>function ($gear) {
                            if ($gear->gearItem->gear->no_items)
                                return Html::a($gear->gearItem->gear->name. " [" . $gear->quantity."]",['/gear/view', 'id'=>$gear->gearItem->gear_id]);
                            else    
                                return Html::a($gear->gearItem->name. " [" . $gear->gearItem->number."]",['/gear-item/view', 'id'=>$gear->gear_item_id]);
                        },
                'format'=>'html',
            ],
            'gearItem.gear.name:text:'.Yii::t('app', 'Model'),
            'gearItem.number:text:'.Yii::t('app', 'Numer'),
            'gearItem.serial:text:'.Yii::t('app', 'Numer seryjny'),
            'description:html',
            'statusLabel:text:'.Yii::t('app', 'Status'),
            'status_time',
            'typeLabel:text:'.Yii::t('app', 'Typ'),
            'info:ntext',
            'create_time',
            'update_time',
        ],
    ]) ?>
    
</div>
</div>
</div>
    </div>
</div>