<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\tabs\TabsX;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Gear */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Magazyn'), 'url' => ['/warehouse/index']];
if ($model->category->lvl>1)
{
    $category = $model->category->getMainCategory();
    $this->params['breadcrumbs'][] = ['label' => $category->name, 'url' => ['/warehouse/index', 'c'=>$category->id]];
    $this->params['breadcrumbs'][] = ['label' => $model->category->name, 'url' => ['/warehouse/index', 'c'=>$category->id, 's'=>$model->category->id]];
}else{
    $this->params['breadcrumbs'][] = ['label' => $model->category->name, 'url' => ['/warehouse/index', 'c'=>$model->category->id]];
}

$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;

?>
<div class="gear-view">
<?php
if (!$model->active) { ?>
<div class="alert alert-danger">
                                <?=Yii::t('app', "UWAGA!!! Model został usunięty");?>.
                            </div>
    
    <?php
}
?>
    <p>     
        <?= Html::a('<i class="fa fa-calendar"></i> ' . Yii::t('app', 'Kalendarz'), ['calendar', 'id' => $model->id], ['class' => 'btn btn-success calendar-button']) ?>   <?= Html::a('<i class="fa fa-calendar"></i> ' . Yii::t('app', 'Kalendarz'), ['/gear/wizja', 'id'=>$model->id], ['class'=>'btn btn-primary', 'data-id'=>$model->id, 'target'=>'_blank'])     ?>     
        <?php if ($user->can('gearEdit')){ ?>
        <?= Html::a('<i class="fa fa-pencil"></i> ' . Yii::t('app', 'Edycja'), ['update', 'id' => $model->id, 'back'=>'view'], ['class' => 'btn btn-primary']) ?>
        <?php } ?>
        <?php if ($user->can('gearDelete')){ ?>
        <?= Html::a('<i class="fa fa-trash"></i> ' . Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                'method' => 'post',
            ],
        ]) ?>
        <?php } ?>
                
        <?php if ($user->can('gearEdit')){ ?>
       <?php if ($model->no_items){ ?>
        <?= Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj egzemplarze'), ['/gear/edit-items', 'id' => $model->id, 'type'=>1], [
            'class' => 'btn btn-primary',
        ]) ?>
        <?= Html::a('<i class="fa fa-trash"></i> ' . Yii::t('app', 'Usuń egzemplarze'), ['/gear/edit-items', 'id' => $model->id, 'type'=>2], [
            'class' => 'btn btn-danger',
        ]) ?>
         <?php } ?>
        <?php } ?>
                <?php 
        if ($user->can('gearOurWarehouseMoveGear')){
        if ($model->no_items){ ?>
                <?= Html::a('<i class="fa fa-exchange"></i> ' . Yii::t('app', 'Przenieś egzemplarze'), ['/gear/edit-items', 'id' => $model->id, 'type'=>3], [
            'class' => 'btn btn-success',
        ]) ?>
        <?php } ?>
        <?php } ?>
        <?php if ($user->can('gearCrossRentalCreate')){ 
            if ($model->getCrossRental()){
            echo Html::a('<i class="fa fa-globe"></i> ' . Yii::t('app', 'Przestań udostępniać w Cross Rental'), ['/cross-rental/delete', 'gear_id' => $model->id], ['class' => 'btn btn-danger']);
        }else{
        echo Html::a('<i class="fa fa-globe"></i> ' . Yii::t('app', 'Udostępnij w Cross Rental'), ['/cross-rental/create', 'gear_id' => $model->id], ['class' => 'btn btn-success']);
        } }?>
        <?= Html::a(Yii::t('app', 'Generuj naklejki QR'), ['#'], ['class' => 'btn btn-primary', 'onclick'=>'createQR(); return false;']);?>
    </p>

<div class="row">
    <div class="col-md-3">
        <div class="row">
            <div class="col-md-12">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5><?php echo $model->name; ?></h5>
                        </div>
                        <div class="ibox-content">
                        <p><strong><?= Yii::t('app', 'Typ') ?>: </strong><?php echo \common\models\Gear::getTypeList()[$model->type]; ?></p>
                        <?php if ($model->type!=2){ ?>
                                <p><strong><?= Yii::t('app', 'Wymiary') ?> [<?= Yii::t('app', 'cm') ?>]:</strong> <?= Yii::t('app', "sz") .":".$model->width.", ".Yii::t('app', "wys").":".$model->height.", ".Yii::t('app', "gł").":".$model->depth ?></p>
                                <p><strong><?= Yii::t('app', 'Objętość') ?> [<?= Yii::t('app', 'm') ?>3]:</strong><?php echo $model->volume; ?></p>
                                <p><strong><?= Yii::t('app', 'Waga') ?> [<?= Yii::t('app', 'kg') ?>]:</strong><?php echo $model->weight; ?></p>
                                <p><strong><?=Yii::t('app', 'Pobór prądu') ?> [<?= Yii::t('app', 'W') ?>]:</strong><?php echo $model->power_consumption; ?></p>
                                <p><strong><?=Yii::t('app', 'Pakowanie') ?>:</strong> <?php foreach ($model->getPacking() as $case){ echo $case." ";} ?></p>
                                 <p><strong><?= Yii::t('app', 'Magazyny') ?> :</strong>
                                 <?php 
                                    $total = 0;
                                    if ($model->no_items==true)
                                                {
                                                   $q= $model->quantity;
                                                }
                                                else
                                                {
                                                    $q= $model->getGearItems()->andWhere(['active' => 1])->count();
                                                }
                            $warehouses = \common\models\Warehouse::find()->all();
                            foreach ($warehouses as $w)
                            {
                                echo $w->getNumberLabel($model);
                                $total += $w->getNumber($model);
                            }
                            $e = $model->getOnEvents();
                            $total+=$e;
                            if ($e)
                            {
                                echo "<br/><span class='label label-primary' style='padding:1px; background-color:#555'>".$e."</span> ".Yii::t('app', 'Na eventach');
                            }
                            $left = $q-$total;
                            if ($left>0)
                            {
                                echo "<br/><span class='label label-primary' style='padding:1px; background-color:#000'>".$left."</span> ".Yii::t('app', 'Nieprzypisane');
                            }
                                 ?></p>
                                <p><strong><?= Yii::t('app', 'Miejsce') ?> :</strong> <?= $model->location?></p>                               
                                <p><strong><?=Yii::t('app', 'Sztuk w magazynach') ?>:</strong>           
                                <?php          if ($model->no_items==true)
                                                {
                                                    echo $model->quantity;
                                                }
                                                else
                                                {
                                                    echo $model->getGearItems()->andWhere(['active' => 1])->count();
                                                } ?>
                                                </p>
                                <?php if ($model->no_items){ 
                                    if (isset($model->gearItems[0])){
                                    $no_item = $model->gearItems[0];
                                    ?>
                                <div style="width:130px;"><?=$no_item->generateBarCode()?></div>
                                <p><?=$no_item->generateQrCodeAsLink()?></p> 
                                <?php } }else{ ?>
                                <div style="width:130px;"><?=$model->generateBarCode()?></div>
                                <p><?=$model->generateQrCodeAsLink()?></p> 
                                 <?php   } ?>
                        <?php } ?>
                        </div>
                        <?php if ($model->getPhotoUrl()) { ?>
                            <div class="ibox-content no-padding border-left-right">
                                <img alt="image" class="img-responsive" src="<?php echo $model->getPhotoUrl(); ?>">
                            </div>
                        <?php } ?>
                    </div>
            </div>

        </div>
        </div>
    <div class="col-md-9">
    <div class="tabs-container">
        <?php
        $tabItems = [];
        if (!$model->no_items) {
            $tabItems = [
                [
                    'label'=>'<i class="fa fa-cogs"></i> '.Yii::t('app', 'Egzemplarze'),
                    'content'=>$this->render('_tabItems', ['model'=>$model]),
                    'active'=>true,
                    'options'=> [
                    'id'=>'tab_cogs']
                ]
            ];
        }
        $tabItems[] =
                [
                    'label'=>'<i class="fa fa-cogs"></i> '.Yii::t('app', 'Powiązane'),
                    'content'=>$this->render('_tabConnected', ['model'=>$model]),
                    'active'=>false,
                    'options'=> [
                    'id'=>'tab_connected']
                ];
        $tabItems[] =
                [
                    'label'=>'<i class="fa fa-cogs"></i> '.Yii::t('app', 'Podobne'),
                    'content'=>$this->render('_tabSimilar', ['model'=>$model]),
                    'active'=>false,
                    'options'=> [
                    'id'=>'tab_similar']
                ];
        $tabItems[] = [
                'label'=>'<i class="fa fa-info"></i> '.Yii::t('app', 'Historia wydarzenia'),
                'content'=>$this->render('_tabHistory', ['model'=>$model, 'dataProvider'=>$dataProvider]),
                'active'=>false,
                'options'=> [
                    'id'=>'tab_history']
                ];
        $tabItems[] = [
                'label'=>'<i class="fa fa-info"></i> '.Yii::t('app', 'Historia wypożyczenia'),
                'content'=>$this->render('_tabHistory2', ['model'=>$model, 'dataProvider'=>$dataProvider]),
                'active'=>false,
                'options'=> [
                    'id'=>'tab_history2']
                ];
        if ($model->no_items)
        {
            $item = \common\models\GearItem::find()->where(['gear_id'=>$model->id, 'active'=>1])->one();
            $serviceSearchModel = new \common\models\GearServiceSearch();
            $params = Yii::$app->request->queryParams;
            $params[$serviceSearchModel->formName()]['gear_item_id'] = $item->id;
            $serviceDataProvider = $serviceSearchModel->search($params);
            $serviceDataProvider->sort->defaultOrder = ['update_time'=>SORT_DESC];
                    $tabItems[] = [
                'label'=>'<i class="fa fa-cogs"></i> '.Yii::t('app', 'Serwis'),
                'content'=>$this->render('_tabService', ['model'=>$model, 'serviceDataProvider'=>$serviceDataProvider, 'serviceSearchModel'=> $serviceSearchModel]),
                'active'=>false,
        ];
        }
        $tabItems[] = [
                'label'=>'<i class="fa fa-info"></i> '.Yii::t('app', 'Informacje'),
                'content'=>$this->render('_tabInfo', ['model'=>$model]),
                'active'=>false,
                'options'=> [
                    'id'=>'tab_info']
        ];
        $tabItems[] = [
                'label'=>'<i class="fa fa-info"></i> '.Yii::t('app', 'Historia przesunięć'),
                'content'=>$this->render('_tabHistory3', ['model'=>$model]),
                'active'=>false,
                'options'=> [
                    'id'=>'tab_history3']
        ];
        $tabItems[] = [
                'label'=>'<i class="fa fa-info"></i> '.Yii::t('app', 'Historia aktualności'),
                'content'=>$this->render('_tabHistory4', ['model'=>$model]),
                'active'=>false,
                'options'=> [
                    'id'=>'tab_history4']
        ];
        if (Yii::$app->user->can('gearAttachments')) {
            $tabItems[] = [
                'label' => '<i class="fa fa-cogs"></i> ' . Yii::t('app', 'Załączniki'),
                'content' => $this->render('_tabAttachment', ['model' => $model]),
                'active' => false,
                'options'=> [
                    'id'=>'tab_attachments'
                    ]
            ];
        }
            $tabItems[] = [
                'label' => Yii::t('app', 'Uwagi'),
                'content' => $this->render('_tabAlert', ['model' => $model]),
                'active' => false,
                'options'=> [
                    'id'=>'tab_alert'
                    ]
            ];
        if ($model->type ==3){
            $tabItems[] = [
                'label' => Yii::t('app', 'Zakupy'),
                'content' => $this->render('_tabPurchase', ['model' => $model]),
                'active' => false,
                'options'=> [
                    'id'=>'tab_purchase'
                    ]
            ];           
        }
        if ($user->can('gearRfid')){
                    $tabItems[] =
                [
                    'label'=>'<i class="fa fa-cogs"></i> '.Yii::t('app', 'NEIS'),
                    'content'=>$this->render('_tabRfid', ['model'=>$model, 'rfids' => $rfids]),
                    'active'=>false,
                    'options'=> [
                        'id'=>'tab_rfid'
                    ]
                ];
        }


        $tabItems[] = [
                'label' => Yii::t('app', 'Zadania'),
                'content' => $this->render('_tabTask', ['model' => $model]),
                'active' => false,
                'options'=> [
                    'id'=>'tab_task'
                    ]
            ];

        $tabItems[] = [
                'label' => Yii::t('app', 'Stawki'),
                'content' => $this->render('_tabFinance', ['model' => $model, 'priceForm'=>$priceForm,
            'groups'=>$groups]),
                'active' => false,
                'options'=> [
                    'id'=>'tab_finance'
                    ]
            ];
        $tabItems[] = [
                'label' => Yii::t('app', 'Tłumaczenia'),
                'content' => $this->render('_tabTranslate', ['model' => $model]),
                'active' => false,
                'options'=> [
                    'id'=>'tab_translate'
                    ]
            ];
        echo TabsX::widget([
            'items'=>$tabItems,
            'encodeLabels'=>false,
            'enableStickyTabs'=>true,
        ]);
        ?>
    </div>
</div>
</div>
</div>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
    function createQR()
    {
        swal({
          text: 'Podaj liczbę kolumn w dokumencie',
          content: {
            element: "input",
            attributes: {
              placeholder: "Podaj wartość",
              type: "number",
              value:3
            }
        },
          button: {
            text: "OK",
            closeModal: true,
          },
        })
        .then(name => {
          if (!name) name=3;
            x = name;
            location.href = "<?=Url::to('/admin/warehouse/pdf?gear_id='.$model->id.'&type=2&columns=')?>"+x;
        });
    }
</script>

<?php
$this->registerJs('
    $(".calendar-button").click(function(e){
    e.preventDefault();
    wname = "kalendarz'.$model->id.'";
    window.open($(this).attr("href"), wname ,"height=500,width=850");
})');

