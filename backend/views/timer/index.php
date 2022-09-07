<?php

/* @var $this yii\web\View */
/* @var $searchModel TimerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use kartik\export\ExportMenu;
use common\components\grid\GridView;
use yii\bootstrap\Html;
$this->title = Yii::t('app', 'ShowTime');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="timer-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=Html::a('<i class="fa fa-download"></i> ' . Yii::t('app', 'Pobierz instalator'), '/files/ShowTime.msi', ['class' => 'btn btn-success']); ?>
    <?php 
    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'id', 'visible' => false],
        'name',
        [
            'attribute' => 'filename',
            'value'=>function($model)
            {
                 //return Html::a($model->filename, ['timer/download', 'id'=>$model->id], ['download'=>'download']);
                 return "<a href='timer/download?id=".$model->id."' download>".$model->filename."</a>";
            },
            'format' => 'html',
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template'=>'{download} {delete}',
            'buttons'=>[
                        'download'=>function($url, $model, $key)
                        {
                            return Html::a(Html::icon('save-file'), $url);
                        }
                        ]
        ],
    ]; 
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $gridColumn,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-timer']],
        'export' => false,
    ]); ?>

</div>