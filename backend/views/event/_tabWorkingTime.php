<?php

use backend\modules\permission\models\BasePermission;
use common\models\User;
use yii\bootstrap\Html;
use common\components\grid\GridView;
use kartik\widgets\ActiveForm;
$isTaskVisible = false;
/* @var $model \common\models\Event; */
/* @var $addon \common\models\EventUserAddon */
/* @var $workingTime \common\models\EventUserWorkingTime */
$user = Yii::$app->user;
$userPermission = $user->id;
if ($user->can('eventsEventEditEyeWorkingHours'.BasePermission::SUFFIX[BasePermission::ALL])) {
    $userPermission = null;
}
$isInEvent = \common\models\EventUser::find()->where(['event_id'=>$model->id, 'user_id'=>$user->id])->count();
?>
<div class="panel-body">

<!--    Godziny pracy-->
<?php if ($user->can('eventsEventEditEyeWorkingHoursWorkingHours')) { ?>

    <h3><?php echo Yii::t('app', 'Godziny pracy'); ?></h3>
    
    <div class="row">
        <div class="col-md-9">
        <?php if ($isInEvent){ ?>
        <?php if ($user->can('eventsEventEditEyeWorkingHoursUserAdd')) { 
        if ((!$model->getBlocks('working'))||(Yii::$app->user->can('eventEventBlockWorking'))) { ?>
            <?php $form = ActiveForm::begin([
                'type'=>ActiveForm::TYPE_INLINE,
                'action'=>['view', 'id'=>$model->id, '#'=>'tab-working-time']
            ]) ?>
            <?php echo $form->errorSummary($workingTime); ?>
            <?php echo $form->field($workingTime, 'dateRange')->widget(\common\widgets\DateRangeField::className()); ?>
            <?php echo $form->field($workingTime, 'department_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\Department::getModelList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Dział...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
            ?>
            <?php echo $form->field($workingTime, 'roleIds')->widget(\kartik\widgets\Select2::className(), [
                'data' => $model->getCompatibilityRoleList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Role...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
            ?>
            <?php if ($model->type==1){ ?>
            <?php echo $form->field($workingTime, 'type')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\EventUserWorkingTime::getTypes(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Okres...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
            $isTaskVisible = false;
            ?>
            <?php }else{ ?>
            <?php 
                $workingTime->type = 3; 
                echo $form->field($workingTime, 'type')->hiddenInput()->label(false);
                echo $form->field($workingTime, 'task_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => $model->getTaskList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'wybierz zadanie...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
                $isTaskVisible = true;
            ?>
            <?php } ?>
            
                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app', 'Dodaj'), ['class' => 'btn btn-success add-working-time-button']) ?>
                </div>
            
            <?php ActiveForm::end(); ?>
            <?php } ?>
            <?php } ?>
            <?php } ?>
        </div>
        <div class="col-md-3">
        <?php if ($user->can('usersPayments')){ ?>
        <?=Html::a(Yii::t('app', 'Dodaj godziny pracownikowi'), ['/event-user-working-time/create', 'id'=>$model->id, 'admin'=>1], ['class'=>'btn btn-success'])?>
        <?php } ?>
        </div>
    </div>

    <?php $periods = [1=>Yii::t('app', 'Pakowanie'), 2=>Yii::t('app', 'Montaż'), 3=>Yii::t('app', 'Event'), 4=>Yii::t('app', 'Demontaż')];
    foreach ($periods as $p=>$p_name){ ?>
    <div class="row">
        <div class="col-md-12">
                 <div class="ibox float-e-margins">
                    <div class="ibox-content">
                    <h3><?=$p_name?></h3>
            <?php
                echo GridView::widget([
                    'dataProvider'=>$model->getUserWorkingTimes($p),
                    'tableOptions' => [
                        'class' => 'kv-grid-table table table-condensed kv-table-wrap'
                    ],
                    'rowOptions' => function ($time) use ($model, $p) {
                        if ($p==1)
                        {
                                $start = $model->packing_start;
                                $end = $model->packing_end;
                        }
                        if ($p==2)
                        {
                                $start = $model->montage_start;
                                $end = $model->montage_end;
                        }
                        if ($p==3)
                        {
                                $start = $model->event_start;
                                $end = $model->event_end;
                        }
                        if ($p==4)
                        {
                                $start = $model->disassembly_start;
                                $end = $model->disassembly_end;
                        }
                    if ($end)
                    {
                        $end = new DateTime($end);
                        $start = new DateTime($start);
                        $end->modify('+2 hours');
                        $start->modify('-2 hours');
                        if (($end->format('Y-m-d H:i:s') < $time->end_time)||($start->format('Y-m-d H:i:s')> $time->start_time)) {
                                return ['style' => 'background-color:#ed556578'];
                        }
                    }

                    },
                    'columns' => [
                        [
                            'class'=>\yii\grid\SerialColumn::className(),
                        ],
                        [
                            'label' => Yii::t('app', 'Użytkownik'),
                            'attribute'=>'user_id',
                            'value'=>function($model) {
                                return $model->user->getDisplayLabel();
                            },
                            'visible'=>Yii::$app->user->can('eventsEventEditEyeWorkingHours'.BasePermission::SUFFIX[BasePermission::ALL]),


                        ],
                        'department.name:text:'.Yii::t('app', 'Dział'),
                        [
                            'attribute' => 'start_time',
                            'value' => function ($model) {
                                return Yii::$app->formatter->asDatetime($model->start_time, 'short');
                            }
                        ],
                        [
                            'attribute' => 'end_time',
                            'value' => function ($model) {
                                return Yii::$app->formatter->asDatetime($model->end_time, 'short');
                            }
                        ],
                        'duration:duration:'.Yii::t('app', 'Il. czasu'),
                        [
                            'attribute'=>'roleIds',
                            'value'=>function($model)
                            {
                                $list = \common\helpers\ArrayHelper::map($model->roles, 'id', 'name');
                                return implode('<br />', $list);
                            },
                            'label' => Yii::t('app', 'Role'),
                            'format'=>'html',
                        ],
                        [
                        'attribute'=>'task_id',
                        'value'=>function($model)
                        {
                            if ($model->task_id)
                            {
                                return $model->task->title;
                            }else{
                                return "-";
                            }
                        },
                        'visible'=>$isTaskVisible    
                        ],
                        [
                            'class'=>\common\components\ActionColumn::className(),
                            'controllerId'=>'event-user-working-time',
                            'template'=>'{update}{delete}',
                            'visibleButtons' => [
                                'update' => ($user->can('eventsEventEditEyeWorkingHoursUserEdit')&&(((!$model->getBlocks('working'))||(Yii::$app->user->can('eventEventBlockWorking'))))),
                                'delete' => ($user->can('eventsEventEditEyeWorkingHoursUserDelete')&&(((!$model->getBlocks('working'))||(Yii::$app->user->can('eventEventBlockWorking'))))),
                            ]
                        ]
                    ],
                ]) ?>
                <h5><?=Yii::t('app', 'Podsumowanie')." ".$p_name?></h5>
                <table class="table table-striped">
                <?php
                $sum_h = 0;
                foreach ($model->getSumUserWorkingTimes($p) as $u)
                { ?>
                    <tr><td><?=$u['name']?></td><td><?=sprintf("%02d%s%02d%s", floor($u['hours']/3600), "h ", ($u['hours']/60)%60, "m");?></td></tr>
                <?php
                $sum_h+=$u['hours'];
                }
                ?>
                <tr style="background-color:#eee;"><td><?=Yii::t('app', 'Łącznie')?></td><td><?=sprintf("%02d%s%02d%s", floor($sum_h/3600), "h ", ($sum_h/60)%60, "m")?></td></tr>
                </table>
                </div>
            </div>
        </div>
    </div>
<?php } }?>


<!--    Koszty-->
<?php if ($user->can('eventsEventEditEyeWorkingHoursCosts')) { ?>
    <div class="row">
        <div class="col-md-12">
            <?php
            if ($isInEvent){ 
            if ($user->can('eventsEventEditEyeWorkingHoursCostsAdd')) {
                if ((!$model->getBlocks('working'))||(Yii::$app->user->can('eventEventBlockWorking'))) { 
                echo Html::a(Yii::t('app', 'Dodaj koszt'), ['event-user-addon/create', 'id' => $model->id], ['class' => 'btn btn-success']);
            } } }?>
            <?php if ($user->can('usersPayments')){ 
                echo Html::a(Yii::t('app', 'Dodaj koszt pracownikowi'), ['event-user-addon/create', 'id' => $model->id, 'admin'=>1], ['class' => 'btn btn-success']);
            }
                ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">

                 <div class="ibox float-e-margins">
                    <div class="ibox-title  newsystem-bg">
                        <h5><?php echo Yii::t('app', 'Koszty'); ?></h5>
                    </div>
                    <div class="ibox-content">
            <?php
            echo GridView::widget([
                'dataProvider'=>$model->getAddons($userPermission),
                'tableOptions' => [
                    'class' => 'kv-grid-table table table-condensed kv-table-wrap'
                ],
                'layout' => '{items}',
                'columns' => [
                    [
                        'class'=>\yii\grid\SerialColumn::className(),
                    ],
                    'user.displayLabel:text:'.Yii::t('app', 'Użytkownik'),
                    'name',
                    'amount:currency',
                    'timeRange:text:'.Yii::t('app', 'Daty'),
                    [
                        'class'=>\common\components\ActionColumn::className(),
                        'controllerId'=>'event-user-addon',
                        'template'=>'{update}{delete}',
                        'visibleButtons' => [
                            'update' => ($user->can('eventsEventEditEyeWorkingHoursCostsEdit')&&(((!$model->getBlocks('working'))||(Yii::$app->user->can('eventEventBlockWorking'))))),
                            'delete' => ($user->can('eventsEventEditEyeWorkingHoursCostsDelete')&&(((!$model->getBlocks('working'))||(Yii::$app->user->can('eventEventBlockWorking'))))),
                        ]
                    ]
                ],
            ])
            ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>


<!--Diety-->
<?php if ($user->can('eventsEventEditEyeWorkingHoursDiet')) { ?>
    <div class="row">
        <div class="col-md-12">
            <?php if ($isInEvent){ 
            if ($user->can('eventsEventEditEyeWorkingHoursDietAdd')) {
                if ((!$model->getBlocks('working'))||(Yii::$app->user->can('eventEventBlockWorking'))) { 
                echo Html::a(Yii::t('app', 'Dodaj dietę'), ['event-user-allowance/create', 'id' => $model->id], ['class' => 'btn btn-success']);
            } } }?>
            <?php if ($user->can('usersPayments')){ 
                echo Html::a(Yii::t('app', 'Dodaj dietę pracownikowi'), ['event-user-allowance/create', 'id' => $model->id, 'admin'=>1], ['class' => 'btn btn-success']);
            }
                ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">

                 <div class="ibox float-e-margins">
                    <div class="ibox-title  newsystem-bg">
                        <h5><?php echo Yii::t('app', 'Diety'); ?></h5>
                    </div>
                    <div class="ibox-content">
            <?php
            echo GridView::widget([
                'dataProvider'=>$model->getAllowances($userPermission),
                'tableOptions' => [
                    'class' => 'kv-grid-table table table-condensed kv-table-wrap'
                ],
                'layout' => '{items}',
                'columns' => [
                    [
                        'class'=>\yii\grid\SerialColumn::className(),
                    ],
                    'user.displayLabel:text:'.Yii::t('app', 'Użytkownik'),
                    'amount:currency',
                    'typeLabel:text:'.Yii::t('app', 'Rodzaj'),
                    'timeRange:text:'.Yii::t('app', 'Daty'),
                    [
                        'class'=>\common\components\ActionColumn::className(),
                        'controllerId'=>'event-user-allowance',
                        'template'=>'{update}{delete}',
                        'visibleButtons' => [
                            'update' => ($user->can('eventsEventEditEyeWorkingHoursDietEdit')&&(((!$model->getBlocks('working'))||(Yii::$app->user->can('eventEventBlockWorking'))))),
                            'delete' => ($user->can('eventsEventEditEyeWorkingHoursDietDelete')&&(((!$model->getBlocks('working'))||(Yii::$app->user->can('eventEventBlockWorking'))))),
                        ]
                    ]
                ],
            ])
            ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<!--    Podsumowanie-->
<?php if ($user->can('eventsEventEditEyeWorkingHoursSummary')) { ?>
    <div class="row">
        <div class="col-md-12">
            <?php $summary = $model->getWorkingTimeSummaryForEventTab($user->id, true); ?>
                 <div class="ibox float-e-margins">
                    <div class="ibox-title  newsystem-bg">
                        <h5><?php echo Yii::t('app', 'Podsumowanie'); ?></h5>
                    </div>
                    <div class="ibox-content">
            <dl class="dl-horizontal">
                <?php if (!$user->can('eventsEventEditEyeWorkingHours'.BasePermission::SUFFIX[BasePermission::ALL])) { ?>
                    <dt><?php echo Yii::t('app', 'Stawka'); ?></dt>
                    <dd><?php echo $summary['rate'] . " / " . User::getRateList()[$user->getIdentity()->rate_type] ?></dd>
                <?php } ?>
                <?php if ($user->getIdentity()->rate_type != User::RATE_MONTH) { ?>
                    <dt><?php echo Yii::t('app', 'Suma za przepracowane godziny'); ?></dt>
                    <dd><?php echo $summary['salary']; ?></dd>
                <?php } ?>
                <dt><?php echo Yii::t('app', 'Koszty'); ?></dt>
                <dd><?php echo $summary['addons']; ?></dd>
                <dt><?php echo Yii::t('app', 'Diety'); ?></dt>
                <dd><?php echo $summary['allowances']; ?></dd>
                <dt><?php echo Yii::t('app', 'Dodatki'); ?></dt>
                <dd><?php echo $summary['roleAddons']; ?></dd>
                <dt><?php echo Yii::t('app', 'Suma'); ?></dt>
                <dd><?php echo $summary['sum']; ?></dd>
                <dt><?php echo Yii::t('app', 'Brutto'); ?></dt>
                <dd><?php echo $summary['brutto']; ?></dd>
            </dl>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

</div>

<?php


if (\Yii::$app->params['companyID']=='e4e')
{


                                $_start_p = $model->packing_start;
                                $_end_p = $model->packing_end;
                                $_start_m = $model->montage_start;
                                $_end_m = $model->montage_end;
                                $_start_e = $model->event_start;
                                $_end_e = $model->event_end;
                                $_start_d = $model->disassembly_start;
                                $_end_d = $model->disassembly_end;
                                
                                $end_p = new DateTime($_end_p);
                                $start_p = new DateTime($_start_p);
                                $end_p->modify('+2 hours');
                                $start_p->modify('-2 hours');
                                
                                $end_m = new DateTime($_end_m);
                                $start_m = new DateTime($_start_m);
                                $end_m->modify('+2 hours');
                                $start_m->modify('-2 hours');
                                
                                $end_e = new DateTime($_end_e);
                                $start_e = new DateTime($_start_e);
                                $end_e->modify('+2 hours');
                                $start_e->modify('-2 hours');
                                
                                $end_d = new DateTime($_end_d);
                                $start_d = new DateTime($_start_d);
                                $end_d->modify('+2 hours');
                                $start_d->modify('-2 hours');

                                $end_p2 = new DateTime($_end_p);
                                $start_p2 = new DateTime($_start_p);
                                $end_p2->modify('+4 hours');
                                $start_p2->modify('-4 hours');
                                
                                $end_m2 = new DateTime($_end_m);
                                $start_m2 = new DateTime($_start_m);
                                $end_m2->modify('+4 hours');
                                $start_m2->modify('-4 hours');
                                
                                $end_e2 = new DateTime($_end_e);
                                $start_e2 = new DateTime($_start_e);
                                $end_e2->modify('+4 hours');
                                $start_e2->modify('-4 hours');
                                
                                $end_d2 = new DateTime($_end_d);
                                $start_d2 = new DateTime($_start_d);
                                $end_d2->modify('+4 hours');
                                $start_d2->modify('-4 hours');

$this->registerJs('
$(".add-working-time-button").click(function(e){
    e.preventDefault();
    var form = $(this).closest("form");
    var start = form.find("#eventuserworkingtime-daterange-start").val();
    var end = form.find("#eventuserworkingtime-daterange-end").val();
    var type = form.find("#eventuserworkingtime-type").val();
    var start_m = "'.$start_m->format('Y-m-d H:i:s').'";
    var end_m = "'.$end_m->format('Y-m-d H:i:s').'";
    var start_p = "'.$start_p->format('Y-m-d H:i:s').'";
    var end_p = "'.$end_p->format('Y-m-d H:i:s').'";
    var start_d = "'.$start_d->format('Y-m-d H:i:s').'";
    var end_d = "'.$end_d->format('Y-m-d H:i:s').'";
    var start_e = "'.$start_e->format('Y-m-d H:i:s').'";
    var end_e = "'.$end_e->format('Y-m-d H:i:s').'";
    var start_m2 = "'.$start_m2->format('Y-m-d H:i:s').'";
    var end_m2 = "'.$end_m2->format('Y-m-d H:i:s').'";
    var start_p2 = "'.$start_p2->format('Y-m-d H:i:s').'";
    var end_p2 = "'.$end_p2->format('Y-m-d H:i:s').'";
    var start_d2 = "'.$start_d2->format('Y-m-d H:i:s').'";
    var end_d2 = "'.$end_d2->format('Y-m-d H:i:s').'";
    var start_e2 = "'.$start_e2->format('Y-m-d H:i:s').'";
    var end_e2 = "'.$end_e2->format('Y-m-d H:i:s').'";
    var show = false;
    var show2 = false;
    if (type==1)
    {
        if ((start_p>start)||(end_p<end))
        {
            show = true;
        }
        if ((start_p2>start)||(end_p2<end))
        {
            show2 = true;
        }
    }
    if (type==2)
    {
        if ((start_m>start)||(end_m<end))
        {
            show = true;
        }
                if ((start_m2>start)||(end_m2<end))
        {
            show2 = true;
        }
    }
    if (type==3)
    {
        if ((start_e>start)||(end_e<end))
        {
            show = true;
        }
        if ((start_e2>start)||(end_e2<end))
        {
            show2 = true;
        }
    }
    if (type==4)
    {
        if ((start_d>start)||(end_d<end))
        {
            show = true;
        }
        if ((start_e2>start)||(end_e2<end))
        {
            show2 = true;
        }
    }
    if (show2)
    {
                swal({
                    title: "'.Yii::t('app', 'Dodane godziny znacząco wykraczają poza godziny eventu. Dlatego niemożliwe jest ich dodanie.').'",
                    icon:"info",
                  buttons: {
                    cancel: "'.Yii::t('app', 'Zamknij').'",
                  },
                });
    }else{
            if (show)
            {
                swal({
                    title: "'.Yii::t('app', 'Dodane godziny znacząco wykraczają poza godziny eventu. Czy na pewno chcesz dodać?').'",
                    icon:"info",
                  buttons: {
                    cancel: "'.Yii::t('app', 'Nie').'",
                    yes: {
                      text: "'.Yii::t('app', 'Tak').'",
                      value: "yes",
                    },
                  },
                })
                .then((value) => {
                  switch (value) {
                 
                    case "yes":
                      form.submit();
                      break;       
                  }
                });
            }else{
                form.submit();
            }
    }

});
    ');
}