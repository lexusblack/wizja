<?php

use common\components\grid\GridView;
use yii\bootstrap\Modal;
use yii\bootstrap\Html;
use kartik\helpers\Enum;
use backend\modules\permission\models\BasePermission;

/* @var $this yii\web\View */
/* @var $searchModel common\models\TaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Zadania');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;

Modal::begin([
    'id' => 'new-service',
    'header' => Yii::t('app', 'Dodaj zadanie'),
    'class' => 'modal',
        'size' => 'modal-lg',
    'options' => [
        'tabindex' => false,
    ],
    'clientOptions' => [
    'keyboard'=> false,
        'backdrop'=> 'static'
    ]
]);
echo "<div class='modalContent'></div>";
Modal::end();
Modal::begin([
    'id' => 'edit-service',
    'header' => Yii::t('app', 'Edytuj zadanie'),
    'class' => 'modal',
        'size' => 'modal-lg',
    'options' => [
        'tabindex' => false,
    ],
    'clientOptions' => [
    'keyboard'=> false,
        'backdrop'=> 'static'
    ]
]);
echo "<div class='modalContent'></div>";
Modal::end();
Modal::begin([
    'id' => 'edit-users',
    'header' => Yii::t('app', 'Przypisz użytkowników'),
    'class' => 'modal',
    'options' => [
        'tabindex' => false,
    ],
    'clientOptions' => [
    'keyboard'=> false,
        'backdrop'=> 'static'
    ]
]);
echo "<div class='modalContent'></div>";
Modal::end();
?>
<div class="menu-pils">
<?= $this->render('_categoryMenu', ['item'=>$menu]); ?>
</div>

<div class="task-index">
<div class="row">
<div class="col-sm-7">
    <?= GridView::widget([
        'filterSelector'=>'.grid-filters',
        'afterRow' => function($model, $key, $index, $grid)
            {
                $afterRow = '';
                $afterRow .= GridView::widget([
                        'dataProvider' => $model->getTaskProvider(),
                        'layout'=>'{items}',
                        'filterModel' => null,
                        'options' => ['class'=>'grid-view grid-view-items'],
                        'toolbar' => null,
                        'columns' => [
                             [
                                 'value' => function($model){ 
                                    $content = '<div class="pull-left" style="margin-right:10px;">';
                                    if (isset($model->creator)) {
                                     //$content .= '<img alt="image" class="img-circle img-very-small" src="'.$model->creator->getUserPhotoUrl().'" title="'.$model->creator->first_name.' '.$model->creator->last_name.'">';
                                    }
                                    $content .='</div>';
                                    $content .= Html::a($model->title, ['view', 'id' => $model->id], ['class'=>'show-service']);
                                    if (isset($model->task_category_id))
                                        $content.='<br/><small>'.$model->taskCategory->name.'</small>';
                                    if (isset($model->creator))
                                        $content.='<br/><small>'.Yii::t('app', 'Utworzył:').$model->creator->first_name.' '.$model->creator->last_name.'</small>';
                                    return $content; },
                                 'attribute'=>'title',
                                 'format'=>'html',
                                 'enableSorting' => false
                             ],
                             [
                                 'value' => function($model){ 
                                    if ($model->datetime) { 
                                        if (($model->status==0)&&(date('Y-m-d')>$model->datetime)){ $class= "label-danger"; }else{ $class="";}
                                     return '<small class="label '.$class.'"><i class="fa fa-clock-o"></i> '.substr($model->datetime, 0, 11).'</small> ';
                                    } },
                                 'attribute'=>'datetime',
                                 'format'=>'html',
                                'contentOptions'=>['style'=>'width: 80px;'],
                                'enableSorting' => false
                             ],
                             [
                                 'label' =>Yii::t('app', 'Użytkownicy'),
                                 'value' => function($model){ $return = ""; 
                                        foreach ($model->getAllUsers() as $team){ 
                                        $status = $model->checkStatusForUser($team->id);
                                        $return .='<a href="/admin/task/edit-users?id='.$model->id.'" style="position:relative;" class="edit-users-button">';
                                        if ($status) { 
                                         $return .='<span class="badge badge-primary pull-right status-bagde"><i class="fa fa-check"></i></span>';
                                        } 
                                        $return .='<img alt="image" class="img-circle img-very-small" src="'.$team->getUserPhotoUrl().'" title="'.$team->first_name." ".$team->last_name.'"></a>';
                                     }
                                     if ((Yii::$app->user->can('menuTasksEdit'.BasePermission::SUFFIX[BasePermission::ALL]))||($model->creator_id==Yii::$app->user->id)) {
                                        $return .=Html::a('<i class="fa fa-plus"></i> ', ['/task/edit-users', 'id'=>$model->id], ['class'=>'btn btn-default btn-circle edit-users-button']);
                                      }

                                    return $return;},
                                 'format'=>'raw',
                                 'attribute'=>'usersID',
                                 'enableSorting' => false
                             ],
                             [
                                 'label' =>Yii::t('app', 'Status'),
                                 'attribute'=>'status',
                                 'value' => function($model){ 
                                if ($model->status==10)
                                                    {
                                                        $return ='<span class="label label-primary"><i class="fa fa-check-circle"></i> '.Yii::t('app', 'Wykonane').'</span> ';
                                                    }
                                                    if (($model->status==0)&&(date('Y-m-d')>$model->datetime)&&($model->datetime))
                                                    {
                                                        $return ='<span class="label label-danger"><i class="fa fa-exclamation-circle"></i> '.Yii::t('app', 'Po terminie').'</span> ';
                                                    }else{
                                                        $return ='<span class="label">'.Yii::t('app', 'Niewykonane').'</span> ';
                                                    }
                                                    if (count($model->taskAttachments)>0)
                                                      $label_att = 'label-success';
                                                    else
                                                      $label_att = '';
                                                    if (count($model->taskNotes)>0)
                                                      $label_notes = 'label-success';
                                                    else
                                                      $label_notes = '';
                                                    $return .='<span class="label '.$label_att.'" style="margin-top:5px; float:left;"><i class="fa fa-file-o"></i> '.count($model->taskAttachments).'</span>';
                                                    $return .='<span class="label '.$label_notes.'" style="margin-top:5px; margin-left:3px; float:left;"><i class="fa fa-comments"></i> '.count($model->taskNotes).'</span>';
                                                    return $return;

                                },
                                 'format'=>'html',
                                'contentOptions'=>['style'=>'width: 80px;'],
                                'enableSorting' => false
                             ],
                             [
                                 'label' =>Yii::t('app', 'Mój'),
                                 'value' => function($model){ 
                                                if ($model->isMine(Yii::$app->user->id))
                                                {
                                                    if (($model->status==10)||($model->checkStatusForUser(Yii::$app->user->id))){
                                                           return Html::a('<i class="fa fa-check"></i> ', ['/task/done', 'id'=>$model->id], ['class'=>'btn btn-primary btn-circle done-button']);
                                                         }else { 
                                                            return Html::a('<i class="fa fa-check"></i> ', ['/task/done', 'id'=>$model->id], ['class'=>'btn btn-primary btn-circle btn-outline done-button']);
                                                         } 
                                                }else{
                                                    return "";
                                                }

                                },
                                 'format'=>'html',
                                'contentOptions'=>['style'=>'width: 50px;'],
                                'enableSorting' => false
                             ]
                        ],
                    ]);
            return Html::tag('tr',Html::tag('td', $afterRow, ['colspan'=>6]), ['class'=>'event-task-details']);
            },
        'dataProvider' => $dataProvider,
        'tableOptions' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap'
        ],
        'filterModel' => $searchModel,
        'toolbar' => [
            [
                'content' =>
                    Html::beginForm('', 'get', ['class'=>'form-inline']) .
                    Html::activeInput('text', $searchModel, 'task_name', ['class'=>'form-control grid-filters', 'placeholder'=>Yii::t('app', 'Nazwa zadania')]). 
                    Html::activeDropDownList($searchModel, 'task_status', [1=>Yii::t('app', 'Niewykonane'), 2=>Yii::t('app', 'Wykonane')], ['class'=>'form-control grid-filters', 'prompt'=>Yii::t('app', 'status')]). 
                    Html::activeDropDownList($searchModel, 'year', Enum::yearList(2016, (date('Y')+1), true), ['class'=>'form-control grid-filters', 'prompt'=>Yii::t('app', 'rok')])
                    . Html::activeDropDownList($searchModel, 'month', Enum::monthList(),['class'=>'form-control grid-filters', 'prompt'=>Yii::t('app', 'miesiąc')])
                        .Html::endForm()
            ]

        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                    'value'=> function(){
                        return Html::icon('arrow-down', ['class'=>'event-show-task', 'style' => 'cursor: pointer;']);
                    },
                    'format'=>'html',
                    'contentOptions'=>['style'=>'width: 70px;'],
            ],
            [
                'attribute'=>'name',
                'label'=>Yii::t('app', 'Wydarzenie'),
                'format'=>'raw',
                'value'=>function($model)
                {
                    $content = Html::a($model->name.' ['.$model->code.']', ['/event/view', 'id'=>$model->id], [ 'target'=>'_blank'])."<br/>";
                    if (isset($model->customer))
                        $content .=$model->customer->name;

                    return $content;
                }
            ],
            [
                'label'=>Yii::t('app', 'Status'),
                'format'=>'html',
                'value'=>function($model)
                {
                    $status = $model->getTaskStatus();
                    if ($status['task']==0)
                        return '<small>'.Yii::t('app', 'Brak zadań').'</small>';
                    $content = '<small>'.Yii::t('app', 'Ukończono').': '.$status['status'].'%</small>
                    <div class="progress progress-mini">
                    <div style="width: '.$status['status'].'%;" class="progress-bar"></div>
                    </div>';
                    return $content;
                },
                'contentOptions'=>['style'=>'width: 110px;'],
            ],
            [
                'value'=>'manager.displayLabel',
                'filter' => \common\models\User::getList(),
                'attribute' => 'manager_id',
                'filterType' => GridView::FILTER_SELECT2,
                 'filterWidgetOptions' => [
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...'),
                    ],
                    'pluginOptions' => [
                        'allowClear'=>true,
                    ],
                ],
            ],
            [
                'label'=>Yii::t('app', 'Od - do'),
                'attribute'=>'event_start',
                'content' => function ($model, $index, $row, $grid)
                {
                    $start = Yii::$app->formatter->asDateTime($model->getTimeStart(),'short');
                    $end = Yii::$app->formatter->asDateTime($model->getTimeEnd(), 'short');
                    return $start.' <br /> '.$end;
                },
                'contentOptions'=>['style'=>'width: 150px;'],
            ],
        ],
    ]); ?>
</div>
        <div class="col-sm-5 task-schema-details">
                                    <blockquote>
                                    <p><?=Yii::t('app', 'Kliknij w nazwę zadania, żeby wyświetlić szczegóły')?>.</p>
                                </blockquote>
        </div>
</div>
</div>

<?php
$this->registerJs('


$(".table-bordered").each(function(){
    $(this).removeClass("table-bordered");
});
$(".table-striped").each(function(){
    $(this).removeClass("table-striped");
});
$(".event-show-task").click(function(e)
{
    e.preventDefault();
    if ($(this).hasClass("glyphicon-arrow-up"))
    {
        $(this).parent().parent().next().slideUp();
    }else{
        $(this).parent().parent().next().slideDown();
    }
                    $(this).toggleClass("glyphicon-arrow-up");
                    $(this).toggleClass("glyphicon-arrow-down");

})
');

$this->registerJs('
    $(".show-service").click(function(e){
        e.preventDefault();
        $(".task-schema-details").empty().load($(this).attr("href"));
    });
    $(".show-service").on("contextmenu",function(){
       return false;
    });
');

$this->registerJs('
    $(".done-button").click(function(e){
        e.preventDefault();
        data = [];
        $.post($(this).attr("href"), data, function(response){
                        editServiceRow(response);
                    });
    });
    $(".done-button").on("contextmenu",function(){
       return false;
    });
');

$this->registerJs('
    $(".add-service").click(function(e){
        $("#new-service").find(".modalContent").empty();
        e.preventDefault();
        $("#new-service").modal("show").find(".modalContent").load($(this).attr("href"));
    });
    $(".add-service").on("contextmenu",function(){
       return false;
    });
'); 

$this->registerJs('
    $(".edit-users-button").click(function(e){
        $("#edit-users").find(".modalContent").empty();
        e.preventDefault();
        $("#edit-users").modal("show").find(".modalContent").load($(this).attr("href"));
    });
    $(".edit-users-button").on("contextmenu",function(){
       return false;
    });
'); 

$this->registerCss('
    .display_none {display: none;}
');
?>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
    function addNewRow(data)
    {      
        $(".task-schema-details").empty().load('/admin/task/view?id='+data.id);
    }


    function editServiceRow(data)
    {
                $.post('/admin/task/small-view-table?id='+data.id, data, function(response){
                        var row = $("[data-key="+data.id+"]");
                        row.empty().append(response);
                            row.find(".done-button").click(function(e){
                                    e.preventDefault();
                                    data = [];
                                    $.post($(this).attr("href"), data, function(response){
                                        editServiceRow(response);
                                                });
                                });
                            row.find(".show-service").click(function(e){
                                e.preventDefault();
                                $(".task-schema-details").empty().load($(this).attr("href"));
                            });
                    });
        $(".task-schema-details").empty().load('/admin/task/view?id='+data.id);
    }

    function deleteItem(item)
    {
        swal({
            title: "Czy Na pewno chcesz usunąć?",
            icon:"warning",
          buttons: {
            cancel: "Nie",
            yes: {
              text: "Tak",
              value: "yes",
            },
          },
        })
        .then((value) => {
          switch (value) {
         
            case "yes":
                    data=[];
                    $.post(item.attr('href'), data, function(response){
                        row = $('#item-'+response.id);
                        row.remove();
                    });
              break;       
          }
        });
    }
    </script>

