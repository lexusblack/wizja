<?php

use kartik\widgets\Select2;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use common\models\Gear;
use yii\helpers\ArrayHelper;

$this->title = Yii::t('app', 'Inwentaryzacje');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="outcomes-warehouse-create">
<div class="row">
<div class="ibox">
<div class="ibox-content">

    <h1><?= Html::encode($this->title) ?></h1>
        <p><?= Html::a(Yii::t('app', 'Nowa inwentaryzacja'), ['stocktaking'], ['class'=>'btn btn-primary btn-xs'])." ";?>
            <?= Html::a(Yii::t('app', 'Zaginione'), ['stocktaking-lost'], ['class'=>'btn btn-danger btn-xs', 'title'=>Yii::t('app', 'Sprzęty nieinwentaryzowane od 30 dni.')])." ";?>
        </p>
    <div class="outcomes-warehouse-form">
    </div>
    <div class="panel_mid_blocks">
        <div class="panel_block">
            <table class="kv-grid-table table kv-table-wrap" id="stocktaking-table">
                <tr>
                    <th><?= Yii::t('app', 'Numer') ?></th>
                    <th><?= Yii::t('app', 'Data') ?></th>
                    <th><?= Yii::t('app', 'Inwentaryzował') ?></th>
                    <th></th>
                </tr>
                <?php foreach ($models as $model)
                { 
                    ?>
                <tr>
                    <td><?= Html::a(Yii::t('app', 'Inwentaryzacja nr ').$model->id, ['stocktaking-report', 'id'=>$model->id]) ?></td>
                    <td><?= $model->datetime ?></th>
                    <td><?= $model->user->displayLabel?></td>
                    <td><?= Html::a(Yii::t('app', 'PDF'), ['stocktaking-report', 'id'=>$model->id, 'pdf'=>true], ['class'=>'btn btn-xs btn-danger']) ?></td>
                </tr>
                <?php } ?>
            </table>
            
        </div>
    </div>

</div>
</div>
</div>
</div>
