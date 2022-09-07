<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model common\models\Event */
/* @var $form yii\widgets\ActiveForm */


?>
<li class="success-element ui-sortable-handle fc-event" id="event<?=$event->id?>" data-type="event"  data-eventid="<?=$event->id?>" data-id="item-<?=$event->id?>" data-event='{ title: "<?=$event->name?>", id: <?=$event->id?> }' style="border-left-color:<?=$event->eventStatut->color?>">
                                    <?=Html::a($event->name, ['/event/view', 'id'=>$event->id],['target'=>'_blank'])?>
                                    <?=Html::a("<i class='fa fa-pencil'></i>", ['event/edit-name', 'id'=>$event->id], ['class'=>'event-edit-name']) ?>
                                    <?=Html::a('<i class="fa fa-plus"></i>', ['event/add-prod-task', 'id'=>$event->id], ['class'=>'add-task-button'])?>
      </li>


<?php $this->registerJs('

  $(".event-edit-name").click(function(e){
    e.preventDefault();
    showEditNamesModal($(this).attr("href"));
  });
  $(".add-task-button").click(function(e){
    e.preventDefault();
    $("#add-task").modal("show").find(".modalContent").load($(this).attr("href"));
  });


  ');

?>