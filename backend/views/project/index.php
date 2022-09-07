<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\ProjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use common\components\grid\GridView;
$this->title = Yii::t('app', 'Projekty');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
            <div class="col-lg-12">
                <div class="wrapper wrapper-content animated fadeInUp">

                    <div class="ibox">
                        <div class="ibox-title">
                            <h5><?=$this->title?></h5>
                            <div class="ibox-tools">
                                    <?php 
                                        echo Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Stwórz projekt'), ['create'], ['class' => 'btn btn-primary btn-xs']);
                                    ?>
                            </div>
                        </div>
                        <div class="ibox-content">

                            <div class="project-list">
    <?= GridView::widget([
        'filterSelector'=>'.grid-filters',
        'dataProvider' => $dataProvider,
        'tableOptions' => [
            'class' => 'table table-hover'
        ],
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute'=>'start_time',
                'label'=>false,
                'format'=>'html',
                'contentOptions' => ['class' => 'project-status'],
                'value'=>function($model)
                {
                    return $model->statusLabel();
                },
            ],
            [
                'attribute'=>'name',
                'label'=>false,
                'format'=>'html',
                'contentOptions' => ['class' => 'project-title'],
                'value'=>function($model)
                {
                    $content = Html::a($model->name." [".$model->code."]", ['view', 'id' => $model->id]);
                    if (isset($model->customer))
                    {
                     $content .="<br/>";
                    $content .="<small>".$model->customer->name;"</small>";                       
                    }
                    $content .="<br/>";
                    $content .="<small>".substr($model->start_time, 0, 10)." - ".substr($model->end_time, 0, 10)."</small>";
                    return $content;
                },
            ],
            [
                'label'=>false,
                'format'=>'html',
                'contentOptions' => ['class' => 'project-completion'],
                'value'=>function($model)
                {
                    $content = '<small>'.Yii::t('app', 'Ukończono:').' '.$model->getCompletion()['status'].'% </small><div class="progress progress-mini">
                                                    <div style="width: '.$model->getCompletion()['status'].'%;" class="progress-bar"></div>
                                                </div>';
                    return $content;
                },
            ],
            [
                'label'=>false,
                'format'=>'html',
                'contentOptions' => ['class' => 'project-people'],
                'value'=>function($model)
                {
                    $content = " ";
                    foreach ($model->projectUsers as $pu)
                    {
                        $content = $content.' <img alt="image" class="img-circle" src="'.$pu->user->getUserPhotoUrl().'" title="'.$pu->user->first_name." ".$pu->user->last_name.'">';
                    }
                    return $content;
                },
            ],
            [
                'label'=>false,
                'format'=>'raw',
                'contentOptions' => ['class' => 'project-actions'],
                'value'=>function($model)
                {
                    $content = Html::a('<i class="fa fa-folder"></i> '.Yii::t('app', 'Podgląd'), ['view', 'id' => $model->id], ['class'=>"btn btn-white btn-sm"]);
                    $content .=" ".Html::a('<i class="fa fa-pencil"></i> '.Yii::t('app', 'Edycja'), ['update', 'id' => $model->id], ['class'=>"btn btn-white btn-sm"]);
                    return $content;
                },
            ],
            ]
    ]); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>




<?php

$this->registerCss('
    .project-list .panel.panel-default{
        border:0;
    }
    .project-list .kv-panel-before{
        display:none;
    }
    .project-list .panel-footer{
        background-color: #fff;
    }
    .project-list .table-bordered > thead > tr > th, .project-list .table-bordered > thead > tr > td{
        background-color: #fff;
        border-left:0;
        border-right:0;
    }
    .project-list .table-bordered > tbody> tr > th, .project-list .table-bordered > tbody> tr > td{
        background-color: #fff;
        border-left:0;
        border-right:0;
    }
    .project-list .table-striped > tbody > tr:nth-of-type(odd) {
        background-color: #fff;
    }
    .project-list .table-striped > thead {
        display:none;
    }
');

?>