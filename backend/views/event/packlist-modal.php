<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\PacklistGear;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\GearServiceStatut */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="gear-service-statut-form">

    <?php $form = ActiveForm::begin(['id'=>'packlist-modal-form']); ?>
    <table class="table" id="pack-gear">
    <tr><th><?=Yii::t('app', 'Nazwa')?></th><th><?=Yii::t('app', 'Przypisz')?></th><th><?=Yii::t('app', 'Łącznie')?></th><th><?=Yii::t('app', 'Packlisty')?></th><th><?=Yii::t('app', 'Uwagi')?></th><td></td></tr>
    <?php foreach ($gears as $gear){ 
        $total = 0;
        $total2 = 0;
                            $content = "";
                            foreach ($gear->eventGear->packlistGears as $p)
                            {
                                $content .='<span class="label label-warning" style="background-color:'.$p->packlist->color.'">'.$p->quantity.'</span> ';
                                $total +=$p->quantity;
                                if (($p->packlist_id!=$packlist->id)&&($p->packlist_id!=$gear->packlist_id))
                                    $total2 +=$p->quantity;
                            }
                            $free = $gear->quantity;
        ?>
    <tr id="row-<?=$gear->id?>"><td><?=$gear->gear->name?></td><td>
        <?php
        $pack= PacklistGear::find()->where(['event_gear_id'=>$gear->id, 'packlist_id'=>$packlist->id])->one();
        if (!$pack)
        {
            $pack = new PacklistGear(['event_gear_id'=>$gear->id, 'packlist_id'=>$packlist->id]);
        }
        if (!$one)
            $pack->quantity = $free;
        echo $form->field($pack, 'quantity')->textInput(['placeholder' => Yii::t('app', 'Iość'), 'name'=>'quantity['.$gear->event_gear_id.']', 'type' => 'number', 'min'=>0, 'max'=>$free])->label(false);
        ?>

    </td><td><?=$gear->eventGear->quantity?></td><td><?=$content?></td><td><?php echo $form->field($pack, 'info')->textInput(['placeholder' => Yii::t('app', 'Uwagi'), 'name'=>'info['.$gear->event_gear_id.']'])->label(false); ?></td><td></td></tr>
    <?php } ?>
    <?php foreach ($ogears as $gear){ 
        $pgear = $gear;
        $gear = $gear->eventOuterGear;
        $total = 0;
        $total2 = 0;
                            $content = "";
                            $packlists = \common\models\PacklistOuterGear::find()->joinWith(['packlist'])->where(['event_outer_gear'=>$pgear->event_outer_gear])->all();
                            foreach ($packlists as $p)
                            {
                                $content .='<span class="label label-warning" style="background-color:'.$p->packlist->color.'">'.$p->quantity.'</span> ';
                                $total +=$p->quantity;
                                if ($p->packlist_id!=$packlist->id)
                                    $total2 +=$p->quantity;
                            }
                            $free = $pgear->quantity;
        ?>
    <tr><td><?=$gear->outerGear->outerGearModel->name."</br>".$gear->outerGear->company->name?></td><td>
        <?php
        $pack= \common\models\PacklistOuterGear::find()->where(['event_outer_gear'=>$pgear->event_outer_gear, 'packlist_id'=>$packlist->id])->one();
        if (!$pack)
        {
            $pack = new \common\models\PacklistOuterGear(['event_outer_gear'=>$pgear->event_outer_gear, 'packlist_id'=>$packlist->id]);
        }
        if (!$one)
            $pack->quantity = $free;
        echo $form->field($pack, 'quantity')->textInput(['placeholder' => Yii::t('app', 'Iość'), 'name'=>'oquantity['.$pgear->event_outer_gear.']', 'type' => 'number', 'min'=>0, 'max'=>$free])->label(false);
        ?>

    </td><td><?=$gear->quantity?></td><td><?=$content?></td><td><?php echo $form->field($pack, 'info')->textInput(['placeholder' => Yii::t('app', 'Uwagi'), 'name'=>'oinfo['.$pgear->event_outer_gear.']'])->label(false); ?></td></tr>
    <?php } ?>
    <?php foreach ($extras as $gear){ 
        $total = 0;
        $total2 = 0;
                            $content = "";
                            foreach ($gear->eventExtraItem->packlistGears as $p)
                            {
                                $content .='<span class="label label-warning" style="background-color:'.$p->packlist->color.'">'.$p->quantity.'</span> ';
                                $total +=$p->quantity;
                                if ($p->packlist_id!=$packlist->id)
                                    $total2 +=$p->quantity;
                            }
                            $free = $gear->quantity;
        ?>
    <tr><td><?=$gear->eventExtraItem->name?></td><td>
        <?php
        $pack= \common\models\PacklistExtra::find()->where(['event_extra_id'=>$gear->event_extra_id, 'packlist_id'=>$packlist->id])->one();
        if (!$pack)
        {
            $pack = new \common\models\PacklistExtra(['event_extra_id'=>$gear->event_extra_id, 'packlist_id'=>$packlist->id]);
        }
        if (!$one)
            $pack->quantity = $free;
        echo $form->field($pack, 'quantity')->textInput(['placeholder' => Yii::t('app', 'Iość'), 'name'=>'equantity['.$gear->event_extra_id.']', 'type' => 'number', 'min'=>0, 'max'=>$free])->label(false);
        ?>

    </td><td><?=$gear->quantity?></td><td><?=$content?></td><td><?php echo $form->field($pack, 'info')->textInput(['placeholder' => Yii::t('app', 'Uwagi'), 'name'=>'einfo['.$gear->event_extra_id.']'])->label(false); ?></td></tr>
    <?php } ?>
    </table>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>'btn btn-success', 'id'=>'add-packlist-form-submit']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

$this->registerJs('
    $("#packlist-modal-form").submit(function(e){
        e.preventDefault();
       
    });

    $("#packlist-modal-form").on("beforeSubmit", function(e){
        $("#add-packlist-form-submit").attr("disabled", true);
        $.ajax({
                type: "POST",
                url: "'.Url::to(['event/save-packlist-modal', 'id'=>$packlist->event_id, 'packlist_id'=>$packlist->id, 'packlist_from'=>$packlist_id]).'",
                data: $(this).serialize(),
                async: false,
                success: function(data){
                    if (data.ok==1)
                    {
                        $("#packlist_modal").modal("hide");
                        $("#tab-gear").load("'.Url::to(['event/gear-tab', 'id'=>$packlist->event_id]).'");
                    }else{
                        
                        $("#add-packlist-form-submit").remove();
                        for ($i=0; $i<data.successes.length; $i++)
                        {
                            $("#row-"+data.successes[$i].id).remove();
                        }
                        for ($i=0; $i<data.errors.length; $i++)
                        {
                            $("#row-"+data.errors[$i].gear.id).find("#packlistgear-quantity").attr("disabled", true);
                            $("#row-"+data.errors[$i].gear.id).find("td:last").empty().append("<p style=\'color:red;\'>'.Yii::t('app', 'Brakuje: ').'"+data.errors[$i].missing+". '.Yii::t('app', 'Co chcesz zrobić?').'</p> ");
                            $("#row-"+data.errors[$i].gear.id).find("td:last").append("<a class=\'btn btn-xs btn-success make-conflict-button\' data-gearid="+data.errors[$i].gear.id+">'.Yii::t('app', 'Stwórz konflikt').'</a> ");
                            $("#row-"+data.errors[$i].gear.id).find("td:last").append("<a class=\'btn btn-xs btn-primary move-button\'data-gearid="+data.errors[$i].gear.id+">'.Yii::t('app', 'Przenieś z obecnym czasem pracy').'</a> ");
                        }
                        $(".make-conflict-button").click(function(e){
                            e.preventDefault();
                            makeConflict($(this));
                        });
                        $(".move-button").click(function(e){
                            e.preventDefault();
                            moveToPacklist($(this));
                        });
                    }
                    
                }    
            });
        return false;
    });
');
?>

<script type="text/javascript">
    function makeConflict(b)
    {
        gear_id = b.data("gearid");
        quantity = $("#row-"+b.data("gearid")).find("#packlistgear-quantity").val();
        info = $("#row-"+b.data("gearid")).find("#packlistgear-info").val();
        $.ajax({
                type: "POST",
                url: "<?=Url::to(['event/save-packlist-modal-conflict', 'id'=>$packlist->event_id, 'packlist_id'=>$packlist->id, 'packlist_from'=>$packlist_id])?>",
                data: {id:gear_id, quantity:quantity, info:info},
                async: false,
                success: function(data){
                    if (data.ok==1)
                    {
                        $("#row-"+b.data("gearid")).remove();
                    }
                    
                }    
            });
    }

    function moveToPacklist(b)
    {
        gear_id = b.data("gearid");
        quantity = $("#row-"+b.data("gearid")).find("#packlistgear-quantity").val();
        info = $("#row-"+b.data("gearid")).find("#packlistgear-info").val();
        $.ajax({
                type: "POST",
                url: "<?=Url::to(['event/save-packlist-modal-one', 'id'=>$packlist->event_id, 'packlist_id'=>$packlist->id, 'packlist_from'=>$packlist_id])?>",
                data: {id:gear_id, quantity:quantity, info:info},
                async: false,
                success: function(data){
                    if (data.ok==1)
                    {
                        $("#row-"+b.data("gearid")).remove();
                    }
                    
                }    
            });
        
    }
</script>