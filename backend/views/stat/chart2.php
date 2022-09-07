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
            <div class="col-lg-12">
            <form class="form-inline">
            <?php 
            $months = Enum::monthList();
            $months = array_merge([Yii::t('app', 'Wszystkie')], $months);
            $cats = \common\models\GearCategory::getMainRootList();
            echo Html::a(Html::icon('arrow-left'), ['chart2', 'm'=>$prev['m'], 'y'=>$prev['y']], ['class'=>'btn btn-md btn-primary date-drop']); ?>
                <?php echo Html::dropDownList('y', $y, Enum::yearList(2016, date('Y'), true), ['class'=>'form-control date-drop', 'id'=>"year"]); ?>
                <?php echo Html::dropDownList('m',$m, $months, ['class'=>'form-control date-drop', 'id'=>'month']); ?>
                <?php echo Html::a(Html::icon('arrow-right'), ['chart2', 'm'=>$next['m'], 'y'=>$next['y']], ['class'=>'btn btn-md  btn-primary date-drop']); ?>
                <?php
                echo Html::dropDownList('category_id', $category_id, $cats , ['class'=>'form-control date-drop', 'id'=>"cat"]);
                ?>
                </form>
                                <?php
                    $this->registerJs('
                        $(".date-drop").on("change", function(e){
                            location.href="/admin/stat/chart2?m="+$("#month").val()+"&y="+$("#year").val()+"&category_id="+$("#cat").val();
                        });
                    ');
                ?>
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
                        <h5>Wypożyczenia - suma wydanych pieniędzy</h5>
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
            <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Najdłużej wypożyczany sprzęt</h5>
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

<?php
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
$dataChart = "";
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
            $dataChart.=",";
            $ticks .= ",";
            $ticks2 .=",";
        }
        $dataChart.= "[".$i.",".$s['quantity']."]";
        $ticks .= "[".$i.",".'"'.$s['name'].'"'."]";
        $ticks2 .= "[".$i.",".'"'.substr($s['name'],0,10).'"'."]";       
    }

}

$dataChart3 = "";
$ticks3 = "";
$ticks32 = "";
for ($i=0; $i<20; $i++)
{
    if (isset($stats['chart3'][$i]))
    {
        $s = $stats['chart3'][$i];
        $s['name'] = str_replace('"', '', $s['name']);
        if ($i>0)
        {
            $dataChart3.=",";
            $ticks3 .= ",";
            $ticks32 .=",";
        }
        $dataChart3.= "[".$i.",".$s['quantity']."]";
        $ticks3 .= "[".$i.",".'"'.$s['name'].'"'."]";
        $ticks32 .= "[".$i.",".'"'.substr($s['name'],0,10).'"'."]";       
    }

}
$this->registerJs('
    var tticks = ['.$tticks.'];
    var tticks2 = ['.$tticks2.'];
$(function() {
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
    var ticks = ['.$ticks.'];
    var ticks2 = ['.$ticks2.'];
$(function() {
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
    var barData2 = {
        label: "Liczba wypożyczeń",
        data: [
            '.$dataChart.'
        ]
    };
    $.plot($("#flot-bar-chart"), [barData2], barOptions2);
});

    var ticks3 = ['.$ticks3.'];
    var ticks32 = ['.$ticks32.'];
$(function() {
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
            ticks: ticks32
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
                return label+" "+y+", "+ticks3[x];
            },
        }
    };
    var barData2 = {
        label: "Pieniądze",
        data: [
            '.$dataChart3.'
        ]
    };
    $.plot($("#flot-bar-chart3"), [barData2], barOptions2);
});
'); ?>
