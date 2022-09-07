<?php
/* @var $this \yii\web\View */
/* @var $event \common\models\Event */

use common\models\EventOuterGear;
use common\models\Event;
use common\components\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

//Pjax::begin(['linkSelector' => 'a:not(.linksWithTarget)']);

$this->title =  Yii::t('app', 'Przypisz sprzęt').' - ' . $event->name;

$btnText = $this->context->title;
if (strpos($this->context->title,  Yii::t('app', 'Oferta')) !== false) {
    $btnText =  Yii::t('app', 'Oferta');
}

?>

<?php
if ($conflict)
{
    $conflictModel = \common\models\EventConflict::findOne($conflict);
    ?>
        
    <div class="conflict-modal widget style1 navy-bg">
    <div class="row">
                        <div class="col-md-2">
                            <i class="fa fa-info fa-3x"></i> 
                        </div>
                        <div class="col-md-10">
                            <?=Yii::t('app', 'Rozwiązujesz konflikt na sprzęt ').$conflictModel->gear->name.Yii::t('app', ' brakuje ').$conflictModel->quantity.Yii::t('app', ' szt.')?>
                        </div>
                    </div>
    
    
    </div>
    <?php
}

?>

<?php //echo $this->render('../warehouse/_summaryTable', ['event'=>$event->id, 'type'=>$type]) ?>
<div class="menu-pils">
    <?= $this->render('_categoryMenu'); ?>
</div>

<?= $this->render('_toolsAssign'); ?>
<div class="warehouse-container">
    <div class="row">
        <div class="col-md-12">
        <?php if ($return == 'finance'){
            echo Html::a(Html::icon('arrow-left').' Wróć', [$this->context->returnRoute, 'id'=>$event->id, "#"=>"tab-money"], ['class'=>'btn btn-primary']); 
        }else{
            if ($conflict)
                    echo Html::a(Html::icon('arrow-left').Yii::t('app', ' Wróć'), [$this->context->returnRoute, 'id'=>$event->id, "#"=>"eventTabs-dd3-tab2"], ['class'=>'btn btn-primary btn-sm']);
                else
                            echo Html::a(Html::icon('arrow-left').' Wróć', [$this->context->returnRoute, 'id'=>$event->id, "#"=>"eventTabs-dd3-tab0"], ['class'=>'btn btn-primary']); 
        } ?>
            
            <?= Html::a(Yii::t('app', 'Magazyn'), array_merge(['warehouse/assign'], $_GET), ['class'=>'btn btn-success']); ?>
        </div>

    </div>
    <div class="gear gears">
        <div class="panel_mid_blocks">
            <div class="panel_block" style="margin-bottom: 0;">
                <div class="title_box">
                    <h4><?php echo $title; ?></h4>
                </div>
            </div>
        </div>
        <?php
        $gearColumns = [
            [
                'label' =>  Yii::t('app', 'Liczba'),
                'format' => 'html',
                'content' => function ($model) use ($event, $type2, $item) {
                    /* @var $model \common\models\OuterGear; */
                    //if ($model->quantity > 0  && $model->quantity != null) {
                        $value = '';
                        if ($model->getIsGearAssigned($event, $model, $type2, $item)) {
                            $value = $model->getAssignedGearNumber($event, $model, $type2, $item);
                        }
                        if ($value=='')
                            $value2 = 0;
                        else
                            $value2 = $value;
                        return Html::input('number', '', $value,
                            [
                                'class' => 'quantity-input item-input-id-'.$model->id,
                                'min' => 0,
                                'max' => $model->quantity,
                                'style' => 'width: 50px;',
                                'data' => [
                                    'id' => $model->id,
                                    'quantity'=>$value2
                                ],
                            ]);
                    //}
                }
            ],
            [
                'attribute' => 'photo',
                'value' => function ($model, $key, $index, $column) {
                    /* @var $model \common\models\OuterGear */
                    if ($model->photo == null)
                    {
                        return '-';
                    }
                    return Html::img($model->getFileThumbUrl(), ['width'=>'100px']);
                },
                'format'=>'raw',
                'contentOptions'=>['class'=>'text-center'],
            ],
            [
                'attribute' => 'name',
                'value' => function ($model, $key, $index, $column) {
                    $content = Html::a($model->name, ['outer-gear-model/update', 'id'=>$model->id]);
                    return $content;
                },
                'format' => 'html',
            ],
            'quantity',
            [
                'label' =>  Yii::t('app', 'Sztuk w magazynie'),
                'value' => function ($model) {
                    return $model->numberOfAvailable();
                }
            ]
            ];
        ?>
        <div class="panel_mid_blocks">
            <div class="panel_block">
                <?php
        echo GridView::widget([
            'dataProvider' => $gearDataProvider,
            'filterModel' => null,
            'tableOptions' => [
                'class' => 'kv-grid-table table table-condensed kv-table-wrap'
            ],
            'columns' => $gearColumns,
        ]); ?>
            </div>
        </div>
    </div>

</div>

<?php
$eventGearUrl = Url::to(['outer-warehouse/assign-outer-gear', 'id'=>$event->id, 'type'=>$type]);
$eventGroupUrl = Url::to(['outer-warehouse/assign-outer-gear', 'id'=>$event->id, 'type'=>$type, 'group'=>1]);
$eventModelUrl = Url::to(['outer-warehouse/assign-outer-gear', 'id'=>$event->id, 'type'=>$type, 'model'=>1, 'type2'=>$type2, 'item'=>$item]);
$eventGearQuantityUrl = Url::to(['outer-warehouse/assign-outer-gear', 'id'=>$event->id, 'type'=>$type, 'noItem'=>1]);
$this->registerJs('




function eventModel(id, add, quantity, old) {
    var data = {
        itemId : id,
        add : add ? 1 : 0,
        quantity: quantity,
    }
    $.post("'.$eventModelUrl.'", data, function(response){
        if (add) {
            resolveConflict(quantity, old);
            toastr.success("'.Yii::t('app', 'Sprzęt dodany do eventu').'");
            var gear_row = $(".gear-item-outer-row[data-itemouterid=\'"+response.gear.id+"\']");
            if ( gear_row.length == 1) {
                gear_row.find("td:nth-child(3)").html(quantity);
            }
            else {
                createRowOuterGear(response.gear, quantity);
            }
        }
        else {
            toastr.error("'.Yii::t('app', 'Sprzęt usunięty z eventu').'");
            $(".gear-item-outer-row[data-itemouterid=\'"+response.gear.id+"\']").remove();
        }
    });
}

function createRowOuterGear(gear, number) {
    var img;
    if (gear.photo) {
        img = "<img src=\'/uploads/outer-gear/"+gear.photo+"\' alt=\'\' width=\'100px\' >";
    }
    var new_row = 
        "<tr class=\'gear-item-outer-row\' data-itemouterid=\'"+gear.id+"\'>"+
            "<td>"+gear.id+"</td>"+
            "<td>"+img+"</td>"+
            "<td>"+number+"</td>"+
            "<td>"+gear.name+"<br>"+gear.qrcode+"</td>"+
            "<td>'. Yii::t('app', 'Zewnętrzny').'</td>"+
            "<td><span class=\'remove_outer_model glyphicon glyphicon-remove\' style=\'cursor:pointer;\' data-id=\'"+gear.id+"\'></span></td>"+
        "</tr>";
    
    if ($("#outcomes-table tbody").length === 0) {
        $("#outcomes-table").append("<tbody></tbody>");
    }
    $("#outcomes-table tbody").each(function(index){
        if (index === 0) {
            $(this).append(new_row);
        }
    });

}

$(".quantity-input").change(function(){
    var id = $(this).data("id");
    var add;
    if (parseInt($(this).val()) === 0) {
        add = false;
    }
    else {
        add = true;
    }
    eventModel(id, add, $(this).val(), $(this).data("quantity"));
});

');

$this->registerCss("
    .conflict-modal{
        position:fixed;
        right:0px;
        top:400px;
        width:250px;
        background-color: #1ab394;
        font-size: 13px;
        color: white;
        padding-right:15px;
        padding-left:15px;
    }");
?>

<?php //Pjax::end(); ?>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
    function resolveConflict(newValue, oldValue){
        <?php if ($conflict) { ?>
        swal({
            closeOnClickOutside: false,
            title: "<?=Yii::t('app', 'Czy konflikt został rozwiązany?')?>",
            icon:"info",
          buttons: {
            cancel: "<?=Yii::t('app', 'Nie')?>",
            partial: {
                text:"<?=Yii::t('app', 'Częściowo')?>",
                value:"partial"
            },
            yes: {
              text: "<?=Yii::t('app', 'Tak')?>",
              value: "yes",
            },
          },
        })
        .then((value) => {
          switch (value) {
         
            case "yes":
              location.href = "<?=Url::to(['warehouse/conflict', 'id'=>$conflict]);?>";
              break; 
            case "partial":
              location.href = "<?=Url::to(['warehouse/conflict-partial', 'id'=>$conflict]);?>&old="+oldValue+"&quantity="+newValue;
              break;       
          }
        });
        <?php } ?>
    }
</script>