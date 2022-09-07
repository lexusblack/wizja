<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Event; */
$formatter = Yii::$app->formatter;
$user = Yii::$app->user;
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Statystyki'); ?></h3>
<?php if (Yii::$app->session->get('company')!=1){ ?>
                    <div class="alert alert-info">
                    <h4><?php echo Yii::t('app', 'Dostępne wkrótce'); ?></h4>
                </div>
    <?php } else{ /*?>
<div class="row">
    <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Finanse</h5>
                    </div>
                    <div class="ibox-content">

        <?php 
            $offers = $model->getAcceptedOffers();
            $data = [];
            if (isset($offers['error']) && $offers['error']) { ?>
                <div class="alert alert-danger">
                    <h4><?php echo Yii::t('app', 'Brak zaakceptowanej oferty'); ?></h4>
                </div><?php
            }
            else { ?>
                    <?php $offersSummary = $model->getOffersSummary(); ?>
                    <?php $profit = $model->getProfit(); ?>
                    <?php 
                    $i=0;
                    foreach ($offersSummary as $label => $value):
                        if (($label!="Brutto")&&($label!="Suma"))
                        {
                            $obj = [];
                            $obj['name']=$label;
                            $obj['przychod'] = $value;
                            $data[$obj['name']] = $obj;
                        }
                            $i++;
                    endforeach; ?>
                    <?php 
                    $i=0;
                    foreach ($profit as $k => $v):
                    if (($k!="Brutto")&&($k!="Suma"))
                    {
                        if (isset($data[$k]))
                        {
                            $data[$k]['zysk'] = $v; 
                            $data[$k]['koszt'] = $data[$k]['przychod']-$data[$k]['zysk'];                        
                        }else{
                            $data[$k]['zysk'] = $v; 
                            $data[$k]['name']=$k;
                            $data[$k]['przychod'] = 0;
                            $data[$k]['koszt'] = $data[$k]['przychod']-$data[$k]['zysk']; 
                        }
                    }
                    $i++;
                    endforeach; ?>

            <?php }?>
                            <div class="flot-chart">
                                <div class="flot-chart-content" id="morris-bar-chart"></div>
                            </div>
                    </div>
                </div>
            </div>
    </div>
<?php
$chart = "";
foreach ($data as $d)
{
    if ($chart!="")
        $chart.=",";
    $chart .= "{y: '".$d['name']."',a: ".$d['przychod'].", b: ".$d['zysk'].", c: ".$d['koszt']."}";
}
$this->registerJs("
function loadEventChart(){ 
Morris.Bar({
        element: 'morris-bar-chart',
        data: [".$chart."],
        xkey: 'y',
        ykeys: ['a', 'b', 'c'],
        labels: ['Przychód', 'Zysk', 'Koszt'],
        hideHover: 'auto',
        resize: true,
        barColors: ['#1ab394', '#cacaca', '#f8ac59'],
    });
}");
?>
<?php */} ?>
</div>