<?php


use common\helpers\Url;
use common\models\GearItemsNoItemsRfid;
use kartik\grid\GridView;
use yii\helpers\Html;

$search = null;
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

echo Html::beginForm(null, 'post', ['id' => 'modelsNoItems']);
echo GridView::widget([
    'dataProvider' => $gearItemNoItemsDataProvider,
    'filterModel' => new GearItemsNoItemsRfid(),
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'name',
            'label' => Yii::t('app','Nazwa'),
            'value' => function ($model) {
                return Html::a($model->gearItem->gear->name, ['gear/view', 'id'=>$model->gearItem->gear->id]);
            },
            'format' => 'html',
            'filter' => "<input name='search' type=text class='form-control' value='".$search."' />"
        ],
        [
            'attribute' => 'Kod RFID',
            'label' => Yii::t('app','Kod RFID'),
            'format' => 'raw',
            'value' => function ($model) {
                $id = $model->id;
                $input = null;
                if ($model->id == null) {
                    $uniqueId = uniqid();
                    $id = 'null'.$uniqueId;
                    $input = Html::input('hidden', 'model_id['.$uniqueId.']', $model->gear_item_id);
                }
                return Html::input('text', 'model['.$id.']', $model->rfid_code) . $input;
            }

        ],
    ],
]);


echo Html::submitButton( Yii::t('app', "Zapisz"), ['class' => 'btn btn-success', 'id' => 'btnSaveNoItems']);
echo Html::endForm();

$this->registerJs('
    $("#btnSaveNoItems").click(function(e){
        e.preventDefault();
        $(".saveing").show();
        var form = $("form#modelsNoItems").serializeArray();
        
        $.ajax({
            type: "post",
            url: "'.Url::toRoute('rfid/update-gear-items-no-item-rfid').'",
            data: form,
            success: function (resp) {
                $(".saveing").hide();
                $(".saved").show();
                setInterval(function(){ $(".saved").hide(); },1000);
            }
        });
    });
');

$this->registerCss('
    .alert-success { border-color: #3c763d; }
    .alert-warning { border-color: #8a6d3b }
    .saveing, .saved { position: fixed; top: 200px; left: 50%; margin-left: -200px; width: 200px; display: none;}
    .glyphicon-refresh-animate {
        -animation: spin 1s infinite linear;
        -webkit-animation: spin2 1s infinite linear;
        margin-left: 10px;
    }
    @-webkit-keyframes spin2 {
        from { -webkit-transform: rotate(0deg);}
        to { -webkit-transform: rotate(360deg);}
    }
    @keyframes spin {
        from { transform: scale(1) rotate(0deg);}
        to { transform: scale(1) rotate(360deg);}
    }
');

?>

<div class="alert alert-warning saveing" role="alert" style="text-align: center;">
    <?=  Yii::t('app', 'Zapisuje zmiany') ?> <span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span>
</div>

<div class="alert alert-success saved" role="alert" style="text-align: center;">
    <?=  Yii::t('app', 'Zapisano!') ?>
</div>
