<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\EventAdditionalStatut */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Dodatkowe statusy wydarzeń'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-additional-statut-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= Html::encode($this->title) ?></h2>
        </div>
        <div class="col-sm-3" style="margin-top: 15px">
            
            <?= Html::a(Yii::t('app', 'Edytuj'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ])
            ?>
        </div>
    </div>
        <div>
        <?= Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj status'), ['create-statut', 'id'=>$model->id], ['class'=>'btn btn-primary'])." "; ?>            
        </div>
<div class="row">
<div class="col-xs-12">
<div class="ibox">
        <div class="ibox-content">
        <ul class="todo-list ui-sortable" id="list">
        <?php foreach (\common\models\EventAdditionalStatutName::find()->where(['active'=>1, 'event_additional_statut_id'=>$model->id])->orderBy(['position'=>SORT_ASC])->all() as $statut){ ?>
        <li class="checklist-item" draggable="true" id="bigitem-<?=$statut->id?>">
        <div class="row">
        <div class="col-xs-9"><?=$statut->name?> <i class="fa <?=$statut->icon?>"></i></div>
        <div class="col-xs-3" style="text-align:right">
                                <?= Html::a('<i class="fa fa-pencil"></i>', ['update-statut', 'id' => $statut->id], [
                                    'class' => 'btn btn-sm',
                                    
                                ])
                                ?>
                                <?= Html::a('<i class="fa fa-trash"></i>', ['delete-statut', 'id' => $statut->id], [
                                    'class' => 'btn btn-danger btn-sm',
                                    'data' => [
                                        'confirm' => Yii::t('app', 'Czy na pewno chcesz usunąć?'),
                                        'method' => 'post',
                                    ],
                                ])
                                ?>
        </div>
        </div>
        </li>
        <?php } ?>
        </ul>
        </div>
</div>
</div>


</div>
</div>

    <?php

$this->registerJs("
$( function() {
    $( '#list').sortable({
    update: function (event, ui) {
        var data = $(this).sortable('serialize');
        $.ajax({
            data: data,
            type: 'POST',
            url: '".Url::to(['/event-additional-statut/order', 'id'=>$model->id])."'
        });
    }
});
    $( '#list').disableSelection();
  } );
  ");
?>