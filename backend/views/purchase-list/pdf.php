<?php
/* @var $this yii\web\View */
/* @var $model common\models\Offer */
/* @var $offerForm \backend\modules\offers\models\OfferForm */

$formatter = Yii::$app->formatter;
use yii\helpers\Html;

?>


<div class="pdf_box">
        <div class="client_info">
            <h1><?=$model->name." ["."LZ/".substr($model->datetime,0,4)."/".$model->id."]"?></h1>
        </div>

<table class="table table-row-border">
<tr><th>#</th><th><?=Yii::t('app', 'Nazwa')?></th><th><?=Yii::t('app', 'Ilość')?></th><th><?=Yii::t('app', 'Firma')?></th><th><?=Yii::t('app', 'Adres')?></th><th><?=Yii::t('app', 'Uwagi')?></th><th><?=Yii::t('app', 'Koszt')?></th></tr>
<?php $i =0; $total = 0; foreach ($model->purchaseListItems as $item)
{ $i++;
	$total +=$item->quantity*$item->price;
    ?>
<tr><td><?=$i?>.</td><td><?= $item->name ?></td><td><?= $item->quantity ?></td><td><?= $item->company_name ?></td><td><?= $item->company_address ?></td><td><?= $item->description ?></td><td><?= $formatter->asCurrency($item->quantity*$item->price) ?></td></tr>
<?php } ?>
<tr style="background-color:#fafafa"><td colspan="6" style="text-align:right; font-weight:bold"><?=Yii::t('app', 'Łączny przewidywany koszt:')?></td><td><?= $formatter->asCurrency($total) ?></td></tr>
</table>

</div>

