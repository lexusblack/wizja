<?php
use yii\bootstrap\Html;
use yii\widgets\DetailView;
use common\models\InvoiceContent;
//use common\components\grid\GridView;
use yii\grid\GridView;
use common\helpers\Url;
/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Faktury'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


$formatter = Yii::$app->formatter;
$formatter->currencyCode = $model->currency;
$model->storeData();

?>
<div class="invoice-view">

    <div class="row">
        <div class="col-lg-6">
            <?= Html::a('<i class="fa fa-pencil"></i> ' . Yii::t('app', 'Edycja'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="fa fa-trash"></i> ' . Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Na pewno usunąć?'),
                    'method' => 'post',
                ],
            ]) ?>
            <?php
            if ($model->event!==null)
            {
                echo Html::a(Yii::t('app', 'Wydarzenie'), ['/event/view', 'id'=>$model->event_id, '#'=>'tab-finances'], ['class'=>'btn btn-warning']);
            }
            ?>
        </div>
        <div class="col-lg-6 text-right">
            <?php echo Html::a(Html::icon('save-file').' '.Yii::t('app', 'Pobierz'), ['pdf', 'id'=>$model->id, 'action'=>'download'], ['class'=>'btn btn-primary']); ?>
            <?php echo Html::a(Html::icon('print').' '.Yii::t('app', 'Drukuj'), ['pdf', 'id'=>$model->id, 'action'=>'print'], ['class'=>'btn btn-primary', 'target'=>'_blank']); ?>
            <?php echo Html::a(Html::icon('send').' '.Yii::t('app', 'Wyślij'), ['send', 'id'=>$model->id], ['class'=>'btn btn-primary']); ?>
        </div>
    </div>




    <h3><?php echo $model->getTypeLabel(); ?></h3>
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
                    <th colspan="4" class="text-center"><?php echo Yii::t('app', 'Faktura nr'); ?> <?php echo $model->fullnumber; ?></th>
                </tr>
                <tr>
                    <th><?php echo $model->getAttributeLabel('date'); ?></th>
                    <td><?php echo $model->date; ?></td>
                    <th><?php echo $model->getAttributeLabel('disposaldate'); ?></th>
                    <td><?php echo $model->disposaldate; ?></td>
                </tr>
                <tr>
                    <th><?php echo $model->getAttributeLabel('paymentdate'); ?></th>
                    <td><?php echo $model->paymentdate; ?></td>
                    <th><?php echo $model->getAttributeLabel('paymentmethod'); ?></th>
                    <td><?php echo $model->getPaymentmethodLabel(); ?></td>
                </tr>


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
           <?php echo $data['seller']['nip']; ?><br />
           <?php echo $data['seller']['bankName']; ?><br />
           <?php echo $data['seller']['bankNumber']; ?></p>
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
           <?php echo $data['buyer']['nip']; ?><br />
           <?php echo $data['buyer']['bankNumber']; ?></p>
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
                    <th><?php echo $model->getAttributeLabel('payment_date'); ?></th>
                    <td><?php echo $model->payment_date; ?></td>
                </tr>
                <tr>
                    <th><?php echo $model->getAttributeLabel('alreadypaid'); ?></th>
                    <td><?php echo $formatter->asCurrency($model->alreadypaid); ?></td>
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
                    <th><?php echo $model->getAttributeLabel('total'); ?></th>
                    <td><?php echo $formatter->asCurrency($model->total); ?></td>
                </tr>
                <tr>
                    <th><?php echo $model->getAttributeLabel('remaining'); ?></th>
                    <td><?php echo $formatter->asCurrency($model->remaining); ?></td>
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
                        <h4><?= Yii::t('app', 'Pozycje na dokumencie') ?></h4>
                    </div>
                </div>
            </div>


            <div class="panel_mid_blocks">
                <div class="panel_block">
            <?php echo GridView::widget([
                    'dataProvider'=>new \yii\data\ActiveDataProvider(['query'=>$model->getInvoiceContents(), 'sort'=>false]),
                'tableOptions' => [
                    'class' => 'kv-grid-table table table-condensed kv-table-wrap'
                ],
                'columns' => [
                    'name',
                    'classification',
                    'unit',
                    'count:decimal',
                    'price:currency',
                    [
                        'attribute'=>'discount_percent',
                        'value'=>function($model) use ($formatter)
                        {
                            return $formatter->asPercent($model->discount_percent/100);
                        }
                    ],
                    [
                        'attribute'=>'vat',
                        'value'=>function($model) use ($formatter)
                        {
                            return $formatter->asPercent($model->vat/100);
                        }
                    ],
                    'netto:currency',
                    'tax:currency',
                    'brutto:currency',
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
                    <td><?php echo $formatter->asCurrency($model->netto); ?></td>
                    <td><?php echo $formatter->asCurrency($model->tax); ?></td>
                    <td><?php echo $formatter->asCurrency($model->total); ?></td>
                </tr>

            </table>
                </div>
            </div>
        </div>
    </div>
    <?php if( empty($model->description) == false): ?>
    <div class="row">
        <div class="col-lg-12">

            <div class="panel_mid_blocks">
                <div class="panel_block">
                    <div class="title_box">
                        <h4><?= Yii::t('app', 'Opis') ?></h4>
                    </div>
            <?php echo $model->description; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel_mid_blocks">
                <div class="panel_block">
                    <div class="title_box">
                        <h4><?= Yii::t('app', 'Pliki') ?></h4>
                    </div>
                    <ol>
                        <?php foreach ($model->invoiceAttachments as $attachment): ?>
                            <li>
                                <?php echo Html::a($attachment->filename, ['invoice-attachment/download', 'id'=>$attachment->id]); ?>
                                <?php echo Html::a("<i class='fa fa-trash'></i>", ['invoice/delete-file', 'id'=>$attachment->id],  [
                'class' => 'btn btn-danger',
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
