<?php
/* @var $this yii\web\View */
/* @var $dashboard \common\models\form\Dashboard */
use kartik\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;
use kartik\helpers\Enum;

$user = Yii::$app->user;

$this->title = Yii::t('app', 'Statysyki');
?>
        <div class="title_box row">
            <div class="col-lg-6">
            <form class="form-inline">
            <?php 
            $months = Enum::monthList();
            $cats = \common\models\GearCategory::getMainRootList();
            echo Html::a(Html::icon('arrow-left'), ['stats', 'm'=>$prev['m'], 'y'=>$prev['y']], ['class'=>'btn btn-md btn-primary date-drop']); ?>
                <?php echo Html::dropDownList('y', $y, Enum::yearList(2016, date('Y'), true), ['class'=>'form-control date-drop', 'id'=>"year"]); ?>
                <?php echo Html::dropDownList('m',$m, $months, ['class'=>'form-control date-drop', 'id'=>'month']); ?>
                <?php echo Html::a(Html::icon('arrow-right'), ['stats', 'm'=>$next['m'], 'y'=>$next['y']], ['class'=>'btn btn-md  btn-primary date-drop']); ?>
                <?php
                echo Html::dropDownList('category_id', $category_id, $cats , ['class'=>'form-control date-drop', 'id'=>"cat"]);
                ?>
                </form>
                                <?php
                    $this->registerJs('
                        $(".date-drop").on("change", function(e){
                            location.href="/admin/site/stats?m="+$("#month").val()+"&y="+$("#year").val()+"&category_id="+$("#cat").val();
                        });
                    ');
                ?>
            </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Najczęściej używany sprzęt</h5>
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
                                <div class="flot-chart-content" id="flot-bar-chart"></div>
                            </div>
                    </div>
                </div>
            </div>
            </div>
            <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Najczęściej wypożyczany sprzęt</h5>
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
                            <div class="flot-chart-content" id="flot-bar-chart2"></div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            <div class="row">
            <div class="col-lg-6">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Pojazdy - kilometrówka</h5>
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
                    </div>
                </div>
            </div>
            </div>

<?php
$dataChart1 = "";
$ticks = "";
$ticks2 = "";
for ($i=0; $i<20; $i++)
{
    if (isset($stats['chart1'][$i]))
    {
        $s = $stats['chart1'][$i];
        $s['name'] = str_replace('"', '', $s['name']);
        if ($i>0)
        {
            $dataChart1.=",";
            $ticks .= ",";
            $ticks2 .=",";
        }
        $dataChart1.= "[".$i.",".$s['days']."]";
        $ticks .= "[".$i.",".'"'.$s['name'].'"'."]";
        $ticks2 .= "[".$i.",".'"'.substr($s['name'],0,8).'"'."]";       
    }

}
$dataChart2 = "";
$tticks = "";
$tticks2 = "";
for ($i=0; $i<20; $i++)
{
    if (isset($stats['chart2'][$i]))
    {
        $s = $stats['chart2'][$i];
        $s['name'] = str_replace('"', '', $s['name']);
        if ($i>0)
        {
            $dataChart2.=",";
            $tticks .= ",";
            $tticks2 .=",";
        }
        $dataChart2.= "[".$i.",".$s['days']."]";
        $tticks .= "[".$i.",".'"'.$s['name'].'"'."]";
        $tticks2 .= "[".$i.",".'"'.substr($s['name'],0,10).'"'."]";       
    }

}
$dataChart3 = "";
$i=0;
$color =["#d3d3d3", "#bababa", "#79d2c0", "#1ab394", "#b5b8cf", "#e4f0fb", "#f9ac5a", "#25c5c8", "#304151"];
foreach ($stats['chart3'] as $car)
{
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
$this->registerJs('
    var ticks = ['.$ticks.'];
    var ticks2 = ['.$ticks2.'];
    var tticks = ['.$tticks.'];
    var tticks2 = ['.$tticks2.'];
    var data = ['.$dataChart3.'];
$(function() {
    var barOptions = {
        series: {
            bars: {
                show: true,
                barWidth: 0.6,
                fill: true,
                fillColor: {
                    colors: [{
                        opacity: 0.8
                    }, {
                        opacity: 0.8
                    }]
                }
            }
        },
        xaxis: {
            ticks: ticks2
        },
        colors: ["#1ab394"],
        grid: {
            color: "#999999",
            hoverable: true,
            clickable: true,
            tickColor: "#D4D4D4",
            borderWidth:0
        },
        legend: {
            show: false
        },
        tooltip: true,
        tooltipOpts: {
            content: function(label,x,y){
                return label+" "+y+", "+ticks[x];
            },
        }
    };
    var barData = {
        label: "Liczba dni",
        data: [
            '.$dataChart1.'
        ]
    };
    $.plot($("#flot-bar-chart"), [barData], barOptions);
    var barOptions2 = {
        series: {
            bars: {
                show: true,
                barWidth: 0.6,
                fill: true,
                fillColor: {
                    colors: [{
                        opacity: 0.8
                    }, {
                        opacity: 0.8
                    }]
                }
            }
        },
        xaxis: {
            ticks: tticks2
        },
        colors: ["#1ab394"],
        grid: {
            color: "#999999",
            hoverable: true,
            clickable: true,
            tickColor: "#D4D4D4",
            borderWidth:0
        },
        legend: {
            show: false
        },
        tooltip: true,
        tooltipOpts: {
            content: function(label,x,y){
                return label+" "+y+", "+tticks[x];
            },
        }
    };
    var barData2 = {
        label: "Liczba dni",
        data: [
            '.$dataChart2.'
        ]
    };
    $.plot($("#flot-bar-chart2"), [barData2], barOptions2);
}); 

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
