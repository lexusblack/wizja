<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\tabs\TabsX;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Gear */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Magazyn'), 'url' => ['/warehouse/index']];
if ($model->category->lvl>1)
{
    $category = $model->category->getMainCategory();
    $this->params['breadcrumbs'][] = ['label' => $category->name, 'url' => ['/warehouse/index', 'c'=>$category->id]];
    $this->params['breadcrumbs'][] = ['label' => $model->category->name, 'url' => ['/warehouse/index', 'c'=>$category->id, 's'=>$model->category->id]];
}else{
    $this->params['breadcrumbs'][] = ['label' => $model->category->name, 'url' => ['/warehouse/index', 'c'=>$model->category->id]];
}

$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
                    if ($model->no_items)
                    {
                        $gear_q = $model->quantity-$model->getInService();
                        
                    }
                    else
                    {
                        $gear_q = $model->getGearItems()->andWhere(['active'=>1])->count()-$model->getInService();
                    }
$service = $model->getInService();
?>
<div class="gear-view">
<?php
if (!$model->active) { ?>
<div class="alert alert-danger">
                                <?=Yii::t('app', "UWAGA!!! Model został usunięty");?>.
                            </div>
    
    <?php
}
?>

<div class="row">
    <div class="col-md-12">
    <div class="ibox">
                        <div class="ibox-title newsystems-bg">
                            <h5><?php echo $model->name; ?></h5>
                        </div>
                        <div class="ibox-content" style="  margin: auto; width: 100%;">
                        <p><?=Html::a(Yii::t('app', 'Zapisz'), ['save-wizja', 'id'=>$model->id], ['class'=>'btn btn-primary save-wizja'])?> <?=Html::a(Yii::t('app', 'Export xls'), ['wizja-export', 'id'=>$model->id], ['class'=>'btn btn-primary'])?></p>

<div class="twrapper twrapper1" style="height:60px; overflow-y:scroll;">
<table class="table" style="margin:0;">
    <tr><th style="width:100px;" class="sticky-col first-col"><?=Yii::t('app', 'Data')?></th><th class="sticky-col second-col" style="width:100px; text-align:center;"><?=Yii::t('app', 'Suma')?></th><th class="sticky-col third-col" style="width:100px;text-align:center;"><?=Yii::t('app', 'Serwis')?></th>
    <?php foreach ($eventList as $id=> $name){ ?>
        <th class="col-normal" title="<?=$name?>"><?=Html::a($name, ['event/view', 'id'=>$id], ['target'=>'_blank'])?></th>
    <?php } ?>
    </tr>
</table>
</div>
                        <div class="twrapper twrapper2" style="height:650px; overflow-y:scroll;">

<table class="table" id="wizja-table">
    <tr><th style="width:100px;" class="sticky-col first-col"><?=Yii::t('app', 'Data')?></th><th class="sticky-col second-col" style="width:100px;text-align:center;"><?=Yii::t('app', 'Suma')?></th><th class="sticky-col third-col" style="width:100px;text-align:center;"><?=Yii::t('app', 'Serwis')?></th>
    <?php foreach ($eventList as $id=> $name){ ?>
        <th class="col-normal" title="<?=$name?>"><?=Html::a($name, ['event/view', 'id'=>$id], ['target'=>'_blank'])?></th>
    <?php } ?>
    </tr>
    <?php foreach ($events as $day => $e) 
    { ?>
        <tr id="row_<?=$day?>" data-day="<?=$day?>" class="day-row"><td class="sticky-col first-col"><?=$day?></td><td id="sum_<?=$day?>" style="text-align:center;" class="sticky-col second-col"></td><td style="text-align:center;" class="sticky-col third-col"><?=$service?></td>
        <?php foreach ($e as $id =>$q){
            $blocked = \common\models\Packlist::find()->where(['event_id'=>$id, 'blocked'=>1])->count();
            if ($blocked)
                $blocked = " disabled";
            else
                $blocked = "";
        if ($q['conflict']>0){
            $style = "style='background-color:#ed5565;'";
            }else{ 
                if (($eventDates[$id]['start']<$day." 23:59:00")&&($eventDates[$id]['end']>$day))
                {
                    $style = "style='background-color:#dddddd;'";
                }else{
                    $style = '';
                }
                } ?>
        <td <?=$style?> class="col-normal"><input type="text" id="<?=$day.'_'.$id?>" value="<?=$q['quantity']?>" class="wizja-input" data-day="<?=$day?>" data-id="<?=$id?>" data-value="<?=$q['quantity']?>" <?=$blocked?> /></td>
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
    function countDay(day)
    {
        var total = <?=$gear_q?>;
        $("#row_"+day).find(".wizja-input").each(function()
        {
            total = total-parseInt($(this).val());
        })
        $("#sum_"+day).html(total);
        if (total<0)
        {
            $("#sum_"+day).addClass("with-conflict");
        }else{
            $("#sum_"+day).removeClass("with-conflict");
        }
    }

    function countAllDays()
    {
        $(".day-row").each(function(){
            countDay($(this).data("day"));
        })
    }

    function freezeTable()
    {
            $("#wizja-table").fxdHdrCol({
            fixedCols: 2,
            width:     "100%",
            height:    600,  
            colModal: [
               { width: 150, align: "center" },
               { width: 150, align: "center" },
               <?php foreach ($eventList as $e){ ?>
                { width: 100, align: "center" },
                <?php } ?>

            ]                
            });
    }
</script>

<?php $this->registerJs('
    countAllDays();

    $(".wizja-input").change(function()
    {
        countDay($(this).data("day"));
    })
    //freezeTable();
$(function(){
  $(".twrapper1").scroll(function(){
    $(".twrapper2").scrollLeft($(".twrapper1").scrollLeft());
  });
  $(".twrapper2").scroll(function(){
    $(".twrapper1").scrollLeft($(".twrapper2").scrollLeft());
  });
});

$(".save-wizja").click(function(e){
    e.preventDefault();
    href = $(this).attr("href");
    data = [];
    $(".wizja-input").each(function(){
        if ($(this).val()!=$(this).data("value"))
        {
            data.push({id:$(this).data("id"), day:$(this).data("day"), value:$(this).val(), oldvalue:$(this).data("value")});
        }
    });
    $.post(href, {data:JSON.stringify(data)}).success(function(){toastr.success("Zapisano!"); location.reload();});

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