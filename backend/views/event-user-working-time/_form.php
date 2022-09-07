<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\EventUserWorkingTime */
/* @var $form yii\widgets\ActiveForm */
?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<div class="event-user-working-time-form">
<div class="alert alert-danger">
                     <?=Yii::t('app', 'Zwróć uwagę, aby daty pokrywały się z czasem trwania wydarzenia nn!') ?>
</div>
    <?php $form = ActiveForm::begin(['id'=>'workingForm']); ?>

    <?php if (!$model->user_id){
        echo $form->field($model, 'user_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => $model->event->getUserList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ])->label(Yii::t('app', 'Pracownik'));
            
    } ?>

    <?= $form->field($model, 'dateRange')->widget(\common\widgets\DateRangeField::className())->label(Yii::t('app', 'Termin')); ?>
    <?php echo $form->field($model, 'department_id')->widget(\kartik\widgets\Select2::className(), [
        'data' => \common\models\Department::getModelList(),
        'options' => [
            'placeholder' => Yii::t('app', 'Wybierz...'),
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'multiple' => false,
        ],
    ]);
    ?>
    <?php echo $form->field($model, 'roleIds')->widget(\kartik\widgets\Select2::className(), [
        'data' => \common\models\UserEventRole::getList(),
        'options' => [
            'placeholder' => Yii::t('app', 'Wybierz...'),
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'multiple' => false,
        ],
    ]);
    ?>


    <?php if ($model->event->type!=1){ ?>
            <?php 
                echo $form->field($model, 'task_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => $model->event->getTaskList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'wybierz zadanie...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
            ?>
            <?php } ?>
            <?php echo $form->field($model, 'type')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\EventUserWorkingTime::getTypes(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Okres...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
            ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id'=>'working-form-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
if (\Yii::$app->params['companyID']=='e4e')
{
    $model = $model->event;

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
$("#working-form-button").on("click", function(e) {
    e.preventDefault();
    var form = $(this).closest("form");
    var formData = form.serialize();
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