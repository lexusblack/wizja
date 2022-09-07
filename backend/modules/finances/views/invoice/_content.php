<?php
use yii\bootstrap\Html;
use common\models\InvoiceContent;
use yii\grid\GridView;
use common\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model common\models\Invoice */



$formatter = Yii::$app->formatter;
$formatter->currencyCode = $model['currency'];

?>
<h3><?php echo ArrayHelper::getValue(\common\models\Invoice::getTypeList(), $model['type'], UNDEFINDED_STRING); ?></h3>
<div class="row">
    <div class="col-xs-4 text-center">
        <?php
        if ($data['seller']['logo']) {
            $logo = Yii::getAlias('@uploads/settings/' . $data['seller']['logo']);
            echo Html::img($logo, ['height'=>'100']);
        }
        ?>

    </div>
    <div class="pull-left">
        <table class="table table-striped">
            <thead>
            <tr>
                <th colspan="4" class="text-center"><?php echo Yii::t('app', 'Faktura nr'); ?> <?php echo $model['fullnumber']; ?>
                    <?php if ($data2){ echo Yii::t('app', ' do ').$data2['model']['fullnumber'];}?>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th><?php echo $tmpModel->getAttributeLabel('date'); ?></th>
                <td><?php echo $model['date']; ?></td>
                <th><?php echo $tmpModel->getAttributeLabel('disposaldate'); ?></th>
                <td><?php echo $model['disposaldate']; ?></td>
            </tr>
            <tr>
                <th><?php echo $tmpModel->getAttributeLabel('paymentdate'); ?></th>
                <td><?php echo $model['paymentdate']; ?></td>
                <th><?php echo $tmpModel->getAttributeLabel('paymentmethod'); ?></th>
                <td><?php echo $data['labels']['paymentMethod']; ?></td>
            </tr>

            </tbody>
        </table>

    </div>
</div>
<div class="row">
    <div class="col-xs-6">
        <h4><?php echo Yii::t('app', 'Sprzedawca'); ?></h4>
        <strong><?php echo $data['seller']['name']; ?></strong><br />
        <?php echo $data['seller']['address']; ?><br />
        <?php echo $data['seller']['zip']." ".$data['seller']['city']; ?><br />
        <strong><?php echo Yii::t('app', 'NIP'); ?>:</strong> <?php echo $data['seller']['nip']; ?><br />
        <strong><?php echo Yii::t('app', 'Bank'); ?>:</strong> <?php echo $data['seller']['bankName']; ?><br />
        <strong><?php echo Yii::t('app', 'Nr konta'); ?>:</strong> <?php echo $data['seller']['bankNumber']; ?>

    </div>
    <div class="pull-left">
        <h4><?php echo Yii::t('app', 'Nabywca'); ?></h4>
        <strong><?php echo $data['buyer']['name']; ?></strong><br />
        <?php echo $data['buyer']['address']; ?><br />
        <?php echo $data['buyer']['zip']." ".$data['buyer']['city']; ?><br />
        <strong><?php echo Yii::t('app', 'NIP'); ?>:</strong> <?php echo $data['buyer']['nip']; ?><br />
        <strong><?php echo Yii::t('app', 'Nr konta'); ?>:</strong> <?php echo $data['buyer']['bankNumber']; ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-6">
        <table class="">
            <tbody>
            <tr>
                <th></th>
                <td></td>
            </tr>
            <tr>
                <th></th>
                <td></td>
            </tr>
            </tbody>
        </table>

    </div>
    <div class="pull-left">
        <table class="table table-striped">
            <tbody>
            <tr>
                <th><?php echo $tmpModel->getAttributeLabel('total'); ?></th>
                <td><?php echo $formatter->asCurrency($model['total'], $model['currency']); ?></td>
            </tr>
            <tr>
                <th><?php echo $tmpModel->getAttributeLabel('remaining'); ?></th>
                <td><?php echo $formatter->asCurrency($model['remaining'], $model['currency']); ?></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<?php if ($data2){ ?>
<div class="row">
    <div class="col-lg-12">
        <h4><?php echo Yii::t('app', 'Stan poprzedni'); ?></h4>
        <?php 
        $show = false;
        foreach ($data2['invoiceContents'] as $i)
        {
            if ($i['discount_percent']>0)
                $show = true;
        }
        echo GridView::widget([
            'dataProvider'=>new \yii\data\ArrayDataProvider(['allModels'=>$data2['invoiceContents'], 'sort'=>false, 'pagination' => false]),
            'layout' => "{items}",
            'columns' => [
	            [
		            'attribute' => 'name',
		            'label' => $tmpContent->getAttributeLabel('name'),
	            ],
	            [
		            'attribute' => 'classification',
		            'label' => $tmpContent->getAttributeLabel('classification'),
	            ],
	            [
		            'attribute' => 'unit',
		            'label' => $tmpContent->getAttributeLabel('unit'),
	            ],
	            [
		            'attribute' => 'count',
		            'label' => $tmpContent->getAttributeLabel('count'),
		            'format' => 'decimal',
	            ],
	            [
		            'attribute' => 'price',
		            'label' => $tmpContent->getAttributeLabel('price'),
		            'format' => ['currency', $model['currency']],
	            ],
	            [
		            'attribute'=>'discount_percent',
		            'label' => $tmpContent->getAttributeLabel('discount_percent'),
                    'visible'=>$show,
		            'value'=>function($model) use ($formatter)
		            {
			            return $formatter->asPercent($model['discount_percent']/100);
		            }
	            ],
	            [
		            'attribute'=>'vat',
		            'label' => $tmpContent->getAttributeLabel('vat'),
		            'value'=>function($model) use ($formatter)
		            {
			            return $formatter->asPercent($model['vat']/100);
		            }
	            ],
	            [
		            'attribute' => 'netto',
		            'label' => $tmpContent->getAttributeLabel('netto'),
		            'format' => ['currency', $model['currency']],
	            ],
	            [
		            'attribute' => 'tax',
		            'label' => $tmpContent->getAttributeLabel('tax'),
		            'format' => ['currency', $model['currency']],
	            ],
	            [
		            'attribute' => 'brutto',
		            'label' => $tmpContent->getAttributeLabel('brutto'),
		            'format' => ['currency', $model['currency']],
	            ],
            ]
        ]);
        ?>

    </div>
</div>

<?php } ?>

<div class="row">
    <div class="col-lg-12">
        <h4><?php if ($data2){ ?>
                        <h4><?= Yii::t('app', 'Stan aktualny') ?></h4>
                    <?php }else { ?>
                        <h4><?= Yii::t('app', 'Pozycje na dokumencie') ?></h4>
                    <?php } ?></h4>
        <?php 
        $show = false;
        foreach ($data['invoiceContents'] as $i)
        {
            if ($i['discount_percent']>0)
                $show = true;
        }
        echo GridView::widget([
            'dataProvider'=>new \yii\data\ArrayDataProvider(['allModels'=>$data['invoiceContents'], 'sort'=>false, 'pagination' => false]),
            'layout' => "{items}",
            'columns' => [
                [
                    'attribute' => 'name',
                    'label' => $tmpContent->getAttributeLabel('name'),
                ],
                [
                    'attribute' => 'classification',
                    'label' => $tmpContent->getAttributeLabel('classification'),
                ],
                [
                    'attribute' => 'unit',
                    'label' => $tmpContent->getAttributeLabel('unit'),
                ],
                [
                    'attribute' => 'count',
                    'label' => $tmpContent->getAttributeLabel('count'),
                    'format' => 'decimal',
                ],
                [
                    'attribute' => 'price',
                    'label' => $tmpContent->getAttributeLabel('price'),
                    'format' => ['currency', $model['currency']],
                ],
                [
                    'attribute'=>'discount_percent',
                    'label' => $tmpContent->getAttributeLabel('discount_percent'),
                    'visible'=>$show,
                    'value'=>function($model) use ($formatter)
                    {
                        return $formatter->asPercent($model['discount_percent']/100);
                    }
                ],
                [
                    'attribute'=>'vat',
                    'label' => $tmpContent->getAttributeLabel('vat'),
                    'value'=>function($model) use ($formatter)
                    {
                        return $formatter->asPercent($model['vat']/100);
                    }
                ],
                [
                    'attribute' => 'netto',
                    'label' => $tmpContent->getAttributeLabel('netto'),
                    'format' => ['currency', $model['currency']],
                ],
                [
                    'attribute' => 'tax',
                    'label' => $tmpContent->getAttributeLabel('tax'),
                    'format' => ['currency', $model['currency']],
                ],
                [
                    'attribute' => 'brutto',
                    'label' => $tmpContent->getAttributeLabel('brutto'),
                    'format' => ['currency', $model['currency']],
                ],
            ]
        ]);
        ?>

    </div>
</div>
<div class="row">
    <div class="pull-left col-xs-offset-6">
        <table class="table">
            <tr>
                <th><?php echo Yii::t('app', 'Kwota netto'); ?></th>
                <th><?php echo Yii::t('app', 'Kwota VAT'); ?></th>
                <th><?php echo Yii::t('app', 'Kwota brutto'); ?></th>
            </tr>
            <tr>
                <td><?php echo $formatter->asCurrency($model['netto'], $model['currency']); ?></td>
                <td><?php echo $formatter->asCurrency($model['tax'], $model['currency']); ?></td>
                <td><?php echo $formatter->asCurrency($model['total'], $model['currency']); ?></td>
            </tr>

        </table>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <strong><?php echo Yii::t('app', 'Uwagi'); ?>:</strong><br/>
        <?php echo nl2br($model['description']); ?>

    </div>
</div>
<div class="row">
    <div class="col-lg-12 text-right">
        <strong><?php echo Yii::t('app', 'Wystawił'); ?>:</strong>
        <?php
        if ($model['creator'] !== null)
        {
            echo $data['labels']['creator'];
        }
        ?>

    </div>
</div>