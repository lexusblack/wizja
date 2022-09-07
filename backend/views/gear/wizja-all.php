<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\tabs\TabsX;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Gear */

$this->title = "Zajętość sprzętu";
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Magazyn'), 'url' => ['/warehouse/index']];


$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;

?>
<div class="gear-view">

<div class="row">
    <div class="col-md-12">
    <div class="ibox">
                        <div class="ibox-title newsystems-bg">
                            <h5><?php echo $this->title; ?></h5>
                        </div>
                        <div class="ibox-content" style="  margin: auto; width: 100%;">

<div class="twrapper twrapper1" style="height:60px; overflow-y:scroll;">
<table class="table" style="margin:0;">
    <tr><th style="width:100px;" class="sticky-col first-col"><?=Yii::t('app', 'Data')?></th>
    <?php foreach ($gears as  $gear){ ?>
        <th class="col-normal" title="<?=$gear->name?>"><?=Html::a($gear->name, ['gear/wizja', 'id'=>$gear->id], ['target'=>'_blank'])?></th>
    <?php } ?>
    </tr>
</table>
</div>
                        <div class="twrapper twrapper2" style="height:650px; overflow-y:scroll;">

<table class="table" id="wizja-table">
    <tr><th style="width:100px;" class="sticky-col first-col"><?=Yii::t('app', 'Data')?></th>
    <?php foreach ($gears as  $gear){  ?>
        <th class="col-normal" title="<?=$gear->name?>"><?=Html::a($gear->name, ['gear/wizja', 'id'=>$gear->id], ['target'=>'_blank'])?></th>
    <?php } ?>
    </tr>
    <?php foreach ($events as $day => $e) 
    { ?>
        <tr id="row_<?=$day?>" data-day="<?=$day?>" class="day-row"><td class="sticky-col first-col"><?=$day?></td>
        <?php foreach ($gears as  $gear){
        if ($events[$day][$gear->id]['quantity']<0){
            $style = "style='background-color:#ed5565; color:white; text-align:center;'";
            }else{ 
                    $style = 'style="text-align:center;"';
                } ?>
        <td <?=$style?> class="col-normal"><?=$events[$day][$gear->id]['quantity']?></td>
        <?php } ?>
    </tr>
    <?php } ?>
</table>
</div>
</div>
</div>
</div>
</div>
</div>


<script type="text/javascript">

    function freezeTable()
    {
            $("#wizja-table").fxdHdrCol({
            fixedCols: 1,
            width:     "100%",
            height:    600,  
            colModal: [
               { width: 150, align: "center" },
               <?php foreach ($gears as $e){ ?>
                { width: 100, align: "center" },
                <?php } ?>

            ]                
            });
    }
</script>

<?php $this->registerJs('
$(function(){
  $(".twrapper1").scroll(function(){
    $(".twrapper2").scrollLeft($(".twrapper1").scrollLeft());
  });
  $(".twrapper2").scroll(function(){
    $(".twrapper1").scrollLeft($(".twrapper2").scrollLeft());
  });
});
    ');

$this->registerCss('
    .with-conflict{
        background-color:#ed5565;
        color:white;
    }
    .view {
  margin: auto;
  width: 600px;
}

.twrapper {
  position: relative;
  overflow: auto;
  border: 1px solid black;
  white-space: nowrap;
}

.sticky-col {
  position: sticky;
  position: -webkit-sticky;
  background-color:#fff;
}
.sticky-col.with-conflict{
    background-color:#ed5565;
}
.first-col {
  width: 100px;
  min-width: 100px;
  max-width: 100px;
  left: 0px;
}

.second-col {
  left: 100px;
    width: 100px;
  min-width: 100px;
  max-width: 100px;
}

.col-normal{
        width: 100px;
  min-width: 100px;
  max-width: 100px;
  overflow:hidden;
    }
.wizja-input{
    max-width:100%;
}
 
   ')


?>