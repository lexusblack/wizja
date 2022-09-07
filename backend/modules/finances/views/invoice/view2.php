<?php

use common\models\Invoice;
use yii\bootstrap\Html;
use yii\grid\GridView;
use common\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */
/* @var $modelObject common\models\Invoice */

$this->title = $model['fullnumber'];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Faktury'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


$formatter = Yii::$app->formatter;
$formatter->currencyCode = $model['currency'];

use yii\bootstrap\Modal;

/* @var $model \common\models\Event; */
Modal::begin([
    'id' => 'new-payment',
    'header' => Yii::t('app', 'Edytuj płatność'),
    'class' => 'modal',
    'options' => [
        'tabindex' => false,
    ],
    'clientOptions' => [
    'keyboard'=> false,
        'backdrop'=> 'static'
    ]
]);
echo "<div class='modalContent'></div>";
Modal::end();
?>
<div class="invoice-view panel panel-default">
<div class="panel-body">
    <div class="row">
        <div class="col-lg-6">
            <?= Html::a('<i class="fa fa-pencil"></i> ' . Yii::t('app', 'Edycja'), ['update', 'id' => $model['id']], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="fa fa-trash"></i> ' . Yii::t('app', 'Usuń'), ['delete', 'id' => $model['id']], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Napewno usunąć?'),
                    'method' => 'post',
                ],
            ]) ?>
            <?php
            if ($data['owner']!=null)
            {
                echo Html::a(ArrayHelper::getValue(\common\models\Invoice::getOwnerTypeList(), $model['owner_type']), ['/'.\common\models\Invoice::getControllerId($model['owner_type']).'/view', 'id'=>$model['owner_id'], '#'=>'tab-finances'], ['class'=>'btn btn-warning']);
            }
            ?>
        </div>
        <div class="col-lg-6 text-right">
            <?php echo Html::a(Html::icon('save-file').' '.Yii::t('app', 'Pobierz'), ['pdf', 'id'=>$model['id'], 'action'=>'download'], ['class'=>'btn btn-primary']); ?>
            <?php echo Html::a(Html::icon('print').' '.Yii::t('app', 'Drukuj'), ['pdf', 'id'=>$model['id'], 'action'=>'print'], ['class'=>'btn btn-primary', 'target'=>'_blank']); ?>
            <?php echo Html::a(Html::icon('send').' '.Yii::t('app', 'Wyślij'), ['send', 'id'=>$model['id']], ['class'=>'btn btn-primary']); ?>
        </div>
    </div>




    <h3><?php echo ArrayHelper::getValue(\common\models\Invoice::getTypeList(), $model['type'], UNDEFINDED_STRING); ?></h3>
    <div class="row">
        <div class="col-lg-6 text-center">
            <?php
                if ($data['seller']['logo']) {
                    $logo = Yii::getAlias('@uploads/settings/' . $data['seller']['logo']);
                    echo Html::img($logo, [ 'style'=>'width:300px']);
                }
            ?>


        </div>
        <div class="col-lg-6">

            <div class="panel_mid_blocks">
                <div class="panel_block">
            <table class="table table-striped">
                <tr>
                    <th colspan="4" class="text-center">
                        <?php echo Yii::t('app', 'Faktura nr') . " " . $model['fullnumber'];
                        if ($parentInvoice) {
                            echo Yii::t("app", " do faktury").": " . Html::a($parentInvoice->fullnumber, ['invoice/view', 'id' => $parentInvoice->id], ['target' => '_blank']);
                        } ?>
                    </th>
                </tr>
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

                <?php if ($modelObject->correction_explanation) { ?>
                    <tr>
                        <th style="white-space: nowrap;"><?= $tmpModel->getAttributeLabel('correction_explanation'); ?></th>
                        <td colspan="3"><?= $modelObject->correction_explanation ?></td>
                    </tr>
                <?php } ?>


            </table>
                </div>
            </div>
        </div>
    </div>
   <div class="row">
       <div class="col-lg-6">

           <div class="panel_mid_blocks">
               <div class="panel_block" style="margin-bottom: 0;">
                   <div class="title_box">
                       <h4><?php echo Yii::t('app', 'Sprzedawca'); ?></h4>
                   </div>
               </div>
           </div>
           <div class="panel_mid_blocks">
               <div class="panel_block">
           <p><strong><?php echo $data['seller']['name']; ?></strong><br />
           <?php echo $data['seller']['address']; ?><br />
           <?php echo $data['seller']['city']; ?><br />
               <strong><?php echo Yii::t('app', 'NIP'); ?>:</strong> <?php echo $data['seller']['nip']; ?><br />
               <strong><?php echo Yii::t('app', 'Bank'); ?>:</strong> <?php echo $data['seller']['bankName']; ?><br />
               <strong><?php echo Yii::t('app', 'Nr konta'); ?>:</strong> <?php echo $data['seller']['bankNumber']; ?>
               </div>
           </div>

       </div>
       <div class="col-lg-6">

           <div class="panel_mid_blocks">
               <div class="panel_block" style="margin-bottom: 0;">
                   <div class="title_box">
                       <h4><?php echo Yii::t('app', 'Nabywca'); ?></h4>
                   </div>
               </div>
           </div>
           <div class="panel_mid_blocks">
               <div class="panel_block">
           <p><strong><?php echo $data['buyer']['name']; ?></strong><br />
           <?php echo $data['buyer']['address']; ?><br />
           <?php echo $data['buyer']['city']; ?><br />
               <strong><?php echo Yii::t('app', 'NIP'); ?>:</strong> <?php echo $data['buyer']['nip']; ?><br />
               <strong><?php echo Yii::t('app', 'Nr konta'); ?>:</strong> <?php echo $data['buyer']['bankNumber']; ?>
               </div>
           </div>
       </div>
   </div>
    <div class="row">
        <div class="col-lg-6">

            <div class="panel_mid_blocks">
                <div class="panel_block">
            <table class="table">

                <tr>
                    <th><?php echo $tmpModel->getAttributeLabel('payment_date'); ?></th>
                    <td><?php echo $model['payment_date']; ?></td>
                </tr>
                <tr>
                    <th><?php echo $tmpModel->getAttributeLabel('alreadypaid'); ?></th>
                    <td><?php echo $formatter->asCurrency($model['alreadypaid'], $model['currency']); ?></td>
                </tr>
                <?php foreach ($data['paymentHistory'] as $payment): ?>
                    <tr>
                        <td>[<?php echo $formatter->asDate($payment['date']); ?>] <?php echo $payment['label']; if (isset($payment['payment_method'])) echo " [".$payment['payment_method']."]";?></td>
                        <td><?php echo $formatter->asCurrency($payment['amount'], $model['currency']); ?>
                        <div class="pull-right"><?php echo Html::a(Html::icon('pencil'), ['history-edit', 'id'=>$payment['id']], ['class'=>'edit-history'] ); ?>
                            <div class="pull-right"><?php echo Html::a(Html::icon('remove'), ['history-remove', 'id'=>$payment['id']], ['class'=>'remove-history'] ); ?></div>
                        </td>
                    </tr>
                <?php endforeach; ?>

            </table>
                </div>
            </div>

        </div>
        <div class="col-lg-6">

            <div class="panel_mid_blocks">
                <div class="panel_block">
            <table class="table">
                <tr>
                    <th><?php echo $tmpModel->getAttributeLabel('total'); ?></th>
                    <td><?php echo $formatter->asCurrency($model['total'], $model['currency']); ?></td>
                </tr>
                <tr>
                    <th><?php echo $tmpModel->getAttributeLabel('remaining'); ?></th>
                    <td><?php echo $formatter->asCurrency($model['remaining'], $model['currency']); ?></td>
                </tr>

            </table>
                </div>
            </div>
        </div>
    </div>
    <?php if ($parentInvoice){ ?>

    <div class="row">
        <div class="col-lg-12">

            <div class="panel_mid_blocks">
                <div class="panel_block" style="margin-bottom: 0;">
                    <div class="title_box">
                        <h4><?= Yii::t('app', 'Stan poprzedni') ?></h4>
                    </div>
                </div>
            </div>


            <div class="panel_mid_blocks">
                <div class="panel_block">
            <?php echo GridView::widget([
                    'dataProvider'=>new \yii\data\ArrayDataProvider(['allModels'=>$data2['invoiceContents'], 'sort'=>false, 'pagination' => false]),
                'layout' => "{items}\n{pager}",
                'tableOptions' => [
                    'class' => 'kv-grid-table table table-condensed kv-table-wrap'
                ],
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

        </div>
    </div>
<?php } ?>

    <div class="row">
        <div class="col-lg-12">

            <div class="panel_mid_blocks">
                <div class="panel_block" style="margin-bottom: 0;">
                    <div class="title_box">
                    <?php if ($parentInvoice){ ?>
                        <h4><?= Yii::t('app', 'Stan aktualny') ?></h4>
                    <?php }else { ?>
                        <h4><?= Yii::t('app', 'Pozycje na dokumencie') ?></h4>
                    <?php } ?>
                    </div>
                </div>
            </div>


            <div class="panel_mid_blocks">
                <div class="panel_block">
            <?php echo GridView::widget([
                    'dataProvider'=>new \yii\data\ArrayDataProvider(['allModels'=>$data['invoiceContents'], 'sort'=>false, 'pagination' => false]),
                'layout' => "{items}\n{pager}",
                'tableOptions' => [
                    'class' => 'kv-grid-table table table-condensed kv-table-wrap'
                ],
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

        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 col-lg-offset-6">

            <div class="panel_mid_blocks">
                <div class="panel_block">
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
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">

            <div class="panel_mid_blocks">
                <div class="panel_block">
                    <div class="title_box">
                    </div>
                    <strong><?php echo Yii::t('app', 'Uwagi'); ?>: </strong><br/>
                    <?php echo nl2br($model['description']); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">

            <div class="panel_mid_blocks">
                <div class="panel_block text-right">
                    <div class="title_box">
                    </div>
                    <strong><?php echo Yii::t('app', 'Wystawił'); ?>: </strong>
                    <?php
                        if ($model['creator'] != null)
                        {
                            echo $data['labels']['creator'];
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel_mid_blocks">
                <div class="panel_block">
                    <div class="title_box">
                        <h4><?= Yii::t('app', 'Pliki') ?></h4>
                    </div>
                    <ol>
                        <?php foreach ($data['invoiceAttachments'] as $attachment): ?>
                            <li>
                                <?php echo Html::a($attachment['filename'], ['invoice-attachment/download', 'id'=>$attachment['id']]); ?>
                                <?php echo Html::a("<i class='fa fa-trash'></i>", ['invoice/delete-file', 'id'=>$attachment['id']],  [
                'class' => 'btn btn-danger btn-xs',
                'data' => [
                    'confirm' => Yii::t('app', 'Na pewno usunąć?'),
                    'method' => 'post',
                ],
            ]); ?>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel_mid_blocks">
                <div class="panel_block">
                    <div class="title_box">
                        <h4><?= Yii::t('app', 'Historia wysyłki') ?></h4>
                    </div>
                    <table class="table">
            <tr><th><?=Yii::t('app', 'Data')?></th><th><?=Yii::t('app', 'Odbiorcy')?></th><th><?=Yii::t('app', 'Nadawca')?></th><th><?=Yii::t('app', 'Plik')?></th></tr>
            <?php foreach($modelObject->invoiceSends as $os){ ?>
            <tr>
                <td><?=$os->datetime?></td>
                <td><?=$os->recipient?></td>
                <td><?php if ($os->user_id) echo $os->user->displayLabel; ?></td>
                <td><?php if ($os->filename) echo Html::a($os->filename, Yii::getAlias('@uploads/invoice/'.$os->filename)); ?></td>
            </tr>
            <?php } ?>
        </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<?php
$this->registerJs('
    $(".remove-history").on("click", function(e){
        e.preventDefault();
        $el = $(this);
        $.get($el.prop("href"), {}, function(){
            location.reload();
        });
        return false;
    });

        $(".edit-history").on("click", function(e){
        e.preventDefault();
        $("#new-payment").modal("show").find(".modalContent").load($(this).attr("href"));
        return false;
    });
');