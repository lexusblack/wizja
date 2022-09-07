<?php
/* @var $this yii\web\View */
/* @var $dashboard \common\models\form\Dashboard */
use common\components\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;
use kartik\helpers\Enum;

$user = Yii::$app->user;

$this->title = Yii::t('app', 'Analiza finansowa');
?>
<?php 
        $sectionList = [Yii::t('app', 'Suma')=>Yii::t('app', 'Suma'), Yii::t('app', 'Transport')=>Yii::t('app', 'Transport'), Yii::t('app', 'Obsługa')=>Yii::t('app', 'Obsługa'), ""=>'bez sekcji'];
        foreach (\common\models\EventExpense::getSectionList() as $s)
        {
            $sectionList[$s] = $s;
        }
?>
        <div class="title_box row">
            <div class="col-lg-6">
            <form class="form-inline">
            <?php 
            $months = Enum::monthList();
            echo Html::a(Html::icon('arrow-left'), ['finance', 'm'=>$prev['m'], 'y'=>$prev['y']], ['class'=>'btn btn-md btn-primary date-drop']); ?>
                <?php echo Html::dropDownList('y', $y, Enum::yearList(2016, date('Y'), true), ['class'=>'form-control date-drop', 'id'=>"year"]); ?>
                <?php echo Html::dropDownList('m',$m, $months, ['class'=>'form-control date-drop', 'id'=>'month']); ?>
                <?php echo Html::a(Html::icon('arrow-right'), ['finance', 'm'=>$next['m'], 'y'=>$next['y']], ['class'=>'btn btn-md  btn-primary date-drop']); ?>
                <?php
                ?>
                </form>
                                <?php
                    $this->registerJs('
                        $(".date-drop").on("change", function(e){
                            location.href="/admin/stat/finance?m="+$("#month").val()+"&y="+$("#year").val();
                        });
                    ');
                ?>
            </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Przychód</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                    <table class="table table-float-right">
                    <tr><th style="text-align:left;">Sekcja</th><th>Przychód <br/>wydarzenia</th><th>Przychód <br/>wypożyczenia</th><th>Koszty <br/>wydarzenia</th><th>Przewidywane<br/> koszty wydarzenia</th><th>Zaliczki</th><th>Zapłacono</th><th>Pensje netto</th><th>Pensje zapł.</th><th>Koszty<br/>stałe</th><th>Inwestycje</th><th>Zysk</th></tr>
                    <?php 
                    $events = $stats->getEventsStats();
                    $rents = $stats->getRentsStats();
                    $month = $stats->getMonthCosts();
                    $invest = $stats->getInvestitions();
                    $salaries = $stats->getEmployeeStats();
                    $salaries_brutto[Yii::t('app', 'Obsługa')] = $salaries['brutto'];
                    $salaries_netto[Yii::t('app', 'Obsługa')] = $salaries['netto'];
                    $salaries_brutto[Yii::t('app', 'Suma')] = $salaries['brutto'];
                    $salaries_netto[Yii::t('app', 'Suma')] = $salaries['netto'];
                    foreach ($sectionList as $val => $s){ 
                        if (!isset($events['value'][$val]))
                        {
                            $events['value'][$val] = 0;
                        }
                        if (!isset($events['cost'][$val]))
                        {
                            $events['cost'][$val] = 0;
                        }
                        if (!isset($rents[$val]))
                        {
                            $rents[$val] = 0;
                        }
                        if (!isset($salaries_brutto[$val]))
                        {
                            $salaries_brutto[$val] = 0;
                        }
                        if (!isset($salaries_netto[$val]))
                        {
                            $salaries_netto[$val] = 0;
                        }
                        if (!isset($month[$val]))
                        {
                            $month[$val] = 0;
                        }
                        if (!isset($invest[$val]))
                        {
                            $invest[$val] = 0;
                        }
                        if (!isset($events['predicted'][$val]))
                        {
                            $events['predicted'][$val] = 0;
                        }
                        if (!isset($events['paid'][$val]))
                        {
                            $events['paid'][$val] = 0;
                        }
                        if (!isset($events['zaliczka'][$val]))
                        {
                            $events['zaliczka'][$val] = 0;
                        }
                        if (!isset($events['salaries'][$val]))
                        {
                            $events['salaries'][$val] = 0;
                        }
                        $profit = $events['value'][$val]-$events['cost'][$val]-$salaries_netto[$val]-$invest[$val]-$month[$val]+$rents[$val];
                        ?>
                    <tr>
                        
                            <td style="text-align:left;"><strong><?=$s?></strong></td>
                            <td><?=Yii::$app->formatter->asCurrency($events['value'][$val])?></td>
                            <td><?=Yii::$app->formatter->asCurrency($rents[$val])?></td>
                            <td><?=Yii::$app->formatter->asCurrency($events['cost'][$val])?></td>
                            <td><?=Yii::$app->formatter->asCurrency($events['predicted'][$val])?></td>
                            <td><?=Yii::$app->formatter->asCurrency($events['zaliczka'][$val])?></td>
                            <td><?=Yii::$app->formatter->asCurrency($events['paid'][$val])?></td>
                            <td><?=Yii::$app->formatter->asCurrency($salaries_netto[$val])?></td>
                            <td><?=Yii::$app->formatter->asCurrency($events['salaries'][$val])?></td>
                            <td><?=Yii::$app->formatter->asCurrency($month[$val])?></td>
                            <td><?=Yii::$app->formatter->asCurrency($invest[$val])?></td>
                            <td><?=Yii::$app->formatter->asCurrency($profit)?></td>
                            
                    </tr>
                    <?php  }  
                     ?>
                    </table>
                    </div>
                </div>
            </div>
            </div>

<?php $this->registerCss('
    .table.table-float-right th, td{
        text-align:right;}');
?>