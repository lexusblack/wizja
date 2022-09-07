<?php
use yii\bootstrap\Html;
use yii\widgets\DetailView;
use common\models\InvoiceContent;
//use common\components\grid\GridView;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $model common\models\Expense */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wydatki'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


$formatter = Yii::$app->formatter;
$formatter->currencyCode = $model->currency;

?>
<div class="invoice-view">

    <div class="row">
        <div class="col-lg-6">


        <?= Html::a('<i class="fa fa-pencil"></i> ' . Yii::t('app', 'Edycja'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fa fa-trash"></i> ' . Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Napewno usunąć?'),
                'method' => 'post',
            ],
        ]) ?>
        </div>

        <div class="col-lg-6 text-right">
            <?php echo Html::a(Html::icon('save-file').' '.Yii::t('app', 'Pobierz'), ['pdf', 'id'=>$model->id, 'action'=>'download'], ['class'=>'btn btn-primary']); ?>
            <?php echo Html::a(Html::icon('print').' '.Yii::t('app', 'Drukuj'), ['pdf', 'id'=>$model->id, 'action'=>'print'], ['class'=>'btn btn-primary', 'target'=>'_blank']); ?>
            <?php echo Html::a(Html::icon('send').' '.Yii::t('app', 'Wyślij'), ['send', 'id'=>$model->id], ['class'=>'btn btn-primary']); ?>
        </div>
    </div>

    <h3><?php echo $model->getExpenseTypeLabel(); ?></h3>

   <div class="row">
       <div class="col-lg-6">

           <div class="panel_mid_blocks">
               <div class="panel_block" style="margin-bottom: 0;">
                   <div class="title_box">
                       <h4><?php echo Yii::t('app', 'Sprzedawca'); ?></h4>
                   </div>
               </div>
           </div>
           <?php
           if($model->customer!== null)
           {
               echo $model->customer->getDisplayLabel();
           }

           ?>
       </div>
       <div class="col-lg-6">

           <div class="panel_mid_blocks">
               <div class="panel_block" style="margin-bottom: 0;">
                   <div class="title_box">
                       <h4><?php echo Yii::t('app', 'Nabywca'); ?></h4>
                   </div>
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
                    <td><?php echo $model->number; ?></td>
                </tr>
                <tr>
                    <th><?php echo Yii::t('app', 'Metoda płatności'); ?></th>
                    <td><?php echo $model->getPaymentmethodLabel(); ?></td>
                </tr>
                <tr>
                    <th><?php echo Yii::t('app', 'Termin płatności'); ?></th>
                    <td><?php echo $model->paymentdate; ?></td>
                </tr>
                <tr>
                    <th><?php echo Yii::t('app', 'Zapłacono'); ?></th>
                    <td><?php echo $formatter->asCurrency($model->alreadypaid); ?></td>
                </tr><tr>
                    <th><?php echo Yii::t('app', 'Razem'); ?></th>
                    <td><?php echo $formatter->asCurrency($model->total); ?></td>
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
                    <td><?php echo $model->date; ?></td>
                </tr>
                <tr>
                    <th><?php echo Yii::t('app', 'Data sprzedaży'); ?></th>
                    <td><?php echo $model->disposaldate; ?></td>
                </tr>
                <tr>
                    <th><?php echo Yii::t('app', 'Do zapłacenia'); ?></th>
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
                        <h4><?= Yii::t('app', 'Stawki na dokumencie') ?></h4>
                    </div>
                </div>
            </div>
            <div class="panel_mid_blocks">
                <div class="panel_block">
            <?php echo GridView::widget([
                    'dataProvider'=>new \yii\data\ActiveDataProvider(['query'=>$model->getExpenseContentRates()]),
                'tableOptions' => [
                    'class' => 'kv-grid-table table table-condensed kv-table-wrap'
                ],
                'columns' => [
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
                'dataProvider'=>new \yii\data\ActiveDataProvider(['query'=>$model->getExpenseContents()]),
                'tableOptions' => [
                    'class' => 'kv-grid-table table table-condensed kv-table-wrap'
                ],
                'columns' => [
                                        [
                        'attribute'=>'name',
                        'value'=>function($model)
                        {
                            if ($model->event_expense_id)
                            {
                                return $model->name." [".$model->evenExpense->event->code."]";
                            }else{
                                return $model->name;
                            }
                        }
                    ],
                    'classification',
                    'unit',
                    'count:decimal',
                    'price:currency',
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
    <div class="row">
        <div class="col-lg-12">
            <div class="panel_mid_blocks">
                <div class="panel_block">
                    <div class="title_box">
                        <h4><?= Yii::t('app', 'Pliki') ?></h4>
                    </div>
                    <ol>
                        <?php foreach ($model->expenseAttachments as $attachment): ?>
                            <li>
                                <?php echo Html::a($attachment->filename, ['expense-attachment/download', 'id'=>$attachment->id]); ?>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
