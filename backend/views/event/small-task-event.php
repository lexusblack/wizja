<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model common\models\Event */
/* @var $form yii\widgets\ActiveForm */

$event = $task->event;
if ($task->datetime)
          $class = " fc-event";
        else
          $class = " fc-event";
        if ($task->status==10)
          $c = "#009900";
        else
          $c = "#990000";
        ?>
      <li class="success-element ui-sortable-handle <?=$class?> task" id="task<?=$task->id?>" data-type="etask"  data-eventid="<?=$task->id?>" data-id="item-<?=$task->id?>" data-event='{ title: "<?=$task->title?>", id: <?=$task->id?> }' style="border-left-color:<?=$c?>;">
                                    <?=Html::a($task->title, ['/event/view', 'id'=>$event->id],['target'=>'_blank'])?>
                                    <?=Html::a("<i class='fa fa-pencil'></i>", ['task/edit-name', 'id'=>$task->id], ['class'=>'event-edit-name']) ?>

                                    <?php if ($task->datetime){ ?>
                                    <span class="label pull-right"><i class="fa fa-clock-o"></i><?=substr($task->datetime, 0, 11)?></span>
                                    <?php } ?>
      </li>


<?php $this->registerJs('

  $(".event-edit-name").click(function(e){
    e.preventDefault();
    showEditNamesModal($(this).attr("href"));
  });


  ');

?>