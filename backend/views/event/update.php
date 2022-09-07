<?php

use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model common\models\Event */

$this->title = Yii::t('app', 'Edycja eventu').': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wydarzenia'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');
?>
<div class="event-update">

    <?= $this->render('_form', [
        'model' => $model,
        'schema_change_possible'=>$schema_change_possible,
        'event'=>null,
        'offer'=>null
    ]) ?>

</div>
<?php


// --- Modal ---
Modal::begin([
    'header' => Yii::t('app', 'Konflik godzin pracy pracowników'),
    'id' => 'modal',
    'class' => 'modal'
]);
echo "<div class='modalContent'></div>";
Modal::end();


$this->registerJs('

var modal = $("#modal");

var conflicts_checked = false;

$("#event-form").on("submit", function(e){
    var data = $("form").serialize();
    var conflicts = null;
    
    $.ajax({
        url: "are-there-user-working-hours-confilcts?id='.$model->id.'", 
        data: data,
        type: "post",
        async: false,
        success: function(c) {
            conflicts = c;
        }
    });
    
    if (conflicts === true && !conflicts_checked) {
        e.preventDefault();
        modal.find(".modalContent").load("change-user-working-hours?id='.$model->id.'", data, function() {
            $(".input-daterangepicker").daterangepicker({
                "timePicker": true,
                "timePicker24Hour": true,
                "locale": {
                    "format": "YYYY-MM-DD HH:mm:ss",
                    "separator": " - ",
                    "applyLabel": "'.Yii::t('app', 'Zastosuj').'",
                    "cancelLabel": "'.Yii::t('app', 'Anuluj').'",
                    "fromLabel": "'.Yii::t('app', 'Od').'",
                    "toLabel": "'.Yii::t('app', 'do').'",
                    "customRangeLabel": "'.Yii::t('app', 'Własna').'",
                    "weekLabel": "W",
                    "daysOfWeek": [
                        "'.Yii::t('app', 'N').'",
                        "'.Yii::t('app', 'Pn').'",
                        "'.Yii::t('app', 'Wt').'",
                        "'.Yii::t('app', 'Śr').'",
                        "'.Yii::t('app', 'Cz').'",
                        "'.Yii::t('app', 'Pt').'",
                        "'.Yii::t('app', 'So').'"
                    ],
                    "monthNames": [
                        "'.Yii::t('app', 'Styczeń').'",
                        "'.Yii::t('app', 'Luty').'",
                        "'.Yii::t('app', 'Marzec').'",
                        "'.Yii::t('app', 'Kwiecień').'",
                        "'.Yii::t('app', 'Maj').'",
                        "'.Yii::t('app', 'Czerwiec').'",
                        "'.Yii::t('app', 'Lipies').'",
                        "'.Yii::t('app', 'Sierpień').'",
                        "'.Yii::t('app', 'Wrzesień').'",
                        "'.Yii::t('app', 'Październik').'",
                        "'.Yii::t('app', 'Listopad').'",
                        "'.Yii::t('app', 'Grudzień').'"
                    ],
                    "firstDay": 1,
                },
            });
            $(".input-daterangepicker").each(function(){
                $(this).val($(this).data("start") + " - " + $(this).data("end"));
            });
        });

        modal.modal("show");
    }
});

$("body").on("click", ".save-custom-working-hours", function(e) {
    var value = $(this).prev().val();
    var start = value.substring(0,19);
    var end = value.substring(22, 42);

    if (moment(start)._isAMomentObject && moment(end)._isAMomentObject ) {
        $.ajax({
            type: "post",
            url: "/admin/crew/change-working-hours?id=" + $(this).prev().data("id"),
            data: {start: start, end: end},
            success: function(ccc) {
                console.log(ccc);
            }
        });
    }
    else {
        alert("'.Yii::t('app', 'Niepoprawny zakres!').'");
    }
});

$("body").on("click", "#button-resolve-conflict", function(e) {
    e.preventDefault();
    
    $.ajax({
        url: "resolve-conflicts",
        method: "post",
        data: $("body").find("#resolve-conflict").serialize(),
        success: function(response) {
            // jeżeli udało się wszystkie konflikty rozwiązać, to lecimy dalej
            if (response) {
                modal.modal("hide");
                conflicts_checked = true;
                $("#event-form").submit();
            }
        }
    });
});





');

echo \kartik\editable\Editable::widget( [
        'name' => 'hidden',
        'inputType'=>\kartik\editable\Editable::INPUT_DATE_RANGE,
        'containerOptions'=>['style'=>'display:none'],
        'pjaxContainerId'=>'none',
    ]
);
$this->registerCss(' .modal-dialog { width: 900px; } .input-daterangepicker { width: 270px; } .save-custom-working-hours { margin-left: 10px; }');

?>

