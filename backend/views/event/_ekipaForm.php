<?php

use common\models\EventBreaks;
use kartik\widgets\DateTimePicker;
use kartik\widgets\Select2;
use yii\bootstrap\Html;
use kartik\grid\GridView;
use kartik\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use kartik\daterange\DateRangePicker;


$format = <<< SCRIPT
function format(obj) {
	if (!obj.id) return obj.text;
	icon = '<span class="glyphicon glyphicon-'+obj.text+'" aria-hidden="true"></span>';
    return icon;
}
SCRIPT;
$escape = new JsExpression("function(m) { return m; }");
$this->registerJs($format, View::POS_HEAD);

$user_id = $user->id;
$event_id = $model->id;
$form = ActiveForm::begin(['type' => ActiveForm::TYPE_INLINE, 'id' => 'add_working_time_form',
    'action' => ['planboard/user-form', 'event_id' => $model->id, 'user_id' => $user_id,
        'update_event_user_data' => 1]]);

?>
    <h2><?= $user->last_name . ' ' . $user->first_name ?></h2>
    <div class="row">
        <div class="col-md-12">
            <?php if (Yii::$app->user->can('eventsEventEditEyeCrewEdit')):?>
            <div class="panel panel-primary">
                <div class="panel-heading"><h4 class="control-label"><?= Yii::t('app', 'Role na evencie:') ?></h4></div>
                <div class="panel-body">
                    <?php if ($noOffer) { ?>
                        <div class="alert alert-danger">
                            <?= Yii::t('app', 'Brak zaakceptowanej oferty') ?>
                        </div><?php
                    } ?>

                    <?= Select2::widget(['data' => $role_list, 'name' => 'roles', 'value' => $user_roles,
                        'options' => ['id' => 'rytut46453vcerty', 'placeholder' => Yii::t('app', 'Wybierz role...'), 'multiple' => true],
                        'pluginOptions' => ['allowClear' => true],]); ?>
                </div>
            </div>
    <?php endif; ?>
    <?php if (Yii::$app->user->can('eventsEventEditEyeCrewEdit')):?>


            <div class="panel panel-primary">
                <div class="panel-heading"><h4><?php echo Yii::t('app', 'Godziny pracy:'); ?></h4></div>
                <div class="panel-body">
                    <?php
                    if ($model->packing_start && $model->packing_end) { ?>
                        <div class="row">
                            <div class="col-md-3">
                                <label>
                                    <?= Html::checkbox('workWholePacking', $checked_packing, ['value' => 1]) ?>
                                    <?=  Yii::t('app', 'Pakowanie') ?>
                                </label>
                            </div>
                            <div class="col-md-8">
                                <?= substr($model->packing_start, 0, strlen($model->packing_start) - 3) . " - " . substr($model->packing_end, 0, strlen($model->packing_end) - 3) ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <?php
                    if ($model->montage_start && $model->montage_end) {
                        $classMontage = null;
                        $disableMontage=null;
                        if(count($closeEvents['montage'])>0 || count($plannedVacations['montage'])>0) {
                            $classMontage = 'alert alert-warning';
                        }
                        if(count($overlapingEvents['montage'])>0 || count($vacations['montage'])>0) {
                            $classMontage = 'alert alert-danger';
                            $disableMontage = 'disabled';
                        }
                        ?>
                        <div class="row <?= $classMontage ?>" style="margin-bottom: 0; border-bottom: 0;">
                            <div class="col-md-3">
                                <label>
                                    <?= Html::checkbox('workWholeMontage', $checked_montage, ['value' => 1, 'disabled' => $disableMontage]) ?>
                                    <?= Yii::t('app', 'Montaż') ?>
                                </label>
                            </div>
                            <div class="col-md-8">
                                <?= substr($model->montage_start, 0, strlen($model->montage_start) - 3) . " - " . substr($model->montage_end, 0, strlen($model->montage_end) - 3) ?>
                            </div>
                        </div>

                        <?php if((count($closeEvents['montage'])>0 || count($plannedVacations['montage'])>0) && count($overlapingEvents['montage'])==0 && count($vacations['montage'])==0){ ?>
                        <div class="row">
                            <div class="col-md-12 alert alert-warning">
                                <?php
                                foreach ($closeEvents['montage'] as $montageEvent) {
                                    echo Html::a($montageEvent->getTimeStart(). " - " .$montageEvent->getTimeEnd(). " ".$montageEvent->name, ['event/view', 'id' => $montageEvent->id], ['target' => '_blank'])."<br>";
                                }
                                foreach ($plannedVacations['montage'] as $vacation) {
                                    echo Html::a(Yii::t('app','Urlop'). ": " . $vacation->start_date. " - " .$vacation->end_date, ['vacation/view', 'id' => $vacation->id], ['target' => '_blank'])."<br>";
                                }
                                ?>
                            </div>
                        </div>
                        <?php } ?>

                        <?php if(count($overlapingEvents['montage'])>0 || count($vacations['montage'])>0){ ?>
                        <div class="row">
                            <div class="col-md-12 alert alert-danger">
                                <?php
                                foreach ($overlapingEvents['montage'] as $montageEvent) {
                                    echo Html::a($montageEvent->getTimeStart(). " - " .$montageEvent->getTimeEnd(). " ".$montageEvent->name, ['event/view', 'id' => $montageEvent->id], ['target' => '_blank'])."<br>";
                                }
                                foreach ($vacations['montage'] as $vacation) {
                                    echo Html::a(Yii::t('app','Urlop'). ": " . $vacation->start_date. " - " .$vacation->end_date, ['vacation/view', 'id' => $vacation->id], ['target' => '_blank'])."<br>";
                                }
                                ?>
                            </div>
                        </div>
                        <?php } ?>

                        <?php
                    }
                    ?>

                    <?php
                    if ($model->event_start && $model->event_end) {
                        $classEvent = null;
                        $disableEvent = null;
                        if(count($closeEvents['event'])>0 || count($plannedVacations['event'])>0) {
                            $classEvent = 'alert alert-warning';
                        }
                        if(count($overlapingEvents['event'])>0 || count($vacations['event'])>0) {
                            $classEvent = 'alert alert-danger';
                            $disableEvent = 'disabled';
                        }
                        ?>
                        <div class="row <?= $classEvent ?>" style="margin-bottom: 0; border-bottom: 0;">
                            <div class="col-md-3">
                                <label>
                                    <?= Html::checkbox('workWholeEvent', $checked_event, ['value' => 1, 'disabled' => $disableEvent]) ?>
                                    <?= Yii::t('app', 'Wydarzenie') ?>
                                </label>
                            </div>
                            <div class="col-md-8">
                                <?= substr($model->event_start, 0, strlen($model->event_start) - 3) . " - " . substr($model->event_end, 0, strlen($model->event_end) - 3) ?>
                            </div>
                        </div>

                        <?php if((count($closeEvents['event'])>0 || count($plannedVacations['event'])>0) && count($overlapingEvents['event'])==0 && count($vacations['event'])==0){ ?>
                            <div class="row">
                                <div class="col-md-12 alert alert-warning" style="margin: ">
                                    <?php
                                    foreach ($closeEvents['event'] as $montageEvent) {
                                        echo Html::a($montageEvent->getTimeStart(). " - " .$montageEvent->getTimeEnd(). " ".$montageEvent->name, ['event/view', 'id' => $montageEvent->id], ['target' => '_blank'])."<br>";
                                    }
                                    foreach ($plannedVacations['event'] as $vacation) {
                                        echo Html::a(Yii::t('app','Urlop'). ": " . $vacation->start_date. " - " .$vacation->end_date, ['vacation/view', 'id' => $vacation->id], ['target' => '_blank'])."<br>";
                                    }
                                    ?>
                                </div>
                            </div>
                        <?php } ?>

                        <?php if(count($overlapingEvents['event'])>0 || count($vacations['event'])>0){ ?>
                            <div class="row">
                                <div class="col-md-12 alert alert-danger">
                                    <?php
                                    foreach ($overlapingEvents['event'] as $montageEvent) {
                                        echo Html::a($montageEvent->getTimeStart(). " - " .$montageEvent->getTimeEnd(). " ".$montageEvent->name, ['event/view', 'id' => $montageEvent->id], ['target' => '_blank'])."<br>";
                                    }
                                    foreach ($vacations['event'] as $vacation) {
                                        echo Html::a(Yii::t('app','Urlop'). ": " . $vacation->start_date. " - " .$vacation->end_date, ['vacation/view', 'id' => $vacation->id], ['target' => '_blank'])."<br>";
                                    }
                                    ?>
                                </div>
                            </div>
                        <?php } ?>

                        <?php
                    }
                    ?>

                    <?php
                    if ($model->disassembly_start && $model->disassembly_end) {
                        $classDisassembly = null;
                        $disableDisassembly = null;
                        if(count($closeEvents['disassembly'])>0 || count($plannedVacations['disassembly'])>0) {
                            $classDisassembly = 'alert alert-warning';
                        }
                        if(count($overlapingEvents['disassembly'])>0 || count($vacations['disassembly'])>0) {
                            $classDisassembly = 'alert alert-danger';
                            $disableDisassembly = 'disabled';
                        }
                        ?>
                        <div class="row <?= $classDisassembly ?>" style="margin-bottom: 0; border-bottom: 0;">
                            <div class="col-md-3">
                                <label>
                                    <?= Html::checkbox('workWholeDisassembly', $checked_disassembly, ['value' => 1, 'disabled' => $disableDisassembly]) ?>
                                    <?= Yii::t('app', 'Demontaż') ?>
                                </label>
                            </div>
                            <div class="col-md-8">
                                <?= substr($model->disassembly_start, 0, strlen($model->disassembly_start) - 3) . " - " . substr($model->disassembly_end, 0, strlen($model->disassembly_end) - 3) ?>
                            </div>
                        </div>

                        <?php if((count($closeEvents['disassembly'])>0 || count($plannedVacations['disassembly'])>0) && count($overlapingEvents['disassembly'])==0 && count($vacations['disassembly'])==0){ ?>
                            <div class="row">
                                <div class="col-md-12 alert alert-warning" style="margin: ">
                                    <?php
                                    foreach ($closeEvents['disassembly'] as $montageEvent) {
                                        echo Html::a($montageEvent->getTimeStart(). " - " .$montageEvent->getTimeEnd(). " ".$montageEvent->name, ['event/view', 'id' => $montageEvent->id], ['target' => '_blank'])."<br>";
                                    }
                                    foreach ($plannedVacations['disassembly'] as $vacation) {
                                        echo Html::a(Yii::t('app','Urlop'). ": " . $vacation->start_date. " - " .$vacation->end_date, ['vacation/view', 'id' => $vacation->id], ['target' => '_blank'])."<br>";
                                    }
                                    ?>
                                </div>
                            </div>
                        <?php } ?>

                        <?php if(count($overlapingEvents['disassembly'])>0 || count($vacations['disassembly'])>0){ ?>
                            <div class="row">
                                <div class="col-md-12 alert alert-danger">
                                    <?php
                                    foreach ($overlapingEvents['disassembly'] as $montageEvent) {
                                        echo Html::a($montageEvent->getTimeStart(). " - " .$montageEvent->getTimeEnd(). " ".$montageEvent->name, ['event/view', 'id' => $montageEvent->id], ['target' => '_blank'])."<br>";
                                    }
                                    foreach ($vacations['disassembly'] as $vacation) {
                                        echo Html::a(Yii::t('app','Urlop'). ": " . $vacation->start_date. " - " .$vacation->end_date, ['vacation/view', 'id' => $vacation->id], ['target' => '_blank'])."<br>";
                                    }
                                    ?>
                                </div>
                            </div>
                        <?php } ?>

                        <?php
                    }
                    ?>

                    <div class="row">
                        <div class="col-md-12" id="modal_breaks_grid">
                            <?php
                            $event_id = $model->id;
                            echo GridView::widget(['dataProvider' => $userWorkingHoursDataProvider,
                                    'layout'=>'{items}',
                                'columns' => ['start_time', 'end_time',
                                    ['class' => 'yii\grid\ActionColumn', 'template' => '{delete}',
                                        'buttons' => ['delete' => function ($url, $model) use ($user_id, $event_id) {
                                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::toRoute(['planboard/delete-user-working-hours',
                                                'id' => $model->id, 'user_id' => $user_id,
                                                'event_id' => $event_id]), ['class' => 'delete_working_hours']);

                                        },]],],]);
                            ?>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-2">
                            <label>
                                <?= Yii::t('app', 'Dodaj godziny pracy') ?>:
                            </label>
                        </div>
                        <div class="col-md-8">
                            <?php

                            $start = $end = null;
                            if ($model->montage_start) {
                                $start = $model->montage_start;
                            }
                            else if ($model->event_start) {
                                $start = $model->event_start;
                            }
                            else if ($model->disassembly_start) {
                                $start = $model->disassembly_start;
                            }

                            if ($model->disassembly_end) {
                                $end = $model->disassembly_end;
                            }
                            else if ($model->event_end) {
                                $end = $model->event_end;
                            }
                            else if ($model->montage_end) {
                                $end = $model->montage_end;
                            }

                            echo DateRangePicker::widget([
                                'name' => 'eventRange',
                                'convertFormat' => true,
                                'pluginOptions' => [
                                    'timePicker' => true,
                                    'timePickerIncrement' => 5,
                                    'timePicker24Hour' => true,
                                    'locale' => [
                                        'format' => 'Y-m-d H:i'
                                    ],
                                    'linkedCalendars'=>false,
                                    'minDate' => $start,
                                    'maxDate' => $end,
                                    'startDate' => $start,
                                    'endDate' => $end
                                ],
                                'options' => [
                                    'id' => 'working-hours-daterange',
                                    'style' => 'width: 300px;',
                                ],
                            ]) ?>
                        </div>
                    </div>

                    <?php if (Yii::$app->user->can('eventsEventEditEyeCrewEdit')) { ?>
                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => 'btn btn-success',
                            'id' => 'btn-add-workin-hours']) ?>
                    </div>
                    <?php } ?>
                </div>
            </div>
        <?php endif; ?>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading"><h4><?php echo Yii::t('app', 'Czas wolny:'); ?></h4></div>
        <div class="panel-body">


            <h4><?php echo Yii::t('app', '1. Przerwy własne'); ?></h4>


            <div class="row">
                <div class="col-md-12" id="modal_breaks_grid">
                    <?php
                    echo GridView::widget(['dataProvider' => $userBreaksDataProvider, 'showFooter' => true,
                        'columns' => [
                            [
                                'attribute' => 'name',
                                'header' => Yii::t('app', 'Nazwa'),
                                'footer' => Html::input('text', 'user_break_name', null, ['class' => 'break-date-time-picker form-control']),
                            ],
                            ['attribute' => 'start_time', 'header' => Yii::t('app', 'Początek'),
                                'footer' => DateTimePicker::widget(['name' => 'user_break_start',
                                    'type' => DateTimePicker::TYPE_INPUT,
                                    'pluginOptions' => ['autoclose' => true, 'format' => 'yyyy-mm-dd hh:ii'],
                                    'options' => ['id' => 'break_start', 'class' => 'break-date-time-picker']]),],
                            ['attribute' => 'end_time', 'header' => Yii::t('app', 'Koniec'),
                                'footer' => DateTimePicker::widget(['name' => 'user_break_end',
                                    'type' => DateTimePicker::TYPE_INPUT,
                                    'pluginOptions' => ['autoclose' => true, 'format' => 'yyyy-mm-dd hh:ii'],
                                    'options' => ['id' => 'break_end', 'class' => 'break-date-time-picker']]),],
                            ['attribute' => 'icon', 'value' => function ($model) {
                                $eventBreak = new EventBreaks();
                                $iconList = $eventBreak->getIconsArray();
                                return Html::icon($iconList[$model->icon]);
                            }, 'format' => 'html', 'header' => Yii::t('app', 'Ikonka'),
                                'footer' => Select2::widget(['name' => 'user_break_icon', 'data' => $iconsArray,
                                    'options' => ['placeholder' => Yii::t('app', 'wybierz'),
                                        'id' => 'break-icon-select',], 'pluginOptions' => ['allowClear' => true,
                                        'templateResult' => new JsExpression('format'),
                                        'templateSelection' => new JsExpression('format'),
                                        'escapeMarkup' => $escape,],]),],
                            ['class' => 'yii\grid\ActionColumn', 'template' => '{delete}',
                                'buttons' => ['delete' => function ($url, $model) use ($user_id, $event_id) {
                                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::toRoute(['planboard/delete-user-break',
                                        'id' => $model->id, 'user_id' => $user_id,
                                        'event_id' => $event_id]), ['title' => Yii::t('app', 'Usuń'),
                                        'data-confirm' => Yii::t('app', 'Czy na pewno usunąć tą przerwę w pracy?'),
                                        'data-method' => 'post',]);

                                }],
                            ]
                        ]]);
                    if (Yii::$app->user->can('eventsEventEditEyeCrewEdit')) {
                        echo Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => 'btn btn-success']);
                    }
                    ?>

                </div>
            </div>


            <h4><?php echo Yii::t('app', '2. Przerwy eventowe'); ?></h4>
            <div class="row">
                <div class="col-md-12" id="modal_breaks_grid">
                    <?php
                    echo GridView::widget(['dataProvider' => $model->getEventBreaksDataProvider(),

                        'columns' => [
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'header' => '',
                                'checkboxOptions' => function ($item, $key, $index, $column) use ($assignedBreks) {
                                    return ['checked' => isset($assignedBreks[$item->id])];
                                }],
                            'name', ['attribute' => 'Początek', 'value' => function ($model) {
                                return Yii::$app->formatter->asDatetime($model->start_time, 'short');
                            }], ['attribute' => 'Koniec', 'value' => function ($model) {
                                return Yii::$app->formatter->asDatetime($model->end_time, 'short');
                            }], ['attribute' => 'icon', 'value' => function ($model) {
                                $list = $model->getIconsArray();
                                return Html::icon($list[$model->icon]);
                            }, 'label' => Yii::t('app', 'Ikonka'), 'format' => 'html',],],]);
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?php if (Yii::$app->user->can('eventsEventEditEyeCrewEdit')) {
                        echo Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => 'btn btn-success', 'id' => 'ekipa_modal_reload_calendar']);
                    } ?>

            </div>
            <div class="pull-right">
                <?= Html::button(Yii::t('app', 'Zamknij'), ['class' => 'btn btn-primary', 'id' => 'close-modal-btn']) ?>
            </div>
        </div>
    </div>

<?php ActiveForm::end(); ?>

<?php

$this->registerJs('

$("#ekipa_modal").on("hidden.bs.modal", function () {
    $("body").find("#ekipa_modal").find(".modalContent").html("");
    $.pjax({container: "#pjax-grid-view"}).done(function(){
        $("body").find(".table-bordered").each(function(){
            $(this).removeClass("table-bordered");
        });
        $("body").find(".table-striped").each(function(){
            $(this).removeClass("table-striped");
        });
    
    });
});

$("#close-modal-btn").click(function(){
     $("body").find("#ekipa_modal").modal("hide");
});

$("#add_working_time_form").on("beforeSubmit", function(e) {
    $.post($(this).attr("action"), $(this).serialize(), function(resp){
            if (resp.error) {
                alert(resp.error);
            }
        })
        .done(function(result) {
            reloadModal();
        })
        .fail(function() {
            alert("Error.");
        }
    );
}).on("submit", function(e){
    e.preventDefault();
});

$(".delete_working_hours").click(function(e){
    e.preventDefault();
    
    var url = $(this).attr("href");
    if (confirm("Czy na pewno usunąć te godziny pracy?")) {
        $.ajax({
            url: url,
            async: false,
            success: function(resp) {
                console.log(resp);
            }
        });
        reloadModal();
    }    
});


function reloadModal() {
    var modal = $("body").find("#ekipa_modal .modalContent");
    modal.html("");
    modal.load("' . Url::to(["event/user-form"]) . '?event_id=' . $model->id . '&user_id=' . $user_id . '");
}


', View::POS_READY);

$this->registerCss('

.panel-primary { background-color: white !important;}
.select2-dropdown { z-index: 5050; }

');
