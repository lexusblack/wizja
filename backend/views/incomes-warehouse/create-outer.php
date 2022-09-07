<?php

use common\models\IncomesGearOuter;
use common\models\OutcomesForEvent;
use common\models\OutcomesForRent;
use common\models\OutcomesGearOuter;
use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

$this->title = Yii::t('app', 'Przyjęcie do magazynu zewnętrznego');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wydane'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

    <h1><?= Html::encode($this->title) ?></h1>
    <div>
        <?=  $this->render('_summaryTable',[
            'event' => $event,
            'rent' => $rent,
        ]); ?>
    </div>


<?= Html::a(Html::icon('arrow-left') . ' ' . Yii::t('app', 'Powrót'), array_merge(['create'], $_GET), ['class'=>'btn btn-warning category-menu-link']);

if (isset($_GET['onlyEvent']) && $_GET['onlyEvent']) {
    echo " " . Html::a(Yii::t('app', 'Sprzęt z wszystkich eventów'), array_merge(['create-outer'], $_GET, ['onlyEvent' => false]), ['class' => 'btn btn-primary category-menu-link']);
}
else {
    echo " " . Html::a(Yii::t('app', 'Sprzęt z tego eventu'), array_merge(['create-outer'], $_GET, ['onlyEvent' => true]), ['class' => 'btn btn-primary category-menu-link']);
}
?>

<div class="menu-pils">
    <?= $this->render('../outer-warehouse/_categoryMenu'); ?>
</div>


<?php
Pjax::begin(); ?>

    <div class="warehouse-container">

        <div class="gear gears">
            <div class="panel_mid_blocks">
                <div class="panel_block" style="margin-bottom: 0;">
                    <div class="title_box">
                        <h4><?= Yii::t('app', 'Modele') ?></h4>
                    </div>
                </div>
            <?php
            $gearColumns = [
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'multiple'=>false,
                    'checkboxOptions' => function ($model, $key, $index, $column)  {

                        $toReturn = [
                            'class'=>'checkbox-model checkbox-item-outer-id item-id-'.$model->id,
                            'disabled' => false,
                            'value' => $model->id,
                            'data' => [
                                'id' => $model->id
                            ]
                        ];
                        if (isset($_COOKIE['checkbox-item-outer-id'][$model->id])) {
                            $toReturn['checked'] = true;
                        }
                        return $toReturn;
                    }
                ],
                [
                    'label' => Yii::t('app', 'Liczba'),
                    'format' => 'html',
                    'content' => function ($model) {
                        if ($model->numberOfAvailable() < $model->quantity) {
                            $value = 1;
                            if (isset($_COOKIE['checkbox-item-outer-id'][$model->id])) {
                                $value = $_COOKIE['checkbox-item-outer-id'][$model->id];
                            }
                            return Html::input('number', '', $value,
                                [
                                    'class' => 'quantity-input item-input-id-'.$model->id,
                                    'min' => 1,
                                    'max' => $model->quantity - $model->numberOfAvailable(),
                                    'style' => 'width: 50px;',
                                    'data' => [
                                        'id' => $model->id
                                    ],
                                ]);
                        }
                    }
                ],
                [
                    'attribute' => 'photo',
                    'value' => function ($model, $key, $index, $column) {
                        if ($model->photo == null)
                        {
                            return '-';
                        }
                        return Html::img($model->getPhotoUrl(), ['width'=>'100px']);
                    },
                    'format'=>'raw',
                    'contentOptions'=>['class'=>'text-center'],
                ],
                [
                    'attribute' => 'name',
                    'value' => function ($model, $key, $index, $column) {
                        $content = Html::a($model->name, ['outer-gear/update', 'id'=>$model->id]);
                        return $content;
                    },
                    'format' => 'html',
                ],
                'quantity',
                [
                    'label' => Yii::t('app', 'Sztuk w magazynie'),
                    'value' => function ($model) {
                        return $model->numberOfAvailable();
                    }
                ],
                'company_name',
                [
                    'class' => 'yii\grid\ActionColumn',
                ],
            ]; ?>
            <div class="panel_mid_blocks">
            <div class="panel_block">
<?php
            echo GridView::widget([
                'dataProvider' => $gearDataProvider,
                'filterModel' => null,
                'rowOptions' => function ($model) use ($event, $rent) {
                    if ($model->quantity == $model->numberOfAvailable()) {
                        return ['style' => 'display:none;'];
                    }
                    if (isset($_GET['onlyEvent']) && $_GET['onlyEvent']) {
                        $outcome_ids = [];
                        if ($event) {
                            foreach (OutcomesForEvent::find()->where(['event_id'=>$event])->all() as $outcome) {
                                $outcome_ids[] = $outcome->id;
                            }
                        }
                        if ($rent) {
                            foreach (OutcomesForRent::find()->where(['rent_id'=>$rent])->all() as $outcome) {
                                $outcome_ids[] = $outcome->id;
                            }
                        }
                        foreach ($outcome_ids as $outcome_id) {
                            $gears = OutcomesGearOuter::find()->where(['outcome_id' => $outcome_id])->andWhere(['outer_gear_id' => $model->id])->all();

                            $quantity = 0;
                            foreach ($gears as $gear) {
                                $quantity += $gear->gear_quantity;
                            }



                            $gears = IncomesGearOuter::find()->where(['income_id' => $outcome_id])->andWhere(['outer_gear_id' => $model->id])->all();
                            foreach ($gears as $gear) {
                                $quantity -= $gear->gear_quantity;
                            }

                            if ($quantity == 0) {
                                return ['style' => 'display:none;'];
                            }
                            else {
                                return null;
                            }
                        }
                    }
                },
                'summary' => '',
                'columns' => $gearColumns,
            ]);
            ?>

        </div>
    </div>
            </div>
        </div>

<?php

Pjax::end();


$this->registerJs('

function createCookie(name, value, days) {
    var expires;

    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    } else {
        expires = "";
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}
function readCookie(name) {
    var nameEQ = encodeURIComponent(name) + "=";
    var ca = document.cookie.split(\';\');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) === \' \') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0) return decodeURIComponent(c.substring(nameEQ.length, c.length));
    }
    return null;
}
function eraseCookie(name) {
    createCookie(name, "", -1);
}

// ************************

var new_page = true;
var clearCookies = false;
$(".category-menu-link").click(function(){
    new_page = false;
});
function confirmExit() {
    if (new_page == true) {
        clearCookies = true;
        return "'.Yii::t('app', 'opuścić stronę?').'";
    }
    else {
        clearCookies = false;
        new_page = true;
    }
}
function clearSession() {
     if (clearCookies == true) {
        clearCookies = false;
        clearAllCookies();
    }
}
function clearAllCookies() {
    var cookies = document.cookie.split(";");
    for (var i = 0; i < cookies.length; i++) {
        var name = cookies[i].trim().split("=")[0];
         document.cookie = name + \'=;expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/\';
    }
}
window.onbeforeunload = function() { return confirmExit(); }
window.onunload = function() { return clearSession(); }

// ************************

$(".quantity-input").change(function(){
   if ( $(".item-id-" + $(this).data("id")).is(":checked") ) {
        createCookie("checkbox-item-outer-id[" + $(this).data("id") + "]", $(this).val(), 1);
        addOuterGearItem($(this).data("id"), $(this).val());
   }
});

$(".checkbox-item-outer-id").change(function(){
    if ($(this).is(":checked")) {
        var quantity = $(".item-input-id-" + $(this).val()).val();
        createCookie("checkbox-item-outer-id[" + $(this).val() + "]", quantity, 1);
        addOuterGearItem($(this).val(), quantity);
    }
    else {
        eraseCookie("checkbox-item-outer-id[" + $(this).val() + "]");
        $(".gear-item-outer-row[data-itemouterid=\'"+$(this).val()+"\']").remove();
    }
});


$(".table-bordered").each(function(){
    $(this).removeClass("table-bordered");
});
$(".table-striped").each(function(){
    $(this).removeClass("table-striped");
});

// *********** tabelka podsumowująca

function addOuterGearItem(id, number) {
    var gear_row = $(".gear-item-outer-row[data-itemouterid=\'"+id+"\']");
    if ( gear_row.length == 1) {
        gear_row.addClass("item-in-basket");
        gear_row.find("input:checkbox").prop("checked", true);
        gear_row.find("td:nth-child(4)").html(number);
    }
    else {
        createRowOuterGear(id, number);
    }
}

function createRowOuterGear(id, number) {
    $.ajax({
        type: "POST",
        async: false,
        url: "'.Url::to(['outcomes-warehouse/get-gear-item-outer']).'?gear_id=" + id,
        success: function(gear) {
        
            var img;
            if (gear.photo) {
                img = "<img src=\'/uploads/outer-gear/"+gear.photo+"\' alt=\'\' width=\'100px\' >";
            }
            var new_row = 
                "<tr class=\'gear-item-outer-row item-in-basket\' data-itemouterid=\'"+id+"\'>"+
                    "<td>"+id+"</td>"+
                    "<td><input class=\'gear-item-outer\' data-id=\'"+id+"\' type=\'checkbox\' checked></td>"+
                    "<td>"+img+"</td>"+
                    "<td>"+number+"</td>"+
                    "<td>"+gear.name+"<br>"+gear.qrcode+"</td>"+
                    "<td>'.Yii::t('app', 'Zewnętrzny').'</td>"+
                    "<td><span class=\'remove_outer_model glyphicon glyphicon-remove\' style=\'cursor:pointer;\' data-id=\'"+id+"\'></span></td>"+
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
    });
}

');