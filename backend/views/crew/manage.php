<?php
/* @var $this \yii\web\View */
/* @var $model \common\models\Event */

use common\models\Vacation;
use yii\bootstrap\Modal;
use common\components\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;
use common\models\Event;
use kartik\dynagrid\DynaGrid;

Modal::begin([
    'header' => Yii::t('app', 'Ekipa'),
    'id' => 'ekipa_modal',
    'class'=>'inmodal inmodal',
    'size' => 'modal-lg',
]);
echo "<div class=\"modalContent\"></div>";
Modal::end();

$this->title = Yii::t('app', 'Zarządzaj ekipą').' - ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wydarzenia'), 'url' => ['event/index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['event/view', 'id'=>$model->id, '#' => 'tab-crew']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Zarządzaj ekipą');
?>
<div class="warehouse-container">
    <div class="row">
        <div class="col-md-12">
            <?php echo Html::a(Html::icon('arrow-left').' Zapisz i wróć', ['event/view', 'id'=>$model->id, '#' => 'tab-crew'], ['class'=>'btn btn-primary']); ?>
        </div>

    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel_mid_blocks">
                <div class="panel_block">
                <?php
                $columns =  [
                    [
                        'class' => 'yii\grid\CheckboxColumn',
                        'checkboxOptions' => function ($item, $key, $index, $column) use ($assignedItems, $model) {
                            $data = [];
                            if (count($item->overlapingEvents($model))>0) {
                                $data['overlaping'] =  1;
                            }
                            if (count($item->eventsIn12HPeriod($model))>0) {
                                $data['close'] = 1;
                            }
                            return [
                                'checked' => in_array($item->id, $assignedItems),
                                'data' => $data
                            ];
                        },
                        'header' => null
                    ],

                    ['class' => 'yii\grid\SerialColumn'],

                    'last_name',
                    'first_name',
                    [
                        'header'=>Yii::t('app', 'Umiejętności'),
                        'attribute' => 'skillId',
                        'filter' => \common\models\Skill::getModelList(),
                        'value'=>function ($model, $key, $index, $column)
                        {
                            $names = $model->getSkills()->select('name')->column();

                            return implode('<br />',$names);
                        },
                        'format' => 'html',
                    ],
                    [
                        'header'=>Yii::t('app', 'Działy'),
                        'attribute' => 'departmentId',
                        'filter' => \common\models\Department::getModelList(),
                        'value'=>function ($model, $key, $index, $column)
                        {
                            $names = $model->getDepartments()->select('name')->column();

                            return implode('<br />',$names);
                        },
                        'format' => 'html',
                    ],
                    [
                        'header'=>Yii::t('app', 'Typ'),
                        'attribute' => 'type',
                        'filter' => \common\models\User::getTypeList(),
                        'value'=>function ($model, $key, $index, $column)
                        {
                            return \common\models\User::getTypeList()[$model->type];
                        },
                        'format' => 'html',
                    ],
                    [
                        'label' => Yii::t('app', 'Zajęte'),
                        'format' => 'raw',
                        'value' => function ($item, $key, $index, $grid) use ($model) {
                            /* @var $item \common\models\User */
                            $result = null;
                            $overlaping = $item->overlapingEvents($model);
                            $period12H = $item->eventsIn12HPeriod($model);
                            $showed = [];

                            if (count($overlaping)>0) {
                                foreach ($overlaping as $event) {
                                    if (in_array($event->id, $showed)) {
                                        continue;
                                    }
                                    $showed[] = $event->id;
                                    $result .= Html::a($event->getTimeStart() . " - " . $event->getTimeEnd(), null, ['class' => 'edit_user', 'data' => ['eventid' => $event->id, 'userid' => $item->id] ]) . " ";
                                    $result .= Html::a($event->name, ['event/view', 'id' => $event->id, '#' => 'tab-crew'], ['target' => '_blank']) . "<br>";
                                }
                            }

                            foreach ($period12H as $event) {
                                if (in_array($event->id, $showed)) {
                                    continue;
                                }
                                $showed[] = $event->id;
                                $result .= Html::a($event->getTimeStart() . " - " . $event->getTimeEnd(), null, ['class' => 'edit_user', 'data' => ['eventid' => $event->id, 'userid' => $item->id] ]) . " ";
                                $result .= Html::a($event->name, ['event/view', 'id' => $event->id, '#' => 'tab-crew'], ['target' => '_blank']) . "<br>";

                            }

                            foreach ($item->overlapingVacations($model) as $vacations) {
                                foreach ($vacations as $vacation) {
                                    if ($vacation->status == Vacation::STATUS_ACCEPTED) {
                                        $result .= Html::a(Yii::t('app','Urlop zaakceptowany'). ": " . $vacation->start_date. " - " .$vacation->end_date, ['vacation/view', 'id' => $vacation->id], ['target' => '_blank'])."<br>";
                                    }
                                    if ($vacation->status == Vacation::STATUS_NEW) {
                                        $result .= Html::a(Yii::t('app','Urlop zaplanowany'). ": " . $vacation->start_date. " - " .$vacation->end_date, ['vacation/view', 'id' => $vacation->id], ['target' => '_blank'])."<br>";
                                    }
                                }
                            }

                            return $result;
                        },
                    ]
                ];
                ?>

<?= DynaGrid::widget([
        'gridOptions'=>[
            'filterSelector'=>'.grid-filters',
            'showPageSummary' => false,
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            
            'tableOptions' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap'
            ],
            'id'=>'crew-manage-grid',

        
            'toolbar' => [

                '{dynagrid}',
                '{dynagridFilter}',
                '{dynagridSort}'

                ],
        ],
        'allowThemeSetting'=>false,
        'storage'=>DynaGrid::TYPE_COOKIE,
        'options'=>['id'=>'dynagrid-crew'],
        'columns' => $columns,
        
    ]); ?>


                </div>
            </div>
        </div>

    </div>

<?php

$assignUrl = Url::to(['crew/assign-user', 'id'=>$model->id,]);
$assignWholeEvent = Url::to(['crew/assign-user-to-whole-event', 'event_id'=>$model->id]);
$assignWarnings = '';

$this->registerJs('

$("input:checkbox").each(function(){
    if ($(this).data("overlaping") == 1 || $(this).data("close") == 1) {
        if ($(this).data("overlaping") == 1)
        {
            $(this).parent().parent().css("background-color", "#f8d7da");
        }else{
            $(this).parent().parent().css("background-color", "#fff3cd");
        }
    }
});

$("body").on("click", ".edit_user", function(e){
    e.preventDefault();
    openUserDetailsModal($(this).data("eventid"), $(this).data("userid"));

});

function openUserDetailsModal(event_id, user_id){
    var modal = $("#ekipa_modal");
    modal.find(".modalContent").load("'.Url::to(["planboard/user-form"]).'?event_id="+event_id+"&user_id="+user_id+"&role='.$role_id.'&in_event=1");
    modal.modal("show");
}

$(":checkbox").not(".select-on-check-all").on("change", function(e){
    e.preventDefault();
    var add = $(this).prop("checked");
    var user_id = $(this).val();
    clickCheckbox(add, user_id);
});

function clickCheckbox(add, user_id)
{
    if (!add) {
        assignUser(user_id, add);
    }
    else {
        /*if ($(this).data("overlaping") == 1 || $(this).data("close") == 1) {
            assignUser(user_id, add);
            openUserDetailsModal('.$model->id.', user_id);
        }
        else {
            assignUserToWholeEvent(user_id);
        } */
            assignUser(user_id, 1);
            openUserDetailsModal('.$model->id.', user_id);
    }  
}


function assignUser(user_id, add) {
    var data = {
        itemId : user_id,
        add : add
    };
    if (!add)
        $.post("'.$assignUrl.'", data, function(response){toastr.error("'.Yii::t('app', 'Pracownik usunięty z ekipy').'");});
    else
        $.post("'.$assignUrl.'", data, function(response){toastr.success("'.Yii::t('app', 'Pracownik dodany do ekipy').'");});
}

function assignUserToWholeEvent(user_id){
    $.post("'.$assignWholeEvent.'&user_id="+user_id, function(response){toastr.success("'.Yii::t('app', 'Pracownik dodany do ekipy').'");});
    
}

$(":checkbox.select-on-check-all").on("change", function(e){
    e.preventDefault();
    var add = $(this).prop("checked");
    var elements = $(this).closest("table").find("tbody :checkbox");
    elements.each(function(index, element){
            $(element).prop("checked", add);
            var user_id = $(this).val();
            clickCheckbox(add, user_id);

    });
});

');

$this->registerCss('
    .display_none {display: none;}
    .panel .panel-heading{display:none}
    .kv-editable-link{border-bottom:0}
');



