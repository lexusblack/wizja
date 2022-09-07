<?php
/* @var $this yii\web\View */
/* @var $dashboard \common\models\form\Dashboard */
use kartik\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;
use kartik\helpers\Enum;

$user = Yii::$app->user;
$formatter = Yii::$app->formatter;

$this->title = Yii::t('app', 'Statysyki');
?>
        <div class="title_box row">
            <div class="col-lg-12">
            <form class="form-inline">
            <?php 
            $months = Enum::monthList();
            $months = array_merge([Yii::t('app', 'Wszystkie')], $months);
            $cats = \common\models\GearCategory::getMainRootList();
            echo Html::a(Html::icon('arrow-left'), ['chart1', 'm'=>$prev['m'], 'y'=>$prev['y']], ['class'=>'btn btn-md btn-primary date-drop']); ?>
                <?php echo Html::dropDownList('y', $y, Enum::yearList(2016, date('Y'), true), ['class'=>'form-control date-drop', 'id'=>"year"]); ?>
                <?php echo Html::dropDownList('m',$m, $months, ['class'=>'form-control date-drop', 'id'=>'month']); ?>
                <?php echo Html::a(Html::icon('arrow-right'), ['chart1', 'm'=>$next['m'], 'y'=>$next['y']], ['class'=>'btn btn-md  btn-primary date-drop']); ?>
                <?php
                echo Html::dropDownList('category_id', $category_id, $cats , ['class'=>'form-control date-drop', 'id'=>"cat"]);
                ?>
                </form>
                                <?php
                    $this->registerJs('
                        $(".date-drop").on("change", function(e){
                            location.href="/admin/stat/chart1?m="+$("#month").val()+"&y="+$("#year").val()+"&category_id="+$("#cat").val();
                        });
                    ');
                ?>
            </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><?=Yii::t('app', 'Najczęściej wykorzystywany sprzęt')?></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content" style="height:300px;">
                            <div class="flot-chart">
                                <div class="flot-chart-content" id="flot-bar-chart2"></div>
                            </div>
                    </div>
                </div>
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><?=Yii::t('app', 'Najczęściej wykorzystywany sprzęt')?></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content" style="height:300px; overflow-y: scroll">
                            <table class="table">
                            <tr><th>#</th><th><?=Yii::t('app', 'Nazwa')?></th><th><?=Yii::t('app', 'l. użyć')?></th></tr>
                            <?php $i =0; 
                            foreach ($stats['chart2'] as $s){
                                $i++;
                                ?>
                                <tr><td><?=$i?></td><td><?=$s['name']?></td><td><?=$s['quantity']?></td></tr>
                            <?php    } ?>
                            </table>
                    </div>
                </div>

            </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><?=Yii::t('app', 'Najlepiej zarabiający sprzęt (suma)')?></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content" style="height:300px;">
                            <div class="flot-chart">
                                <div class="flot-chart-content" id="flot-bar-chart4"></div>
                            </div>
                    </div>
                </div>
                                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><?=Yii::t('app', 'Najlepiej zarabiający sprzęt (suma)')?></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content" style="height:300px; overflow-y: scroll">
                            <table class="table">
                            <tr><th>#</th><th><?=Yii::t('app', 'Nazwa')?></th><th><?=Yii::t('app', 'Najlepiej zarabiający sprzęt (suma)')?></th></tr>
                            <?php $i =0; 
                            foreach ($stats['chart4'] as $s){
                                $i++;
                                ?>
                                <tr><td><?=$i?></td><td><?=$s['name']?></td><td><?=$formatter->asCurrency($s['total'])?></td></tr>
                            <?php    } ?>
                            </table>
                    </div>
                </div>
            </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><?=Yii::t('app', 'Najlepiej zarabiający sprzęt na sztukę')?></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content" style="height:300px;">
                            <div class="flot-chart">
                                <div class="flot-chart-content" id="flot-bar-chart3"></div>
                            </div>
                    </div>
                </div>
                        <div class="ibox-content" style="height:300px; overflow-y: scroll">
                            <table class="table">
                            <tr><th>#</th><th><?=Yii::t('app', 'Nazwa')?></th><th><?=Yii::t('app', 'Najlepiej zarabiający sprzęt na sztukę')?></th></tr>
                            <?php $i =0; 
                            foreach ($stats['chart3'] as $s){
                                $i++;
                                ?>
                                <tr><td><?=$i?></td><td><?=$s['name']?></td><td><?=$formatter->asCurrency($s['quantity'])?></td></tr>
                            <?php    } ?>
                            </table>
                    </div>
            </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><?=Yii::t('app', 'Najdłużej używany sprzęt')?></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content" style="height:300px;">
                    <?php if ($m==0){ ?>
                        <div class="alert alert-danger">
                                <?=Yii::t('app', 'Zbyt duży okres, żeby wyświetlić ten wykres')?>.
                            </div>
                    <?php } ?>
                            <div class="flot-chart">
                                <div class="flot-chart-content" id="flot-bar-chart"></div>
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
        $ticks2 .= "[".$i.",".'"'.mb_substr($s['name'],0,30).'"'."]";       
    }

}
$dataChart2 = "";
$tticks = "";
$tticks2 = "";
$i=0;
foreach ($stats['chart2'] as $s)
{
        if ($i<20)
        {
             $s['name'] = str_replace('"', '', $s['name']);
            if ($i>0)
            {
                $dataChart2.=",";
                $tticks .= ",";
                $tticks2 .=",";
            }
            $dataChart2.= "[".$i.",".$s['quantity']."]";
            $tticks .= "[".$i.",".'"'.$s['name'].'"'."]";
            $tticks2 .= "[".$i.",".'"'.mb_substr($s['name'],0,30).'"'."]";            
        }
        $i++;
      

}
$dataChart3= "";
$ttticks = "";
$ttticks2 = "";
$i=0;
foreach ($stats['chart3'] as $s)
{
        if ($i<20)
        {
             $s['name'] = str_replace('"', '', $s['name']);
            if ($i>0)
            {
                $dataChart3.=",";
                $ttticks .= ",";
                $ttticks2 .=",";
            }
            $dataChart3.= "[".$i.",".$s['quantity']."]";
            $ttticks .= "[".$i.",".'"'.$s['name'].'"'."]";
            $ttticks2 .= "[".$i.",".'"'.substr($s['name'],0,30).'"'."]";            
        }
        $i++;
      

}
$dataChart4= "";
$ttticks4 = "";
$ttticks42 = "";
$i=0;
foreach ($stats['chart4'] as $s)
{
        if ($i<20)
        {
             $s['name'] = str_replace('"', '', $s['name']);
            if ($i>0)
            {
                $dataChart4.=",";
                $ttticks4 .= ",";
                $ttticks42 .=",";
            }
            $dataChart4.= "[".$i.",".$s['total']."]";
            $ttticks4 .= "[".$i.",".'"'.$s['name'].'"'."]";
            $ttticks42 .= "[".$i.",".'"'.substr($s['name'],0,30).'"'."]";            
        }
        $i++;
      

}
$this->registerJs('
    var ticks = ['.$ticks.'];
    var ticks2 = ['.$ticks2.'];
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

    var ttticks = ['.$ttticks.'];
    var ttticks2 = ['.$ttticks2.'];

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
            ticks: ttticks2
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
                return label+" "+y+", "+ttticks[x];
            },
        }
    };
    var barData = {
        label: "Zysk",
        data: [
            '.$dataChart3.'
        ]
    };
    $.plot($("#flot-bar-chart3"), [barData], barOptions);

    var tticks = ['.$tticks.'];
    var tticks2 = ['.$tticks2.'];
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
    var barData = {
        label: "Liczba eventów",
        data: [
            '.$dataChart2.'
        ]
    };
    $.plot($("#flot-bar-chart2"), [barData], barOptions);

    var tticks4 = ['.$ttticks4.'];
    var tticks42 = ['.$ttticks42.'];
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
            ticks: tticks42
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
                return label+" "+y+", "+tticks4[x];
            },
        }
    };
    var barData = {
        label: "Zysk",
        data: [
            '.$dataChart4.'
        ]
    };
    $.plot($("#flot-bar-chart4"), [barData], barOptions);
});
'); ?>
