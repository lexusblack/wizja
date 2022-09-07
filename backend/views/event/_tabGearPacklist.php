<?php
use yii\bootstrap\Html;
use yii\helpers\Url;

?>
<div class="row" style="margin-top:30px">
    <div class="col-md-12">
    <p>
            <?= Html::a('<i class="fa fa-list"></i> ' . Yii::t('app', 'PDF'), ['packlist-pdf', 'id' => $model->id, 'packlist_id'=>$packlist->id], ['class' => 'btn btn-info btn-xs', 'target'=>'_blank']);?><?= Html::a('<i class="fa fa-money"></i> ' . Yii::t('app', 'PDF'), ['packlist-pdf', 'id' => $model->id, 'packlist_id'=>$packlist->id], ['class' => 'btn btn-info btn-xs', 'target'=>'_blank']);?>
            <span class="label lazur-bg" id="weight<?=$packlist->id?>"><i class="fa fa-plug"></i> </span></h3>
              <span class="label yellow-bg" id="volume<?=$packlist->id?>"><i class="fa fa-archive"></i> </span></h3>
        <?php if (Yii::$app->user->can('eventEventEditPencil')) { ?>
        <?= Html::a('<i class="fa fa-pencil"></i> ' . Yii::t('app', 'Edycja'), ['add-packlist', 'id' => $model->id, 'packlist_id'=>$packlist->id], ['class' => 'btn btn-primary btn-xs add-packlist']);?>
        <?= Html::a('<i class="fa fa-pencil"></i> ' . Yii::t('app', 'Edycja pozycji'), ['#'], ['class' => 'btn btn-primary btn-xs', 'id'=>'edit-packlist-gear-'.$packlist->id]);?>
        <?= Html::a('<i class="fa fa-trash"></i> ' . Yii::t('app', 'Usuń'), ['packlist-delete', 'id' => $model->id, 'packlist_id' => $packlist->id], ['class' => 'btn btn-danger btn-xs','data' => [
                'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                'method' => 'post',
            ],]);?>
            <?php } ?>
             
    </p>
<h3><?=$packlist->name." <i class='fa fa-circle' style='color:".$packlist->color."'></i>"?></h3>

<p><?=str_ireplace("\r\n", "<br/>", $packlist->info);?></p>
<table class="table">
<tr>
    <th>#</th>
    <th><?=Yii::t('app', 'Zdjęcie')?></th>
    <th><?=Yii::t('app', 'Nazwa')?></th>
    <th><?=Yii::t('app', 'Sztuk')?></th>
   <!-- <th><?=Yii::t('app', 'Waga')?></th>
    <th><?=Yii::t('app', 'Objętość')?></th> -->
    <th><?=Yii::t('app', 'Uwagi')?></th>
    <th></th>
</tr>
<?php 
$volumeTotal = 0;
$weightTotal = 0;
$i =1;
foreach ($packlist->getGearsByCategories() as $cat)
{
    if ($cat['cat']->color)
                        {
                            $style= "style='background-color:".$cat['cat']->color.";'";
                        }else{
                            $style = "";
                        }
    ?>
 <tr>
    <td colspan="8" <?=$style?>><?=$cat['cat']->name?></td>
</tr>   
    <?php
    foreach ($cat['items'] as $g)
    {
        
        if ($g['type']=='gear'){
            $gear = $g['item']->eventGear->gear;
            $volume = $gear->countVolume2($g['item']->quantity);
            $weight = $gear->getWeightCase($g['item']->quantity)+$g['item']->quantity*$gear->weight;
            $name = Html::a($gear->name, ['gear/view', 'id'=>$gear->id]);
            if ($gear->photo == null) {
            $photo = "-";
            }
            else {
                $photo = Html::a(Html::img($gear->getPhotoUrl(), ['width'=>50]), ['gear/view', 'id'=>$gear->id]);
            }
        }
        if ($g['type']=='outer_gear'){
            $gear = $g['item']->outerGear->outerGearModel;
            $volume = $gear->countVolume()*$g['item']->quantity;
            $weight = $gear->weight*$g['item']->quantity;
            $name = Html::a($gear->name, ['outer-gear-model/view', 'id'=>$gear->id]);
            if ($gear->photo == null) {
            $photo = "-";
            }
            else {
                $photo = Html::a(Html::img($gear->getPhotoUrl(), ['width'=>50]), ['outer-gear-model/view', 'id'=>$gear->id]);
            }
        }
        if ($g['type']=='extra'){
            $gear = $g['item']->eventExtra;
            $volume = $gear->volume;
            $weight = $gear->weight;
            $name = $gear->name;
            $photo = "-";
        }
        $g = $g['item'];
        $weightTotal +=$weight;
        $volumeTotal +=$volume; 

        ?>
 <tr>
    <td><?=$i++?></td>
    <td><?=$photo?></td>
    <td><?=$name?></td>
    <td><?=$g->quantity?></td>
  <!--  <td><?=$weight?> [kg]</td>
    <td><?=$volume?> [m3]</td> -->
    <td><?=$g->info?></td>
    <td></td>
</tr>       
<?php
    }
}
?>
<!--
<tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td><?=$weightTotal?> [kg]</td>
    <td><?=$volumeTotal?> [m3]</td>
    <td></td>
    <td></td>
</tr> -->
</table>
</div>
</div>
<?php
$modalPacklistUrl = Url::to(['event/get-packlist-modal', 'id'=>$model->id]);

$this->registerJs('
$("#edit-packlist-gear-'.$packlist->id.'").on("click", function(e){
    e.preventDefault();
    //robimy tutaj serializację zaznaczonych checkboxów w trzech tabelkach i wyświetlamy opcję do wpisania ilości
    var gears = [];
    var ogears = [];
    var extra = [];
    var modal = $("#packlist_modal");
    pack_id = '.$packlist->id.';
        $.ajax({
                    url: "'.$modalPacklistUrl.'",
                    type: "post",
                    async: false,
                    data: {packlist_id:pack_id, all:1},
                    success: function(data) {
                        modal.modal("show").find(".modalContent").empty().append(data);
                    },
                    error: function(data) {
                            
                    }
                }); 
});

$("#weight'.$packlist->id.'").append("'.$weightTotal.' kg");
$("#volume'.$packlist->id.'").append("'.$volumeTotal.' m3");
    ');