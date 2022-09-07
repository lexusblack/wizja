<?php
/* @var $this yii\web\View */
/* @var $dashboard \common\models\form\Dashboard */
use kartik\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;
use kartik\helpers\Enum;

$user = Yii::$app->user;

$this->title = Yii::t('app', 'Statystyki');
$dataChart3 = "";
$i=0;
$sum = 0;
$color =["#d3d3d3", "#bababa", "#79d2c0", "#1ab394", "#b5b8cf", "#e4f0fb", "#f9ac5a", "#25c5c8", "#304151"];
foreach ($stats as $car)
{$sum+=$car['distance'];
    if ($i>0)
        {
            $dataChart3.=",";
        }
    $dataChart3 .= '{
        label: "'.$car['name'].'",
        data: '.$car['distance'].',
        color: "'.$color[$i%9].'",
    }';
    $i++;
}
?>
        


        <div class="title_box row">
            <div class="col-lg-6">
            <form class="form-inline">
            <?php 
            $months = Enum::monthList();
            $months = array_merge([Yii::t('app', 'Wszystkie')], $months);
            $cats = \common\models\GearCategory::getMainRootList();
            echo Html::a(Html::icon('arrow-left'), ['chart3', 'm'=>$prev['m'], 'y'=>$prev['y']], ['class'=>'btn btn-md btn-primary date-drop']); ?>
                <?php echo Html::dropDownList('y', $y, Enum::yearList(2016, date('Y'), true), ['class'=>'form-control date-drop', 'id'=>"year"]); ?>
                <?php echo Html::dropDownList('m', $m, $months, ['class'=>'form-control date-drop', 'id'=>'month']); ?>
                <?php echo Html::a(Html::icon('arrow-right'), ['chart3', 'm'=>$next['m'], 'y'=>$next['y']], ['class'=>'btn btn-md  btn-primary date-drop']); ?>
                </form>
                                <?php
                    $this->registerJs('
                        $(".date-drop").on("change", function(e){
                            location.href="/admin/stat/chart3?m="+$("#month").val()+"&y="+$("#year").val();
                        });
                    ');
                ?>
            </div>
            </div>
            <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><?=Yii::t('app', 'Pojazdy - kilometrówka')." (".Yii::t('app', 'Łącznie - ').$sum."km)"?></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">

                        <div class="flot-chart">
                            <div class="flot-chart-content" id="flot-bar-chart3"></div>
                        </div>
                        <table class="table">
                            <tr><th><?=Yii::t('app', 'Pojazd')?></th><th><?=Yii::t('app', 'Przejechanych km')?></th></tr>
                            <?php foreach ($stats as $car) { ?>
                            <tr><td><?=$car['name']?></td><td><?=$car['distance']." km"?></td></tr>

                            <?php    } ?>
                        </table>
                    </div>
                </div>
            </div>
            </div>

<?php
$this->registerJs('
    var data = ['.$dataChart3.'];

$(function() {

    var plotObj = $.plot($("#flot-bar-chart3"), data, {
        series: {
            pie: {
                show: true
            }
        },
        grid: {
            hoverable: true
        },
        tooltip: true,
        tooltipOpts: {
            content: function(label,x,y){
                return label+" "+y+ "km";
            },
            defaultTheme: false
        }
    });

});

'); ?>
