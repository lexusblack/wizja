<?php

use yii\helpers\Html;
use common\components\grid\GridView;
use yii\bootstrap\Modal;
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
    <p>
        <?php if ($user->can('menuTasksAdd')) { 
            echo Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']) . " ";
        }
        ?>
    </p>
<div class="menu-pils">
<?= $this->render('_categoryMenu', ['item'=>$menu]); ?>
</div>

<div class="task-index">
<div class="row">
<div class="col-sm-7">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap'
        ],
        'filterModel' => $searchModel,
        'filterSelector'=>'.grid-filters',
        'toolbar' => [
            [
                'content' =>
                    Html::beginForm('', 'get', ['class'=>'form-inline']).Html::activeDropDownList($searchModel, 'task_datetime', [1=>Yii::t('app', 'w ciągu 7 dni'), 2=>Yii::t('app', 'w ciągu 14 dni'), 3=>Yii::t('app', 'w ciągu miesiąca'), 0=>Yii::t('app', 'wszystkie')], ['class'=>'form-control grid-filters', 'prompt'=>Yii::t('app', 'wybierz okres')])

                        .Html::endForm()
            ]

        ],
        'columns' => [
             [
                 'value' => function($model){ 
                    $content = '<div class="pull-left" style="margin-right:10px;">';
                    if (isset($model->creator)) {
                     //$content .= '<img alt="image" class="img-circle img-very-small" src="'.$model->creator->getUserPhotoUrl().'" title="'.$model->creator->first_name.' '.$model->creator->last_name.'">';
                    }
                    $content .='</div>';
                    $content .= Html::a($model->title, ['view', 'id' => $model->id], ['class'=>'show-service']);
                    if (isset($model->event))
                        $content.='<br/>'.Html::a('<small>'.$model->event->displayLabel.'</small>', ['/event/view', 'id'=>$model->event_id], ['target' => '_blank']);
                    if (isset($model->rent))
                        $content.='<br/>'.Html::a('<small>'.$model->rent->displayLabel.'</small>', ['/rent/view', 'id'=>$model->rent_id], ['target' => '_blank']);
                    if (isset($model->customer))
                        $content.='<br/>'.Html::a('<small>'.$model->customer->name.'</small>', ['/customer/view', 'id'=>$model->customer_id], ['target' => '_blank']);
                    if (isset($model->creator))
                        $content.='<br/><small>'.Yii::t('app', 'Utworzył:').$model->creator->first_name.' '.$model->creator->last_name.'</small>';
                    return $content; },
                 'attribute'=>'title',
                 'format'=>'raw'
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
                 'filter' => \common\models\User::getList(),
                 'attribute'=>'usersID',
             ],
             [
                 'label' =>Yii::t('app', 'Status'),
                 'attribute'=>'status',
                'filterType' => GridView::FILTER_SELECT2,
                'filter'=>\common\models\Task::getStatusFilter(),
                'filterWidgetOptions' => [
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...'),
                    ],
                    'pluginOptions' => [
                        'allowClear'=>true,
                    ],
                ],
                 'value' => function($model){ 
                    if ($model->status==10)
                    {
                        $return ='<span class="label label-primary"><i class="fa fa-check-circle"></i> '.Yii::t('app', 'Wykonane').'</span> ';
                    }else{
                      if (($model->status==0)&&(date('Y-m-d')>$model->datetime)&&($model->datetime))
                      {
                          $return ='<span class="label label-danger"><i class="fa fa-exclamation-circle"></i> '.Yii::t('app', 'Po terminie').'</span> ';
                      }else{
                          $return ='<span class="label">'.Yii::t('app', 'Niewykonane').'</span> ';
                      }                      
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
                 'format'=>'raw',
                'contentOptions'=>['style'=>'width: 80px;'],
             ],
             [
                 'label' =>Yii::t('app', 'Mój status'),
                 'attribute'=>'my_status',
                'filterType' => GridView::FILTER_SELECT2,
                'filter'=>[1=>Yii::t('app',"Wykonane"), 2=>Yii::t('app','Niewykonane')],
                'filterWidgetOptions' => [
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...'),
                    ],
                    'pluginOptions' => [
                        'allowClear'=>true,
                    ],
                ],
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
             ]
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

if ($id)
{
  $this->registerJs('
    $(".task-schema-details").empty().load("/admin/task/view?id='.$id.'");
');
  
}
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