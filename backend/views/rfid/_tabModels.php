<?php


use common\helpers\Url;
use kartik\grid\GridView;
use yii\helpers\Html;

echo Html::beginForm(null, 'post', ['id' => 'formModels']);
echo GridView::widget([
    'dataProvider' => $gearItemDataProvider,
    'filterModel' => $gearItemSearchModel,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'name',
            'value' => function ($model) {
                /** @var \common\models\GearItem $model */
                return Html::a($model->name . ", ". Yii::t('app', 'numer').": " . $model->number, ['gear-item/view', 'id'=>$model->id]);
            },
            'format' => 'html',
        ],
        [
            'attribute' => 'Kod RFID',
            'label' => Yii::t('app','Kod RFID'),
            'format' => 'raw',
            'value' => function ($model) {
                return Html::input('text', 'model['.$model->id.']', $model->rfid_code);
            }

        ],
    ],
]);


echo Html::submitButton( Yii::t('app', "Zapisz"), ['class' => 'btn btn-success', 'id' => 'saveBtnModel']);
echo Html::endForm();

$this->registerJs('
    $("#saveBtnModel").click(function(e){
        e.preventDefault();
        $(".saveing").show();
        var form = $("form#formModels").serializeArray();

        $.ajax({
            type: "post",
            url: "'.Url::toRoute('rfid/update-gear-items-rfid').'",
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
