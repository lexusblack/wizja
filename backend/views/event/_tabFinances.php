<?php
use yii\helpers\Html;
use common\components\grid\GridView;
use kartik\form\ActiveForm;
use yii\bootstrap\Modal;
use kartik\grid\CheckboxColumn;
use kartik\grid\SerialColumn;
/* @var $model \common\models\Event; */
/* @var $this \yii\web\View */
$formatter = Yii::$app->formatter;
$user = Yii::$app->user;


Modal::begin([
    'id' => 'edit-provision',
    'header' => Yii::t('app', 'Edycja prowizji PM'),
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
<div class="panel-body">

<div class="row">
<div class="col-md-12">
    <?php
    if (($user->can('eventsEventEditEyeFinanceAddCost'))&&(((!$model->getBlocks('cost'))||(Yii::$app->user->can('eventEventBlockCost'))))) {
        echo Html::a(Yii::t('app', 'Dodaj koszt'), ['event-expense/create', 'id' => $model->id], ['class' => 'btn btn-success']);
    }
    if ($user->can('eventsEventEditEyeFinanceAddInvoice')) {
        echo Html::a(Yii::t('app', 'Dodaj załącznik'), ['event-invoice/create', 'id' => $model->id], ['class' => 'btn btn-success']);
    }
    if ($user->can('menuInvoicesInvoiceCreate')) {
        echo Html::a(Yii::t('app', 'Wystaw fakturę'), ['/finances/invoice/create', 'id' => $model->id, 'owner' => \common\models\Invoice::OWNER_TYPE_EVENT], ['class' => 'btn btn-default', 'target' => '_blank']);
    }
    if ($user->can('menuInvoicesExpenseCreate')) {
        echo Html::a(Yii::t('app', 'Dodaj fakturę kosztową'), ['/finances/expense/create', 'id'=>$model->id], ['class'=>'btn btn-default', 'target'=>'_blank']);
    } ?>
</div>
</div>

<!--    Wartość projektu + Razem -->
<?php if ($user->can('eventsEventEditEyeFinanceProjectCosts')): 
    if (Yii::$app->session->get('company')!=1){ ?>
<div class="row">
    <div class="col-md-12">
             <div class="ibox float-e-margins">
                <div class="ibox-title  blue-bg">
                    <h5><?php echo Yii::t('app', 'Wartość projektu'); ?></h5>
                </div>
                <div class="ibox-content">
        <?php
            $offers = $model->getAcceptedAgencyOffers();

            // jeżeli nie ma zaakceptowanej oferty
            if (isset($offers['error']) && $offers['error']) { ?>
                <div class="alert alert-danger">
                    <h4><?php echo Yii::t('app', 'Brak zaakceptowanej oferty'); ?></h4>
                </div><?php
            }
            else { ?>
                <?php 
                 $total_netto = 0;
                $total_profit = 0;               
                foreach ($offers as $offer): ?>
                            <div class="title_box">
                                <h4><?php echo Yii::t('app', 'Oferta'); ?>: <?php echo $offer->name; ?></h4>
                            </div>
                            <table class="table table-condensed">
                                <tr>
                                    <th><?php echo Yii::t('app', 'Klient'); ?></th>
                                    <th><?php echo Yii::t('app', 'Project Manager'); ?></th>
                                    <th><?php echo Yii::t('app', 'Wartość netto'); ?></th>
                                    <th><?php echo Yii::t('app', 'Zysk netto'); ?></th>
                                    <th><?php echo Yii::t('app', 'Wartość brutto'); ?></th>
                                </tr>

                                <tr>
                                    <td><?php echo $offer->customer->name; ?></td>
                                    <td><?php echo $offer->manager->displayLabel; ?></td>
                                    <td><?php echo $formatter->asCurrency($offer->getNettoValue()); ?></td>
                                    <td><?php echo $formatter->asCurrency($offer->getProfitValue()); ?></td>
                                    <td><?php echo $formatter->asCurrency($offer->getNettoValue()*1.23); ?></td>
                                </tr>
                            </table>
                <?php
                $total_netto += $offer->getNettoValue();
                $total_profit += $offer->getProfitValue();
                 endforeach;
            }?>
            </div>
            </div>
    </div>
    <div class="col-md-12">
             <div class="ibox float-e-margins">
                <div class="ibox-title  blue-bg">
                    <h5><?php echo Yii::t('app', 'Razem'); ?></h5>
                </div>
                <div class="ibox-content">
        <?php
        if (isset($offers['error']) && $offers['error']) { ?>
            <div class="alert alert-danger">
                <h4><?php echo Yii::t('app', 'Brak zaakceptowanej oferty'); ?></h4>
            </div><?php
        }
        else {
            ?>
                            <table class="table table-condensed">
                                <tr>
                                    <th><?php echo Yii::t('app', 'Wartość netto'); ?></th>
                                    <th><?php echo Yii::t('app', 'Zysk netto'); ?></th>
                                    <th><?php echo Yii::t('app', 'Wartość brutto'); ?></th>
                                </tr>

                                <tr>
                                    <td><?php echo $formatter->asCurrency($total_netto); ?></td>
                                    <td><?php echo $formatter->asCurrency($total_profit); ?></td>
                                    <td><?php echo $formatter->asCurrency($total_netto*1.23); ?></td>
                                </tr>
                            </table>
        <?php
        }
        ?>
                    </div>
            </div>

    </div>
</div>
<?php }else{ ?>
<div class="row">
    <div class="col-md-12">
             <div class="ibox float-e-margins">
                <div class="ibox-title  blue-bg">
                    <h5><?php echo Yii::t('app', 'Wartość projektu'); ?></h5>
                </div>
                <div class="ibox-content" style="overflow-x: scroll;">
        <?php
            $offers = $model->getFinancesOffers();

            // jeżeli nie ma zaakceptowanej oferty
            if (isset($offers['error']) && $offers['error']) { ?>
                <div class="alert alert-danger">
                    <h4><?php echo Yii::t('app', 'Brak zaakceptowanej oferty'); ?></h4>
                </div><?php
            }
            else { 
            $gcat = \common\models\GearCategory::getMainList(true);
                ?>
                        <table class="table table-condensed">
                                <tr>
                                    <th><?php echo Yii::t('app', 'Oferta'); ?></th>
                                    <?php foreach ($gcat as $key => $cat):
                                    ?>
                                        <th><?php echo $cat->name; ?></th>
                                    <?php  endforeach; ?>
                                    <th><?php echo Yii::t('app', 'Obsługa'); ?></th>
                                    <th><?php echo Yii::t('app', 'Transport'); ?></th>
                                    <th><?php echo Yii::t('app', 'Inne'); ?></th>
                                    <th><?php echo Yii::t('app', 'Suma'); ?></th>
                                    <th><?php echo Yii::t('app', 'Brutto'); ?></th>
                                </tr>

                                        <?php foreach ($offers as $offer) { ?>
                                        <?php $summary = $offer->getOfferValues(); ?>


                                <tr>
                                    <td><strong><?php echo $offer->name; ?></strong></td>
                                    <?php foreach ($gcat as $key => $cat){
                                          ?>
                                        <td><?php if (isset($summary[$cat->name])) {echo $formatter->asCurrency($summary[$cat->name], $offer->priceGroup->currency); }else{ echo  $formatter->asCurrency(0, $offer->priceGroup->currency); }?></td>
                                    <?php }  ?>
                                     <td><?=$formatter->asCurrency($summary[Yii::t('app', 'Obsługa')], $offer->priceGroup->currency)?></td>
                                      <td><?=$formatter->asCurrency($summary[Yii::t('app', 'Transport')], $offer->priceGroup->currency)?></td>
                                       <td><?=$formatter->asCurrency($summary[Yii::t('app', 'Inne')], $offer->priceGroup->currency)?></td>
                                    <td><?=$formatter->asCurrency($summary[Yii::t('app', 'Suma')], $offer->priceGroup->currency)?></td>
                                    <td><?=$formatter->asCurrency($summary[Yii::t('app', 'Suma')]+$offer->getVatValue(), $offer->priceGroup->currency)?></td>
                                </tr>
                            
                <?php } ?>
                <?php $summary = $model->getOffersSummary(); ?>
                <tr style="background-color:#eee; font-weight:bold;"><td><?=Yii::t('app', 'Razem')?></td>
                    <?php foreach ($gcat as $key => $cat){
                                          ?>
                                        <td><?php if (isset($summary[$cat->name])) {echo $formatter->asCurrency($summary[$cat->name]); }else{ echo  $formatter->asCurrency(0); }?></td>
                                    <?php }  ?>
                                    <td><?=$formatter->asCurrency($summary[Yii::t('app', 'Obsługa')])?></td>
                                      <td><?=$formatter->asCurrency($summary[Yii::t('app', 'Transport')])?></td>
                                       <td><?=$formatter->asCurrency($summary[Yii::t('app', 'Inne')])?></td>
                                <td><?=$formatter->asCurrency($summary[Yii::t('app', 'Suma')])?></td>
                                    <td><?=$formatter->asCurrency($summary[Yii::t('app', 'Brutto')])?></td>

                </tr>
                </table>
                <?php } ?>
            
            </div>
            </div>
    </div>

</div>
<?php    } ?>
<?php endif; ?>


<!--    Koszty dodatkowe -->
<?php  if ($user->can('eventsEventEditEyeFinanceExtraCosts')): ?>
<div class="row">
    <div class="col-md-12">
                <div class="ibox float-e-margins">
                <div class="ibox-title  yellow-bg">
                <h5><?php echo Yii::t('app', 'Koszty dodatkowe'); ?></h5>
                        <?php if (($user->can('eventsEventEditEyeFinanceAddCost'))&&(((!$model->getBlocks('cost'))||(Yii::$app->user->can('eventEventBlockCost'))))) { ?>
                            <div class="ibox-tools white">
                            <?php echo Html::a(Yii::t('app', 'Ukryj zerowe'), '#', ['class' => 'btn btn-xs hide-zero']); ?>
                            <?php echo Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj'), ['event-expense/create', 'id' => $model->id], ['class' => 'btn btn-xs']); ?>
                        </div>

        <?php } ?>
                </div>
                <div class="ibox-content">
        <?php

    echo GridView::widget([
        'dataProvider' => $model->getEventExpensesDataProvider(),
         'id'=>'eventexpense-grid',
         'rowOptions' => function ($model) {
        if ($model->amount == 0) {
            return ['class' => 'zero-cost'];
        }
        },
        'showPageSummary' => true,
        'columns' => [
            ['class' => CheckboxColumn::className()],
            ['class' =>SerialColumn::className()],
            [
                'attribute' => 'name',
                'format'=>'raw',
                'value' => function($model){
                    if (isset($model->task_id))
                        if ($model->task->getEventProdukcja())
                        {
                            return Html::a($model->name, ['/event/view', 'id' => $model->task->getEventProdukcja()->id]);
                        }else{
                            return Html::a($model->name, ['/event/view', 'id' => $model->task->event_id]);
                        }
                        
                    else
                        return $model->name;    
                }
            ],
            'section',
            [
                'attribute' => 'customer_id',
                'filterType' => GridView::FILTER_SELECT2,
                'filter'=>\common\models\Customer::getList(),
                'filterWidgetOptions' => [
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...'),
                    ],
                    'pluginOptions' => [
                        'allowClear'=>true,
                    ],
                ],
                'label'=>Yii::t('app', 'Firma'),
                'format'=>'html',
                'value'=>function($model){
                    if (isset($model->customer))
                        return Html::a($model->customer->name, ['/customer/view', 'id' => $model->customer_id]);
                    else
                        return "-";
                }
            ],
            [
                'attribute' => 'amount',
                'format' => 'currency',
                'contentOptions'=>[
                    'class'=>'sum-cell',
                ],
            ],
            [
                'label'=>Yii::t('app', 'Zapłacono'),
                'value'=>function($model){
                    if ($model->expense_id)
                    {
                        if ($model->expense->paid)
                        {
                            return Yii::t('app', 'TAK');
                        }else{
                            if ($model->expense->alreadypaid>0)
                            {
                                return Yii::t('app', 'CZĘŚCIOWO');
                            }else{
                                return Yii::t('app', 'NIE');
                            }
                        }
                    }else{
                        return Yii::t('app', 'NIE');
                    }
                }
            ],
            'invoice_nr',
            [
                'attribute'=>'expense_id',
                'label'=>Yii::t('app', 'Faktura'),
                'format'=>'html',
                'filterType' => GridView::FILTER_SELECT2,
                'filter'=>[1=>"Bez podpiętej faktury", 2=>"Bez numeru fv", 3=>"Bez numeru i podpiętej fv"],
                'filterWidgetOptions' => [
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...'),
                    ],
                    'pluginOptions' => [
                        'allowClear'=>true,
                    ],
                ],
                'value' => function($model, $key, $index, $column)
                {
                    if ($model->expense_id)
                    {
                        $content = Html::a($model->expense->number, ['/finances/expense/view', 'id' => $model->expense_id]);
                        return $content;
                    }else{
                        $content = Html::a(Yii::t('app', 'Dodaj fakturę'), ['/finances/expense/create', 'id' => $model->event_id]);
                        return $content;
                    }

                },
            ],
            'info:ntext',
                    [
                        'class'=>\common\components\ActionColumn::className(),
                        'controllerId'=>'event-expense',
                        'template'=>'{update}{delete}',

                        'visibleButtons' => [
                                'update' => function ($expense) use ($model, $user) {
                                    return ($user->can('eventsEventEditEyeFinanceExtraCostsEdit')&&((!$expense->task_id)||($user->can('eventsEventEditEyeWorkingHoursCostsEditProd')))&&(((!$model->getBlocks('cost'))||(Yii::$app->user->can('eventEventBlockCost')))));
                                },
                                'delete' => function ($expense) use ($model, $user) {
                                    return ($user->can('eventsEventEditEyeFinanceExtraCostsDelete')&&(!$expense->task_id)&&(((!$model->getBlocks('cost'))||(Yii::$app->user->can('eventEventBlockCost')))));
                                },
                            ]
                    ]
        ],
    ]); 
        ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>



<!--    Koszty obsługi według rejestracji czasu-->
<?php if ($user->can('eventsEventEditEyeFinanceWorkingHoursCosts') || $user->can('eventsEventEditEyeFinanceCrewCosts')): ?>
<div class="row">
    <?php $data = $model->getDepartmentsWorkingTimeSummary(); ?>
    <div class="col-md-12">
        <?php if ($user->can('eventsEventEditEyeFinanceWorkingHoursCosts')) { ?>
            <div class="ibox float-e-margins">
                <div class="ibox-title  yellow-bg">
                    <h5><?php echo Yii::t('app', 'Koszty obsługi według rejestracji czasu'); ?></h5>
                </div>
                <div class="ibox-content">
                    <table class="table table-condensed table-bordered">
                        <tr>
                            <?php foreach ($data as $k => $v): ?>
                                <th><?php echo $k; ?></th>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <?php foreach ($data as $k => $v): ?>
                                <td><?php echo $formatter->asCurrency($v); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    </table>
                </div>
            </div>
            <?php
        }
        if ($user->can('eventsEventEditEyeFinanceCrewCosts')) { /*
            echo \yii\jui\Accordion::widget([
                'items' => [
                    [
                        'header' => Yii::t('app', 'Koszty obsługi'),
                        'content' => $this->render('_userSummary', ['model'=>$model]),
                    ],

                ],
                'options' => ['tag' => 'div'],
                'itemOptions' => ['tag' => 'div'],
                'headerOptions' => ['tag' => 'h4'],
                'clientOptions' => ['collapsible' => true, 'active'=>false, 'heightStyle'=>'content'],
            ]); */ ?>
            <div class="ibox float-e-margins">
                <div class="ibox-title  yellow-bg">
                    <h5><?php echo Yii::t('app', 'Koszty obsługi według pracowników'); ?></h5>
                    <div class="ibox-tools white">
                                    <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                </div>
                <div class="ibox-content">
                <?= $this->render('_userSummary', ['model'=>$model]) ?>
                </div>
            </div>
      <?php  }
        ?>
    </div>

</div>
<?php endif; ?>

    <?php $profit = $model->getEventProfits(); ?>
<!--    Zysk-->
<?php if ($user->can('eventsEventEditEyeFinanceProfit')): 
if (Yii::$app->session->get('company')==1){ ?>
<div class="row">

    <div class="col-md-12">
             <div class="ibox float-e-margins">
                <div class="ibox-title  navy-bg">
                    <h5><?php echo Yii::t('app', 'Zysk'); ?></h5>

                </div>
                <div class="ibox-content" style="overflow-x: scroll;">
        <table class="table table-condensed table-bordered">
            <tr>
                <?php foreach ($profit as $k => $v): ?>
                    <th><?php echo $k; ?></th>
                <?php endforeach; ?>
            </tr>
            <tr>
                <?php foreach ($profit as $k => $v): ?>
                    <td><?php echo $formatter->asCurrency($v); ?></td>
                <?php endforeach; ?>
            </tr>
        </table>
            </div>
        </div>
    </div>
</div>
<?php } endif; ?>



<?php  if ($user->can('eventsEventEditEyeFinanceProvision')): 
if (Yii::$app->session->get('company')==1){ 
        $values = $model->getEventValueAll();
        $profits = $model->getEventProfits();
        $provisions = $model->getProvisions();
    ?>
<div class="row">
    <div class="col-md-12">
             <div class="ibox float-e-margins">
                <div class="ibox-title  navy-bg">
                    <h5><?php echo Yii::t('app', 'Prowizje'); ?></h5>
                    <div class="ibox-tools white">
                            <?php echo Html::a('<i class="fa fa-downlaod"></i> '.Yii::t('app', 'Wczytaj domyślne prowizje'), ['event/copy-provisions', 'id' => $model->id], ['class' => 'btn btn-xs']); ?>
                        </div>
                </div>
                <div class="ibox-content" style="overflow-x: scroll;">
        <table class="table table-condensed table-bordered">
        <tr><th></th>
                <?php foreach ($profit as $k => $v): ?>
                    <th><?php 
                    echo $k; ?></th>
                <?php endforeach; ?>
                <th></th>
        </tr>
        <?php foreach ($model->getGProvisions()as $prov){ ?>
        <tr style="background-color:#eee;">
            <td><strong><?=$prov['group']->name?></strong></td>
            <?php foreach ($profit as $k => $v): $profit[$k]-=$prov['sections'][$k]?>
                    <td><?=$formatter->asCurrency($prov['sections'][$k])?></td>
                <?php endforeach; ?>
                <td><?php 
                if ($prov['group']->is_pm)
                    echo Html::a('<i class="fa fa-pencil"></i> ', ['event/edit-provision', 'id' => $model->id], ['class' => 'btn btn-xs edit-provision-button']);
                else
                    echo Html::a('<i class="fa fa-pencil"></i> ', ['event/edit-provision-group', 'id' => $prov['group']->id], ['class' => 'btn btn-xs edit-provision-button']); ?></td>
        </tr>
                <tr>
            <td><?=Yii::t('app', 'Zysk po')?></td>
            <?php foreach ($profit as $k => $v): ?>
                    <td><?=$formatter->asCurrency($profit[$k])?></td>
                <?php endforeach; ?>
            <td></td>
        </tr>
        <?php } ?>
        </table>
            </div>
        </div>
    </div>
</div>
<?php  if ($user->can('eventsEventEditEyeFinanceProfit')): 
if (Yii::$app->session->get('company')==1){ ?>
<div class="row">
    <div class="col-md-12">
             <div class="ibox float-e-margins">
                <div class="ibox-title  navy-bg">
                    <h5><?php echo Yii::t('app', 'Zysk po prowizjach'); ?></h5>

                </div>
                <div class="ibox-content" style="overflow-x: scroll;">
        <table class="table table-condensed table-bordered">
            <tr>
                <?php foreach ($profit as $k => $v): ?>
                    <th><?php echo $k; ?></th>
                <?php endforeach; ?>
            </tr>
            <tr>
                <?php foreach ($profit as $k => $v): ?>
                    <td><?php echo $formatter->asCurrency($v); ?></td>
                <?php endforeach; ?>
            </tr>
        </table>
            </div>
        </div>
    </div>
</div>
<?php } endif; ?>


<?php } endif;  ?>

<!--    Faktury przychody -->
<?php if ($user->can('eventsEventEditEyeFinanceInvoiceIn')): ?>
<div class="row">
    <div class="col-md-12">
             <div class="ibox float-e-margins">
                <div class="ibox-title  newsystem-bg">
                    <h5><?php echo Yii::t('app', 'Faktury przychody'); ?></h5>
                </div>
                <div class="ibox-content">
                <?php
                echo GridView::widget([
                    'dataProvider'=>$model->getInvoicesDataProvider(),
                    'id'=>'events-invoice-grid',
                    'showPageSummary' => true,
                    'columns' => [

                        [
                            'attribute'=>'fullnumber',
                            'value' => function($model)
                            {
                                $content = Html::a($model->fullnumber, ['/finances/invoice/view', 'id'=>$model->id], ['target'=>'_blank']);
                                return $content;
                            },
                            'format'=>'raw'
                        ],

                        [
                            'attribute'=>'customer_id',
                            'value' => function ($model)
                            {
                                $label = '';
                                if ($model->customer)
                                {
                                    $label = $model->customer->displayLabel;
                                }
                                return $label;
                            },
                            'filter'=>\common\models\Customer::getList(),
                            'filterType'=>GridView::FILTER_SELECT2,
                            'filterWidgetOptions' => [
                                'options' => [
                                    'placeholder' => Yii::t('app', 'Wybierz...')
                                ],
                                'pluginOptions' => [
                                    'allowClear'=>true,
                                ],
                            ],
                        ],
                        [
                            'class'=>\common\components\grid\LabelColumn::className(),
                            'attribute'=>'type',
                            'filter'=>\common\models\Invoice::getTypeList(),
                        ],
                        [
                            'attribute'=>'date',
                            'filterType'=>GridView::FILTER_DATE_RANGE,
                            'filterWidgetOptions' => [
                                'pluginOptions'=> [
                                    'format' => 'yyyy-mm-dd'
                                ]
                            ],
                        ],
                        'paymentdate',
                        [
                            'attribute'=>'alreadypaid',
                            'pageSummary'=>true,
                            'contentOptions'=>[
                                'class'=>'sum-cell',
                            ]
                        ],
                        [
                            'attribute'=>'total',
                            'pageSummary'=>true,
                            'contentOptions'=>[
                                'class'=>'sum-cell',
                            ]
                        ],
                    ],
                ]);
                ?>
            </div>
        </div>


    </div>
</div>
<?php endif; ?>



<!--    Faktury koszty-->
<?php if ($user->can('eventsEventEditEyeFinanceInvoiceOut')): ?>
<div class="row">
    <div class="col-md-12">
             <div class="ibox float-e-margins">
                <div class="ibox-title  newsystem-bg">
                    <h5><?php echo Yii::t('app', 'Faktury koszty'); ?></h5>
                </div>
                <div class="ibox-content">
                <?php
                echo GridView::widget([
                    'dataProvider'=>$model->getExpensesDataProvider(),
                    'layout' => '{items}',
                    'columns' => [
                        ['class' => \kartik\grid\SerialColumn::className()],

                        [
                            'attribute'=>'number',
                            'value' => function($model)
                            {
                                $content = Html::a($model->number, ['/finances/expense/view', 'id'=>$model->id], ['target'=>'_blank']);
                                return $content;
                            },
                            'format'=>'raw',
                        ],
                        [
                            'attribute'=>'customer_id',
                            'value' => function ($model)
                            {
                                $label = '';
                                if ($model->customer)
                                {
                                    $label = $model->customer->displayLabel;
                                }
                                return $label;
                            },
                            'filter'=>\common\models\Customer::getList(),
                            'filterType'=>GridView::FILTER_SELECT2,
                            'filterWidgetOptions' => [
                                'options' => [
                                    'placeholder' => Yii::t('app', 'Wybierz...')
                                ],
                                'pluginOptions' => [
                                    'allowClear'=>true,
                                ],
                            ],
                        ],
                        [
                            'attribute'=>'date',
                            'filterType'=>GridView::FILTER_DATE,
                            'filterWidgetOptions' => [
                                'pluginOptions'=> [
                                    'format' => 'yyyy-mm-dd'
                                ]
                            ],
                        ],
                        [
                            'attribute'=>'netto',
                            'pageSummary'=>true,

                        ],
                        [
                            'attribute'=>'tax',
                            'pageSummary'=>true,

                        ],
                        [
                            'attribute'=>'total',
                            'pageSummary'=>true,

                        ],
                        [
                            'attribute'=>'paid',
                            'filter'=>[
                                1=>Yii::t('app', 'Tak'),
                                0=>Yii::t('app', 'Nie')
                            ],
                            'value'=> function($model)
                            {
                                return $model->paid ? Yii::t('app', 'Tak') : Yii::t('app', 'Nie');
                            }
                        ],
                    ],
                ]);
                ?>
            </div>
        </div>


    </div>
</div>
<?php endif; ?>


<?php $form = ActiveForm::begin([
    'type'=>ActiveForm::TYPE_HORIZONTAL,
    'action' => ['/event/update', 'id'=>$model->id, '#'=>'tab-finances'],
]); ?>







<!--        Notatki-->
<?php if ($user->can('eventsEventEditEyeFinanceNotes')): ?>
<div class="row">
<div class="col-md-12">
             <div class="ibox float-e-margins">
                <div class="ibox-title  newsystem-bg">
                    <h5><?php echo Yii::t('app', 'Notatki'); ?></h5>
                </div>
                <div class="ibox-content">
        <?php echo $form->field($model, 'finance_info')->widget(\common\widgets\RedactorField::className(), ['id' => 'aaa123'])->label(false); ?>
    </div>
    </div>
    </div>
</div>
<?php endif; ?>


<?php ActiveForm::end(); ?>


<!--        Załączniki -->
<?php if ($user->can('eventsEventEditEyeFinanceAttachments')): ?>
    <div class="row">
    <div class="col-md-12">
             <div class="ibox float-e-margins">
                <div class="ibox-title  newsystem-bg">
                    <h5><?php echo Yii::t('app', 'Załączniki'); ?></h5>
                </div>
                <div class="ibox-content">
                <?php
                echo GridView::widget([
                    'dataProvider'=>$model->getEventInvoiceDataProvider(),
                    'layout' => '{items}',
                    'columns' => [
                        [
                            'class'=>\yii\grid\SerialColumn::className(),
                        ],
                        [
                            'attribute' => 'filename',
                            'value'=>function($model)
                            {
                                return Html::a($model->filename, ['event-invoice/download', 'id'=>$model->id]);
                            },
                            'format' => 'html',
                        ],
                        'update_time:datetime:'.Yii::t('app', 'Data'),
                        'typeLabel:text:'.Yii::t('app', 'Typ'),
                        [
                            'class'=>\common\components\ActionColumn::className(),
                            'controllerId'=>'event-invoice',

                            'template'=>'{delete}',
                        ],
                    ],
                ]);
                ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
</div>
<script type="text/javascript">
    function recalculateCosts()
    {
        $.ajax({
            url: '<?=\yii\helpers\Url::to(['event/recalculate-costs'])?>?id=<?=$model->id?>',
                    success: function(response){
                                  }
            });
    }
</script>
<?php

$this->registerCss('
    .hidden-row{display:none;}');
$this->registerJs('
    $(".hide-zero").click(function(e){
        e.preventDefault();
        $(".zero-cost").toggleClass("hidden-row");
    });
    $(".edit-provision-button").click(function(e){
        e.preventDefault();
        $("edit-provision").find(".modalContent").empty();
        e.preventDefault();
        $("#edit-provision").modal("show").find(".modalContent").load($(this).attr("href"));
    });
    $(".edit-provision-button").on("contextmenu",function(){
       return false;
    });
'); ?>

<?php
$this->registerJs('
recalculateCosts();

function sumTable(){
    
    var keys = $("#eventexpense-grid").yiiGridView("getSelectedRows");
    var totals = [0];
    
    var $dataRows = $("#eventexpense-grid tbody tr");
    $dataRows.each(function(){
    
        $(this).find(".sum-cell").each(function(i){
            var currentKey = $(this).closest("tr").data("key");
            var sumRow = false;
            
            // for all rows or selected 
            if (keys.length<1 || $.inArray(currentKey, keys)!=-1) {
                sumRow = true;
            }
            
            if (sumRow==true) {
                var val = $(this).html();
                var el2 = $(this).find(".kv-editable-value");
               
                if (el2.length) {
                    val = el2.html();
                }
                
                if (val=="-" || val=="<em>(brak)</em>") {
                    val = 0;
                }
                
                if ("'.Yii::$app->formatter->decimalSeparator.'".length > 0) {
                    val = val.replace("'.Yii::$app->formatter->decimalSeparator.'", ".");
                }
                val = val.replace("'.Yii::$app->formatter->thousandSeparator.'", "");
                val = val.replace(/[^0-9.,]+/ig, "");
                val = val.replace(",", ".");
                totals[i] += parseFloat(val);
            }
            
        });
    });
    
    var x = 5;
    var y = 5;
    
    for(var j=x;j<=y; j++) {
        $(".kv-page-summary td").eq(j).html(totals[j-x].toFixed(2));
        $(".kv-page-summary td").eq(j).css("white-space", "nowrap");
    }
    
}

sumTable();

$(".kv-row-checkbox").on("change", function(){
    sumTable();
});
');

?>

