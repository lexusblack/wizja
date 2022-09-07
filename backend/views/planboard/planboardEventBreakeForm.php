<?php
use kartik\grid\GridView;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\Select2;
use kartik\daterange\DateRangePicker;
use common\models\EventBreaks;
use common\models\Event;
use yii\web\JsExpression;
use yii\web\View;
use wbraganca\dynamicform\DynamicFormWidget;

$format = <<< SCRIPT
function format(obj) {
	if (!obj.id) return obj.text;
	icon = '<span class="glyphicon glyphicon-'+obj.text+'" aria-hidden="true"></span>';
    return icon;
}
SCRIPT;
$escape = new JsExpression("function(m) { return m; }");


$addon = <<< HTML
<span class="input-group-addon">
    <i class="glyphicon glyphicon-calendar"></i>
</span>
HTML;

$form = ActiveForm::begin([	
	'id' => 'event_breakes_form_modal',
	'action' => Url::to(['planboard/event-breaks-form', 'event_id' => $event_id, 'update_event_breaks' => 1 ]),
]); ?>


<div class="panel panel-default">
		<?php DynamicFormWidget::begin([
        'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
        'widgetBody' => '.container-items', // required: css class selector
        'widgetItem' => '.item', // required: css class
        'limit' => 5, // the maximum times, an element can be cloned (default 999)
        'min' => 1, // 0 or 1 (default 1)
        'insertButton' => '.add-item', // css class
        'deleteButton' => '.remove-item', // css class
        'model' => new EventBreaks(),
        'formId' => 'event_breakes_form_modal',
        'formFields' => [
        		'id',
            'name',
            'start_time',
            'end_time',
            'icon'
        ],
    ]); ?>
    <div class="panel-heading">
    	<button type="button" class="add-item btn btn-success pull-right"><i class="glyphicon glyphicon-plus"></i></button>

    	<div class="clearfix"></div>

    </div>
    <div class="panel-body">
        <div class="container-items"><!-- widgetContainer -->
        <?php foreach ($models as $i => $model): ?>
            <div class="item panel panel-default"><!-- widgetBody -->
                <div class="panel-heading">
                    <h3 class="panel-title pull-left"><?= Yii::t('app', 'Przerwa')?></h3>
                    <div class="pull-right">
                        <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-body">
                    <?php
                        // necessary for update action.
                        if (! $model->isNewRecord) {
                            echo Html::activeHiddenInput($model, "[{$i}]id", ['class' => 'break_id']);
                        }
                    ?>

                    <div class="form-group">
										    <?php
										    $model->break_date_range = isset($model->start_time) ? $model->start_time.' - '.$model->end_time : $event->getTimeStart().' - '.$event->getTimeEnd();
										    echo $form->field($model, "[$i]break_date_range",[
										    	'addon' => [
										    		'append' => ['content'=>'<i class="glyphicon glyphicon-calendar"></i>'],
										    		'groupOptions' => ['class'=>'input-group drp-container'],
										    	],
										    	
										    	])->widget(DateRangePicker::classname(),[
												    'convertFormat'=>true,
												    'useWithAddon'=>true,
												    'pluginOptions' => [
												    	'timePicker'=>true,
							                'timePickerIncrement'=>5,
							                'timePicker24Hour' => true,
							                'locale'=>['format'=>'Y-m-d H:i:s'],
							                'minDate'=>$event->getTimeStart(),
							                'maxDate'=>$event->getTimeEnd(),
												    	],
										        ]);
										    ?>
										</div>

                    <?= $form->field($model, "[$i]name") ?>
                    <div class="form-group"> 
											<?= $form->field($model, "[$i]icon")->widget(Select2::classname(), [
											    'data' => $iconsArray,
											    'options' => ['placeholder' => Yii::t('app', 'Wybierz')],
											    'pluginOptions' => [
											      'allowClear' => true,
											      'templateResult' => new JsExpression('format'),
											      'templateSelection' => new JsExpression('format'),
											      'escapeMarkup' => $escape,
											    ],
                                            ]); ?>
                    </div>
                    <div class="form-group">
                        <?=GridView::widget([
                            'dataProvider' => $userDataProvider,
                            'columns' => [
                                [
                                    'class' => 'yii\grid\CheckboxColumn',
                                    'cssClass' => 'userForBreak',
                                    'name' => "EventBreaks[$i][check]",
                                    'checkboxOptions' => function($eventUser) use($event, $model) {
                                        $toReturn['data'] = [
                                            'eventid' => $event->id,
                                            'breakid' => $model->id,
                                            'userid'  => $eventUser->user_id,
                                        ];

                                        if (\common\models\EventBreaksUser::find()->where(['user_id' => $eventUser->user_id])->andWhere(['event_break_id' => $model->id])->count() == 1) {
                                            $toReturn['checked'] = true;
                                        }
                                        $toReturn['value'] = $eventUser->user_id;
                                        return $toReturn;
                                    },
                                    'options' => ['class' => 'checkbox-column'],
                                ],
                                [
                                    'label' =>  Yii::t('app', 'Pracownik'),
                                    'content' => function($eventUser) use ($model) {
                                        return $eventUser->user->first_name . " " . $eventUser->user->last_name;
                                    }
                                ]
                            ],
                        ]);
                        ?>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
    <?php DynamicFormWidget::end(); ?>
    <br>
</div>
<br>




<?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success pull-right']) ?>
<br><br>
<?php

ActiveForm::end();

$this->registerJs($format .'
    $("body").find("#event_breakes_form_modal").on("submit", function(e){
        var _form = $(this);
        
        $.post(
            _form.attr("action"),
            _form.serialize()
        )
        .done(function(result){
        	var modal = $("body").find("#event_breaks_modal");
        	modal.modal("hide");
            $("#calendar").fullCalendar("refetchEvents");
            modal.find(".modalContent").html("");
        })
        .fail(function(){
            console.log("Server error 1!");
        });

        return false;
    });

    $("#event_breakes_form_modal .dynamicform_wrapper").on("beforeDelete", function(e, item) {
	    if (confirm("'.Yii::t("app", "Czy na pewno chcesz usunąć?").'")) {
		    var id_inp = $(item).find(".break_id");
		    if(id_inp){
		    	$.post(
	          "'.Url::to(['planboard/event-breaks-form', 'event_id' => $event_id, 'delete_event_breaks' => 1 ]).'",
	          {id: id_inp.val()}
		      )
		      .done(function(result){
		        $("#calendar").fullCalendar("refetchEvents");
		      })
		      .fail(function(){
		          console.log("Server error 2!");
		      });
		    }

		    return true;
	    }
	    
    return false;
    });
		
    $(".userForBreak").click(function(){
        if ($(this).data("breakid")) {
            var breakid = $(this).data("breakid");
            var userid = $(this).data("userid");
            if ($(this).is(":checked")) {
                assignUserBreak(userid, breakid);
            }
            else {
                deleteUserEventBreak(userid, breakid);
            }
        }
    });
    
     $("#event_breakes_form_modal .dynamicform_wrapper").on("afterInsert", function(e, item){
        var last_index = $(".checkbox-column").length;
        $(".dynamicform_wrapper .checkbox-column").each(function(index){
            if (last_index == index + 1) {
                $(this).parent().parent().find(".select-on-check-all").click(function(){
                    var checked = $(this).is(":checked");
                    $(this).parent().parent().parent().next().find(".userForBreak").each(function(){
                        $(this).prop("checked", checked);
                    });
                });
                $(this).parent().parent().find(".userForBreak").each(function(){
                    var input = $(this);
                    input.attr("name", "EventBreaks[" + index + "][check][]" );
                    input.attr("data-breakid", "");
                    input.attr("checked", false);
                    input.attr("value", input.attr("data-userid"));
                });
            }
        });
     
     });
		 
	$(".select-on-check-all").click(function(){
	    var selectChecked = $(this).is(":checked");
	    $(this).parent().parent().parent().next().find("input").each(function(){
	        var breakid = $(this).data("breakid");
            var userid = $(this).data("userid");
	        if (selectChecked && !$(this).is(":checked")) {
	            assignUserBreak(userid, breakid);
	        }
	        else if (!selectChecked) {
	            deleteUserEventBreak(userid, breakid);
	        }
	    });
	});
	
	
    function assignUserBreak(userid, breakid) {
        $.post("'.Url::to(['planboard/assign-user-break']).'" + "?user_id=" + userid + "&event_break_id=" + breakid );
    }
    
    function deleteUserEventBreak(userid, breakid) {
        $.post("'.Url::to(['planboard/delete-user-event-break']).'" + "?user_id=" + userid + "&event_break_id=" + breakid );
    }
		
',View::POS_HEAD);