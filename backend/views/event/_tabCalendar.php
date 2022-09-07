<?php
use yii\bootstrap\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use common\models\EventUserPlannedWrokingTime;
use yii\bootstrap\Modal;

/* @var $model \common\models\Event; */

Modal::begin([
    'id' => 'schedule-modal',
    'header' => Yii::t('app', 'Edytuj harmonogram'),
    'options' => [
        'tabindex' => false,
    ],
    'clientOptions' => [
    'keyboard'=> false,
        'backdrop'=> 'static'
    ]
]);
Modal::end();

$this->registerJs('
    $(".add-schedule").click(function(e){
        e.preventDefault();
        $("#schedule-modal").find(".modal-body").empty();
        $("#schedule-modal").modal("show").find(".modal-body").load($(this).attr("href"));
    });
    $(".add-schedule").on("contextmenu",function(){
       return false;
    }); 
');
?>
<div class="panel-body">
<div class="row">
<div class="col-lg-6">
<?php if (Yii::$app->user->can('eventEventEditEyeDescriptionEdit')) { ?>
<p>
        <?php echo Html::a(Yii::t('app', 'Dodaj notatkę'), ['/customer-note/create',  'customer_id'=>$model->customer_id, 'contact_id'=>$model->contact_id, 'event_id'=>$model->id], ['class'=>'btn btn-success']); ?>
</p>
<?php } ?>
<?php 
if (count($model->customerNotes)>0){ ?>
<h3><?php echo Yii::t('app', 'Notatki'); ?></h3>
<div>
                                <div class="feed-activity-list">
                                 <?php       
                                    foreach ($model->customerNotes as $m)
                                        {
                                            if ($m->toShow()){
                                        ?>
                                        <div class="feed-element">
                                        <a href="#" class="pull-left">
                                            <?=$m->user->getUserPhoto("img-circle")?>
                                        </a>
                                        <div class="media-body ">
                                            <small class="pull-right text-navy"><?=$m->datetime?></small>
                                            <strong><?=$m->user->displayLabel?>:<br></strong><?=$m->name?></br>
                                            <small class="text-muted"><?=$m->type?></small><br>
                                            <small class="text-muted"><?=Yii::t('app', 'Załączniki: ')?><?php echo Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj'), ['/customer-note/add-file', 'id'=>$m->id]); ?></small><br>
                                            <?php foreach ($m->clientNoteAttachments as $a){ ?>
                                            <small class="text-muted"><?=Html::a('<i class="fa fa-file"></i> '.$a->filename, $a->getFileUrl())?></small> <?=Html::a('<i class="fa fa-trash"></i> ', ['/customer-note/delete-file', 'id'=>$a->id], [ 'data' => ['confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'), 'method' => 'post']])?><br/>
                                            <?php } ?>
                                            <div class="actions">
                                            <?php if (isset($m->contact)){ echo Html::a('<i class="fa fa-user"></i> '.$m->contact->displayLabel, ['/contact/view', 'id'=>$m->contact_id], ['class'=>'btn btn-xs btn-warning']); } ?>
                                            <?php if ($m->user_id == Yii::$app->user->id) { ?>
                                            <?php echo Html::a('<i class="fa fa-pencil"></i> '.Yii::t('app', 'Edytuj'), ['/customer-note/update', 'id'=>$m->id], ['class'=>'btn btn-xs btn-success']); ?>
                                            <?php echo Html::a('<i class="fa fa-trash"></i> '.Yii::t('app', 'Usuń'), ['/customer-note/delete', 'id'=>$m->id], ['class'=>'btn btn-xs btn-danger', 'data' => ['confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'), 'method' => 'post']]); ?>
                                            <?php } ?>
                                            </div>
                                        </div>
                                        </div>
                                        <?php
                                        }   }
                                    ?>                                
                                </div>
                            </div>
<?php } ?>
<h3><?php echo Yii::t('app', 'Opis'); ?></h3>

<?php if (Yii::$app->user->can('eventEventEditEyeDescriptionEdit')) { ?>
        <div id="event-description-form">
            <?php $form = ActiveForm::begin([
                'action'=>['event/update', 'id'=>$model->id],
            ]); ?>

                <?php echo $form->field($model, 'description')->widget(\common\widgets\RedactorField::className())->label(false); ?>
                <?php echo $form->field($model, 'details')->widget(\common\widgets\RedactorField::className()); ?>

                <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => 'btn btn-success submitdescription']) ?>
            </div>
            <?php ActiveForm::end(); ?>

        </div>

        <?php 
        foreach ($model->extraFields as $field)
        {
            if ($field['type']==1)
                echo $form->field($field['field'], 'value_int')->textInput(['maxlength' => true, 'autocomplete'=>"off", 'id'=>"event-field-".$field['id'], 'data-id'=>$field['id'], 'class'=>'event-field'])->label($field['name']);
            if ($field['type']==2)
                echo $form->field($field['field'], 'value_text')->textInput(['maxlength' => true, 'autocomplete'=>"off", 'id'=>"event-field-".$field['id'], 'data-id'=>$field['id'], 'class'=>'event-field'])->label($field['name']);
            if ($field['type']==3)
            echo $form->field($field['field'], 'value_text')->textarea(['rows' => '6', 'class'=>'event-field', 'data-id'=>$field['id'],'id'=>"event-field-".$field['id']])->label($field['name']);

            }?>


<?php }
else {
    echo $model->description;
    if ($model->details)
    {
        echo "<p>".Yii::t('app', 'Szczegóły')."</p>";
        echo $model->details;
    }
    foreach ($model->extraFields as $field)
        {
            if ($field['type']==1)
            {
                ?>
                <p><b><?=$field['name']?>: </b><?=$field['field']->value_int?></p>
                <?php
            }else{
                ?>
                <p><b><?=$field['name']?>: </b><?=nl2br($field['field']->value_text)?></p>
                <?php
            }
            }
    
}
?>
</div>
<div class="col-lg-6">
<h3><?php echo Yii::t('app', 'Kalendarz'); ?> <?= Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj etap'), ['/event/add-schedule', 'id' => $model->id], [
                                                            'class' => 'btn btn-xs  btn-success add-schedule ',
                                                            
                                                        ])
                                                        ?></h3>
<div class="ibox-content" id="ibox-content">

                        <div id="vertical-timeline" class="vertical-container dark-timeline">
                        <?php 
$users = \common\helpers\ArrayHelper::map($model->getUsers()->AsArray()->all(), 'id', 'id');

                        foreach ($model->eventSchedules as $schedule){ ?>
                                <div class="vertical-timeline-block">
                                <div class="vertical-timeline-icon navy-bg">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <div class="vertical-timeline-content">
                                    <h2><?= $schedule->name ?>
                                                                            <div class="pull-right" style="text-align:right">
                                                        <?= Html::a('<i class="fa fa-pencil"></i>', ['/event/update-schedule', 'id' => $schedule->id], [
                                                            'class' => 'btn btn-xs  add-schedule',
                                                            
                                                        ])
                                                        ?>
                                                        <?= Html::a('<i class="fa fa-trash"></i>', ['/event/delete-schedule', 'id' => $schedule->id], [
                                                            'class' => 'btn btn-danger btn-xs',
                                                            'data' => [
                                                                'confirm' => Yii::t('app', 'Czy na pewno chcesz usunąć?'),
                                                                'method' => 'post',
                                                            ],
                                                        ])
                                                        ?>
                                </div>
                                    </h2>
                                    <div class="team-members pull-right">
                                    <?php 
                                        $workingTimes = EventUserPlannedWrokingTime::find()->where(['user_id' => $users])->andWhere(['event_id' => $model->id])->andWhere(['start_time'=>$schedule->start_time, 'end_time'=>$schedule->end_time])->all();
                                    foreach ($workingTimes as $u)
                                    { ?>
<a href="#"><img alt="image" class="img-circle img-small" src="<?php echo $u->user->getUserPhotoUrl();?>" title="<?=$u->user->first_name." ".$u->user->last_name; ?>"></a>
                                     <?php   } ?>
                                    </div>

                                    <span class="vertical-date">
                                        <?=substr($schedule->start_time,0,11);?>
                                        <?php if (substr($schedule->start_time,0,11)!=substr($schedule->end_time,0,11)) { echo " - ".substr($schedule->end_time,0,11);}?>
                                        <br/>
                                        <small><?=substr($schedule->start_time,11,5);?> - <?=substr($schedule->end_time,11,5);?></small>
                                    </span>
                                </div>
                            </div>
                        <?php } ?>                       
                        </div>

                    </div>
</div>
</div>
</div>

<?php
$eventsaveFieldsUrl = Url::to(['event/save-field', 'id'=>$model->id]);
$this->registerJs(
'
$(".submitdescription").click(function(e){
    e.preventDefault();
    $(this).closest("form").submit();
});
$(".event-field").change(function(){

    field_id = $(this).data("id");
    val = $(this).val();
    var data = {
        val : val,
        field_id : field_id
    }
    $.post("'.$eventsaveFieldsUrl .'", data, function(response){
        if (response.success)
        {
            toastr.success("'.Yii::t('app', 'Zapisano pomyślnie').'");
        }else{
            toastr.danger("'.Yii::t('app', 'Wystąpił błąd w zapisie').'");
        }
    });

});
'

    );