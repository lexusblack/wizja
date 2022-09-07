<?php
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */



$formatter = Yii::$app->formatter;
$formatter->currencyCode = $model['currency'];

?>
<h3><?= Yii::t('app', 'Koszt') ?></h3>
<div class="row">
    <div class="col-xs-4 text-center">
        &nbsp;
    </div>
    <div class="pull-left">
        <table class="table table-striped">
            <thead>
            <tr>
                <th colspan="4" class="text-center"><?php echo Yii::t('app', 'Koszt nr'); ?> <?php echo $model['number']; ?></th>
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
        <?php echo $data['seller']['city']; ?><br />
        <strong><?php echo Yii::t('app', 'NIP'); ?>:</strong> <?php echo $data['seller']['nip']; ?><br />
        <strong><?php echo Yii::t('app', 'Bank'); ?>:</strong> <?php echo $data['seller']['bankName']; ?><br />
        <strong><?php echo Yii::t('app', 'Nr konta'); ?>:</strong> <?php echo $data['seller']['bankNumber']; ?>

    </div>
    <div class="pull-left">
        <h4><?php echo Yii::t('app', 'Nabywca'); ?></h4>
        <strong><?php echo $data['buyer']['name']; ?></strong><br />
        <?php echo $data['buyer']['address']; ?><br />
        <?php echo $data['buyer']['city']; ?><br />
        <strong><?php echo Yii::t('app', 'NIP'); ?>:</strong> <?php echo $data['buyer']['nip']; ?><br />
        <strong><?php echo Yii::t('app', 'Nr konta'); ?>:</strong> <?php echo $data['buyer']['bankNumber']; ?>
    </div>
</div>
<br class="clear" />
<div class="row">
    <div class="col-xs-6">
        <table class="table table-striped">
            <tbody>
            <tr>
                <th><?php echo $tmpModel->getAttributeLabel('payment_date'); ?></th>
                <td><?php echo $model['payment_date']; ?></td>
            </tr>
            <tr>
                <th><?php echo $tmpModel->getAttributeLabel('alreadypaid'); ?></th>
                <td><?php echo $formatter->asCurrency($model['alreadypaid'], $model['currency']); ?></td>
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
<div class="row">
    <div class="col-lg-12">
        <h4><?= Yii::t('app', 'Stawki na dokumencie') ?></h4>
        <?php echo GridView::widget([
            'dataProvider'=>new \yii\data\ArrayDataProvider(['allModels'=>$data['expenseContentRates'], 'sort'=>false]),
            'columns' => [
                [
                    'attribute'=>'vat',
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
    <div class="col-lg-12">
        <h4><?= Yii::t('app', 'Pozycje na dokumencie') ?></h4>
        <?php echo GridView::widget([
            'dataProvider'=>new \yii\data\ArrayDataProvider(['allModels'=>$data['expenseContents'], 'sort'=>false, 'pagination' => false]),
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
        <strong><?php echo Yii::t('app', 'Uwagi'); ?>:</strong>
        <?php
//        foreach ($model['events'] as $event)
//        {
//            echo $event['name'].' ['.$event['code'].']<br />';
//        }
//        ?>
        <?php echo $model['description']; ?>

    </div>
</div>
<div class="row">
    <div class="col-lg-12 text-right">
        <strong><?php echo Yii::t('app', 'Wystawił'); ?>:</strong>
        <?php
        if ($model['creator'] != null)
        {
            echo $data['labels']['creator'];
        }
        ?>

    </div>
</div>