<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\tabs\TabsX;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\GearItem */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Magazyn'), 'url' => ['/warehouse/index']];
if ($model->gear->category->lvl>1)
{
    $category = $model->gear->category->getMainCategory();
    $this->params['breadcrumbs'][] = ['label' => $category->name, 'url' => ['/warehouse/index', 'c'=>$category->id]];
    $this->params['breadcrumbs'][] = ['label' => $model->gear->category->name, 'url' => ['/warehouse/index', 'c'=>$category->id, 's'=>$model->gear->category->id]];
}else{
    $this->params['breadcrumbs'][] = ['label' => $model->gear->category->name, 'url' => ['/warehouse/index', 'c'=>$model->gear->category->id]];
}
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Model'), 'url' => ['/gear/view', 'id'=>$model->gear_id]];
$this->params['breadcrumbs'][] = $this->title;

if (!$model->active) {
    echo Yii::t('app', "Egzemplarz został usunięty");
    return;
}

if (Yii::$app->session->getFlash('succes')) {
    ?>
    <div class="alert alert-warning alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <?= Yii::$app->session->getFlash('succes'); ?>
    </div><?php
}

?>
<div class="gear-item-view">

    <p>
        <?= Html::a('<i class="fa fa-pencil"></i> ' . Yii::t('app', 'Edycja'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fa fa-trash"></i> ' . Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                'method' => 'post',
            ],
        ]) ?>
        <?php
            if ($model->status == \common\models\GearItem::STATUS_ACTIVE)
            {
                echo Html::a('<i class="fa fa-gear"></i> ' . Yii::t('app', 'Wyślij na serwis'), ['/gear-service/create', 'id' => $model->id], ['class' => 'btn btn-warning']);
            }else{
                $service = \common\models\GearService::getCurrentModel($model->id);
                if ($service != null) {
                    echo Html::a($service->serviceStatus->name, ['/gear-service/view', 'id'=>$service->id], ['class'=>'btn', 'style'=>'color:white; background-color:'.$service->serviceStatus->color]);
                                }
            }
        ?>
        <?= Html::a(Yii::t('app', 'Generuj naklejkę QR'), ['#'], ['class' => 'btn btn-primary', 'onclick'=>'createQR(); return false;']);?>

    </p>

<div class="row">
    <div class="col-md-4">
        <div class="row">
            <div class="col-md-12">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5><?php echo $model->name." [ numer:".$model->number."]"; ?></h5>
                        </div>
                        <div class="ibox-content">
                                <?php if ($model->warehouse_id){ ?> 
                                <p><strong><?= Yii::t('app', 'Magazyn') ?> :</strong> <?= $model->warehouseModel->name?></p>
                                <?php } ?>
                                <?php if ($model->event_id){ ?> 
                                <p><strong><?= Yii::t('app', 'Wydany') ?> :</strong> <?= $model->event->name?></p>
                                <?php } ?>
                                <?php if ($model->rent_id){ ?> 
                                <p><strong><?= Yii::t('app', 'Wydany') ?> :</strong> <?= $model->rent->name?></p>
                                <?php } ?>
                                <?php if ($model->one_in_case){ ?>
                                <p><strong><?= Yii::t('app', 'Wymiary') ?> [<?= Yii::t('app', 'cm') ?>]:</strong> <?= Yii::t('app', "sz").":".$model->width.", ".Yii::t('app', 'wys').":".$model->height.", ".Yii::t('app', 'gł').":".$model->depth ?></p>
                                <p><strong><?= Yii::t('app', 'Objętość') ?> [<?= Yii::t('app', 'm') ?>3]:</strong><?php echo $model->volume; ?></p>
                                <?php }else{?>
                                <p><strong><?= Yii::t('app', 'Wymiary') ?> [<?= Yii::t('app', 'cm') ?>]:</strong> <?= Yii::t('app', "sz").":".$model->gear->width.", ".Yii::t('app', 'wys').":".$model->gear->height.", ".Yii::t('app', 'gł').":".$model->gear->depth ?></p>
                                <p><strong><?= Yii::t('app', 'Objętość') ?> [<?= Yii::t('app', 'm') ?>3]:</strong><?php echo $model->gear->volume; ?></p>
                                <?php if (isset($model->gear)){ ?>
                                <p><strong><?= Yii::t('app', 'Case') ?>:</strong><?php echo $model->gear->name; ?></p>
                                <?php } ?>
                                <?php } ?>
                                <p><strong><?= Yii::t('app', 'Waga') ?> [<?= Yii::t('app', 'kg') ?>]: </strong><?php echo $model->gear->weight; ?></p>
                                <p><strong><?= Yii::t('app', 'Pobór prądu') ?> [<?= Yii::t('app', 'W') ?>]:</strong><?php echo $model->gear->power_consumption; ?></p> 
                                <p><strong><?= Yii::t('app', 'Godziny lamp') ?>:</strong><?php echo $model->lamp_hours; ?></p>
                                <div style="width:130px;"><?=$model->generateBarCode()?></div>
                                <p><?=$model->generateQrCodeAsLink()?></p>
                                                        <p><strong><?= Yii::t('app', 'Opis') ?>:</strong><?=$model->description?></p>

                        </div>
                        <?php if ($model->gear->getPhotoUrl()) { ?>
                            <div class="ibox-content no-padding border-left-right">
                                <img alt="image" class="img-responsive" src="<?php echo $model->gear->getPhotoUrl(); ?>">
                            </div>
                        <?php } ?>
                    </div>
            </div>

        </div>
        </div>
    <div class="col-md-8">
    <div class="tabs-container">
        <?php

        $tabItems[] = [
                'label'=>'<i class="fa fa-info"></i> '.Yii::t('app', 'Historia wydarzeń'),
                'content'=>$this->render('_tabHistory', ['model'=>$model]),
                'active'=>false,
        ];
        $tabItems[] = [
                'label'=>'<i class="fa fa-cogs"></i> '.Yii::t('app', 'Serwis'),
                'content'=>$this->render('_tabService', ['model'=>$model, 'serviceDataProvider'=>$serviceDataProvider, 'serviceSearchModel'=> $serviceSearchModel]),
                'active'=>false,
        ];


        echo TabsX::widget([
            'items'=>$tabItems,
            'encodeLabels'=>false,
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
            location.href = "<?=Url::to('/admin/warehouse/pdf?gear_item_id='.$model->id.'&type=2&columns=')?>"+x;
        });
    }
</script>