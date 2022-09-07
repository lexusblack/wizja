<?php
use yii\bootstrap\Html;
use yii\helpers\Url;
$user = Yii::$app->user;
/* @var $model \common\models\Event; */
use common\components\grid\GridView;
use yii\bootstrap\Modal;

Modal::begin([
    'id' => 'add-to-pr',
    'header' => Yii::t('app', 'Dodaj event do projektu'),
    'class'=> 'modal',
    'options' => [
        'tabindex' => false,
    ],
]);
echo "<div class='modalContent'></div>";
Modal::end();

$this->registerJs('
    $(".add-to-project").click(function(e){
        $("#add-to-pr").find(".modalContent").empty();
        e.preventDefault();
        $("#add-to-pr").modal("show").find(".modalContent").load($(this).attr("href"));
    });');
?>
<div class="panel-body">
        <?php
        if ($user->can('eventsEventAdd')) {
            echo Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj nowy'), ['/event/create', 'project_id'=>$model->id], ['class' => 'btn btn-success btn-sm']) . " ".Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj z istniejących'), ['/project/add-to-project', 'id'=>$model->id], ['class' => 'btn btn-success btn-sm add-to-project']);
        } ?>
                            <div class="project-list">
    <?= GridView::widget([
        'filterSelector'=>'.grid-filters',
        'dataProvider' => $model->getAssignedEvents(),
        'tableOptions' => [
            'class' => 'table table-hover'
        ],
        'columns' => [
            [
                'attribute'=>'name',
                'label'=>false,
                'format'=>'html',
                'contentOptions' => ['class' => 'project-title'],
                'value'=>function($model)
                {
                    $content = Html::a($model->name." [".$model->code."]", ['/event/view', 'id' => $model->id]);
                    if (isset($model->customer))
                    {
                     $content .="<br/>";
                    $content .="<small>".$model->customer->name;"</small>";                       
                    }
                    $content .="<br/>";
                    $content .="<small>".substr($model->getTimeStart(), 0, 10)." - ".substr($model->getTimeEnd(), 0, 10)."</small>";
                    return $content;
                },
            ],
            [
                'label'=>false,
                'format'=>'html',
                'contentOptions' => ['class' => 'project-completion'],
                'value'=>function($model)
                {
                    $content = '<small>'.Yii::t('app', 'Ukończono:').' '.$model->getTaskStatus()['status'].'% </small><div class="progress progress-mini">
                                                    <div style="width: '.$model->getTaskStatus()['status'].'%;" class="progress-bar"></div>
                                                </div>';
                    return $content;
                },
            ],
            [
                'format'=>'html',
                'value'=>function($model)
                {
                    if ($model->location)
                    {
                        $content = Html::a($model->location->name, ['/location/view', 'id' => $model->location->id]);
                        return $content;
                    }else{
                        return $model->address;
                    }

                },
                'attribute'=>'location_id',
            ],
            [
                'label'=>false,
                'format'=>'html',
                'contentOptions' => ['class' => 'project-people'],
                'value'=>function($model)
                {
                    $content = " ";
                    if ($model->manager)
                    {
                        $content = $content.' <img alt="image" class="img-circle" src="'.$model->manager->getUserPhotoUrl().'" title="'.$model->manager->first_name." ".$model->manager->last_name.'">';
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
                    $content = Html::a('<i class="fa fa-folder"></i> '.Yii::t('app', 'Podgląd'), ['/event/view', 'id' => $model->id], ['class'=>"btn btn-white btn-xs"]);
                    $content .= " ".Html::a('<i class="fa fa-trash"></i> '.Yii::t('app', 'Usuń z projektu'), ['/event/delete-from-project', 'id' => $model->id], ['class'=>"btn btn-danger btn-xs"]);
                    return $content;
                },
            ],
            ]
    ]); ?>
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