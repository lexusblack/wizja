<?php
/* @var $this yii\web\View */
/* @var $dashboard \common\models\form\Dashboard */
use kartik\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;
use kartik\helpers\Enum;
$formatter = Yii::$app->formatter;

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
            echo Html::a(Html::icon('arrow-left'), ['chart4', 'm'=>$prev['m'], 'y'=>$prev['y']], ['class'=>'btn btn-md btn-primary date-drop']); ?>
                <?php echo Html::dropDownList('y', $y, Enum::yearList(2016, date('Y'), true), ['class'=>'form-control date-drop', 'id'=>"year"]); ?>
                <?php echo Html::dropDownList('m',$m, $months, ['class'=>'form-control date-drop', 'id'=>'month']); ?>
                <?php echo Html::a(Html::icon('arrow-right'), ['chart4', 'm'=>$next['m'], 'y'=>$next['y']], ['class'=>'btn btn-md  btn-primary date-drop']); ?>

                </form>
                                <?php
                    $this->registerJs('
                        $(".date-drop").on("change", function(e){
                            location.href="/admin/stat/chart4?m="+$("#month").val()+"&y="+$("#year").val()+"&category_id=";
                        });
                    ');
                ?>
            </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><?=Yii::t('app', 'Zarobki per Klient')?></h5>
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
                            <table class="table">
                            <tr><th>#</th><th><?=Yii::t('app', 'Klient')?></th><th><?=Yii::t('app', 'Suma obrotu')?></th><th><?=Yii::t('app', 'Suma kosztów')?></th><th><?=Yii::t('app', 'Suma zysku')?></th></tr>
                            <?php $i =0; 
                            foreach ($stats as $s){
                                $i++;
                                ?>
                                <tr><td><?=$i?></td><td><?=$s['name']?></td><td><?=$formatter->asCurrency($s['value'])?></td><td><?=$formatter->asCurrency($s['cost'])?></td><td><?=$formatter->asCurrency($s['value']-$s['cost'])?></td></tr>
                            <?php    } ?>
                            </table>
                    </div>
                </div>

            </div>
            </div>
           