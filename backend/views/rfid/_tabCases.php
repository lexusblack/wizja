<?php


use common\helpers\Url;
use kartik\grid\GridView;
use yii\helpers\Html;

echo Html::beginForm(null, 'post', ['id' => 'cases']);
echo GridView::widget([
    'dataProvider' => $casesDataProvider,
    'filterModel' => $casesSearchModel,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'name',
            'label' => Yii::t('app','Nazwa'),
            'value' => function ($model) {
                return Html::a($model->name, ['gear-group/view', 'id'=>$model->id]);
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


echo Html::submitButton( Yii::t('app', "Zapisz"), ['class' => 'btn btn-success', 'id' => 'saveBtnCase']);
echo Html::endForm();

$this->registerJs('
    $("#saveBtnCase").click(function(e){
        e.preventDefault();
        $(".saveing").show();
        var form = $("form#cases").serializeArray();
        
        $.ajax({
            type: "post",
            url: "'.Url::toRoute('rfid/update-cases-rfid').'",
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
