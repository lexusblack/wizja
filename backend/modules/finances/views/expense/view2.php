<?php
use yii\bootstrap\Html;
use yii\grid\GridView;
use common\helpers\ArrayHelper;
use common\models\EventExpense;

//use common\components\grid\GridView;
/* @var $this yii\web\View */
/* @var $model common\models\Expense */

$this->title = $model['number'];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wydatki'), 'url' => ['index']];
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
        <br />
        <?php
            foreach ($model['events'] as $event)
            {
                echo Html::a(Yii::t('app', 'Wydarzenie').': '.$event['name'], ['/event/view', 'id'=>$event['id'], '#'=>'tab-finances'], ['class'=>'btn btn-warning']);

            }

        ?>
        </div>

        <div class="col-lg-6 text-right">
            <?php echo Html::a(Html::icon('save-file').' '.Yii::t('app', 'Pobierz'), ['pdf', 'id'=>$model['id'], 'action'=>'download'], ['class'=>'btn btn-primary']); ?>
            <?php echo Html::a(Html::icon('print').' '.Yii::t('app', 'Drukuj'), ['pdf', 'id'=>$model['id'], 'action'=>'print'], ['class'=>'btn btn-primary', 'target'=>'_blank']); ?>
            <?php echo Html::a(Html::icon('send').' '.Yii::t('app', 'Wyślij'), ['send', 'id'=>$model['id']], ['class'=>'btn btn-primary']); ?>
        </div>
    </div>

    <h3><?php echo ArrayHelper::getValue(\common\models\Expense::getTypeList(), $model['type'], UNDEFINDED_STRING); ?></h3>

   <div class="row">
       <div class="col-lg-6">

           <div class="panel_mid_blocks">
               <div class="panel_block" style="margin-bottom: 0;">
                   <div class="title_box">
                       <h4><?php echo Yii::t('app', 'Sprzedawca'); ?></h4>
                   </div>
               </div>
           </div>
           <p><strong><?php echo $data['seller']['name']; ?></strong><br />
               <?php echo $data['seller']['address']; ?><br />
               <?php echo $data['seller']['city']; ?><br />
               <strong><?php echo Yii::t('app', 'NIP'); ?>:</strong> <?php echo $data['seller']['nip']; ?><br />
               <strong><?php echo Yii::t('app', 'Bank'); ?>:</strong> <?php echo $data['seller']['bankName']; ?><br />
               <strong><?php echo Yii::t('app', 'Nr konta'); ?>:</strong> <?php echo $data['seller']['bankNumber']; ?>
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
                       <strong><?php echo Yii::t('app', 'Bank'); ?>:</strong> <?php echo $data['buyer']['bankName']; ?><br />
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
                    <th><?php echo Yii::t('app', 'Numer'); ?></th>
                    <td><?php echo $model['number']; ?></td>
                </tr>
                <tr>
                    <th><?php echo Yii::t('app', 'Metoda płatności'); ?></th>
                    <td><?php echo $data['labels']['paymentMethod']; ?></td>
                </tr>
                <tr>
                    <th><?php echo Yii::t('app', 'Termin płatności'); ?></th>
                    <td><?php echo $model['paymentdate']; ?></td>
                </tr>
                <tr>
                    <th><?php echo Yii::t('app', 'Zapłacono'); ?></th>
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
                <tr>
                    <th><?php echo Yii::t('app', 'Razem do zapłaty'); ?></th>
                    <td><?php echo $formatter->asCurrency($model['total'], $model['currency']); ?></td>
                </tr>

            </table>

                </div>
            </div>
        </div>
        <div class="col-lg-6">

            <div class="panel_mid_blocks">
                <div class="panel_block">
            <table class="table">
                <tr>
                    <th><?php echo Yii::t('app', 'Data wystawienia'); ?></th>
                    <td><?php echo $model['date']; ?></td>
                </tr>
                <tr>
                    <th><?php echo Yii::t('app', 'Data sprzedaży'); ?></th>
                    <td><?php echo $model['disposaldate']; ?></td>
                </tr>
                <tr>
                    <th><?php echo Yii::t('app', 'Do zapłacenia'); ?></th>
                    <td><?php echo $formatter->asCurrency($model['remaining'], $model['currency']); ?></td>
                </tr>

            </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">

            <div class="panel_mid_blocks">
                <div class="panel_block" style="margin-bottom: 0;">
                    <div class="title_box">
                        <h4><?= Yii::t('app', 'Stawki na dokumencie') ?></h4>
                    </div>
                </div>
            </div>
            <div class="panel_mid_blocks">
                <div class="panel_block">
            <?php echo GridView::widget([
                    'dataProvider'=>new \yii\data\ArrayDataProvider(['allModels'=>$data['expenseContentRates']]),
                    'layout' => "{items}\n{pager}",
                'tableOptions' => [
                    'class' => 'kv-grid-table table table-condensed kv-table-wrap'
                ],
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
                        'format' => ['currency', $model['currency']],
                    ],
	                [
		                'attribute' => 'tax',
		                'format' => ['currency', $model['currency']],
	                ],
	                [
		                'attribute' => 'brutto',
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
        <div class="col-lg-12">

            <div class="panel_mid_blocks">
                <div class="panel_block" style="margin-bottom: 0;">
                    <div class="title_box">
                        <h4><?= Yii::t('app', 'Pozycje na dokumencie') ?></h4>
                    </div>
                </div>
            </div>
            <div class="panel_mid_blocks">
                <div class="panel_block">
            <?php echo GridView::widget([
                'dataProvider'=>new \yii\data\ArrayDataProvider(['allModels'=>$data['expenseContents']]),
                'layout' => "{items}\n{pager}",
                'tableOptions' => [
                    'class' => 'kv-grid-table table table-condensed kv-table-wrap'
                ],
                'columns' => [
	                [
		                'attribute' => 'name',
		                'label' => $tmpContent->getAttributeLabel('name'),
                        'value'=>function($model)
                        {
                            if ($model['event_expense_id'])
                            {
                                $ee = EventExpense::findOne($model['event_expense_id']);
                                return $model['name']." [".$ee->event->code."]";
                            }else{
                                return $model['name'];
                            }
                        }
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
                    <strong><?php echo Yii::t('app', 'Uwagi'); ?>:</strong>

                    <?php echo $model['description']; ?>
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
                    <strong><?php echo Yii::t('app', 'Wystawił'); ?>:</strong>
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
                        <?php foreach ($data['expenseAttachments'] as $attachment): ?>
                            <li>
                                <?php echo Html::a($attachment['filename'], ['expense-attachment/download', 'id'=>$attachment['id']]); ?>
                                <?php echo Html::a("<i class='fa fa-trash'></i>", ['expense/delete-file', 'id'=>$attachment['id']],  [
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