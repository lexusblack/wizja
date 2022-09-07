<?php

use kartik\widgets\Select2;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use common\models\Gear;
use yii\helpers\ArrayHelper;

$this->title = Yii::t('app', 'Zaginione');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Inwentaryzacje'), 'url' => ['stocktakings']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="outcomes-warehouse-create">
<div class="row">
<div class="ibox">
<div class="ibox-content">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="outcomes-warehouse-form">
    </div>
    <div class="panel_mid_blocks">
        <div class="panel_block">
        <table class="kv-grid-table table kv-table-wrap" id="stocktaking-table">
        <?php foreach ($mainCategories as $cat){
                
                $ids = $cat->children()->column();
                $categoryIds = array_merge([$cat->id], $ids);
                $gearsModel = Gear::find()->where(['category_id'=>$categoryIds])->andWhere(['or', ['id'=>$gears], ['no_items'=>1]])->andWhere(['active'=>1])->all();
                if ($cat->color)
                        {
                            $style= "style='background-color:".$cat->color.";'";
                        }else{
                            $style = "";
                        }
                if ($gearsModel) {
            ?>
            <tr <?=$style?>><td colspan="5"><strong><?=$cat->name?></strong></td></tr>
            
                <tr>
                    <th style="width: 70px;"><?= Yii::t('app', 'Id') ?></th>
                    <th><?= Yii::t('app', 'Nazwa') ?></th>
                    <th><?= Yii::t('app', 'Brakujące') ?></th>
                    <th><?= Yii::t('app', 'L. w magazynie') ?></th>
                    
                    <th><?= Yii::t('app', 'Numery') ?></th>
                </tr>
                <?php foreach ($gearsModel as $gear)
                { 
                    if ($gear->no_items)
                    {
                        $quantity = $gear->quantity;
                        $gearItem = $gear->getGearItems()->andWhere(['active'=>1])->one();
                        $numbers = "";
                        $scanned = $gear->quantity;
                        if ($gearItem){
                            if ($gearItem->test_date<$date){
                                $scanned = $gear->quantity;
                            }else{
                                $scanned = $gear->quantity - intval($gearItem->test_status);
                            }
                            
                        }

                    }
                    else
                    {
                        $quantity = $gear->getGearItems()->andWhere(['active'=>1])->count();
                        $scanned = 0;
                        $numbers = "";
                        $itemsScanned = $gear->getGearItems()->andWhere(['active'=>1])->andWhere(['or', ['<', 'test_date', $date], ['test_date'=>null]])->all();
                        foreach ($itemsScanned as $i) {
                            $scanned += 1;
                            if (!$gear->no_items)
                                $numbers .= $i->number.", ";
                        }
                    }
                    
                    ?>
                <?php if ($scanned>0) { ?>
                <tr>
                    <td style="width: 70px;"><?= $gear->id ?></td>
                    <td><?= $gear->name ?></th>
                    <td style="text-align:center"><?= $scanned?></td>
                    <td style="text-align:center"><?= $quantity ?></td>
                    
                    <td style="text-align:center"><?= $numbers ?></td>
                </tr>
                <?php } ?>
                <?php } ?>
            
        <?php } } ?>
            </table>
        </div>
    </div>

</div>
</div>
</div>
</div>
