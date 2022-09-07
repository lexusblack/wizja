<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ListView;
use common\components\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\GearGroup */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Zestaw'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

if (!$model->active) {
    echo Yii::t('app', "Model został usunięty");
    return;
}

?>
<div class="gear-group-view">

    <p>
        <?= Html::a('<i class="fa fa-pencil"></i> ' . Yii::t('app', 'Edycja'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fa fa-trash"></i> ' . Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a(Yii::t('app', 'Generuj naklejki QR'), ['#'], ['class' => 'btn btn-primary', 'onclick'=>'createQR(); return false;']);?>
    </p>
<div class="row">
    <div class="col-md-4">
        <div class="row">
            <div class="col-md-12">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5><?php echo $model->name; ?></h5>
                        </div>
                        <div class="ibox-content">
                                <p><strong><?= Yii::t('app', 'Magazyn') ?> :</strong> <?= $model->warehouse?></p>
                                <p><strong><?= Yii::t('app', 'Miejsce') ?> :</strong> <?= $model->location?></p>
                                <p><strong><?= Yii::t('app', 'Opis') ?> :</strong> <?= $model->description?></p>
                                <div style="width:130px;"><?=$model->generateBarCode()?></div>
                                <p><?=$model->generateQrCodeAsLink()?></p>
                                <p><?=Yii::t('app', 'Czy chcesz skopiować miejsce magazynowe do egzemplarzy w case?' )?> <?= Html::a(Yii::t('app', 'Kopiuj'), ['copy-location', 'id'=>$model->id], ['class' => 'btn btn-primary btn-xs copy-location']);?></p>
                        </div>
                    </div>
            </div>

        </div>
        </div>
    <div class="col-md-8">
        <?php
            echo GridView::widget([
                'dataProvider' => new \yii\data\ActiveDataProvider([
                    'query'=>$model->getGearItems(),
                    'pagination' => false,
                    'sort'=>false,
                ]),
                'tableOptions' => [
                    'class' => 'kv-grid-table table table-condensed kv-table-wrap'
                ],
                'columns' => [
                    [
                        'class'=>\yii\grid\SerialColumn::className(),
                    ],
                    [
                        'attribute'=>'photo',
                        'value'=>function ($model, $key, $index, $column)
                        {
                            return Html::a(Html::img($model->getPhotoUrl(), ['width'=>100]), ['gear-item/view', 'id'=>$model->id]);
                        },
                        'format'=>'html',
                    ],
                    [
                        'attribute'=>'gear_id',
                        'value'=>function ($model, $key, $index, $column)
                        {
                            return Html::a($model->gear->name, ['gear/view', 'id'=>$model->gear_id]);
                        },
                        'format'=>'html',
                    ],
                    [
                        'attribute'=>'name',
                        'value'=>function ($model, $key, $index, $column)
                        {
                            return Html::a($model->name, ['gear-item/view', 'id'=>$model->id]);
                        },
                        'format'=>'html',
                    ],
                ]
            ]);
        ?>
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
            location.href = "<?=Url::to('/admin/warehouse/pdf?gear_group_id='.$model->id.'&type=2&columns=')?>"+x;
        });
    }
</script>

<?php
$this->registerJs('
    $(".copy-location").click(function(e){
        e.preventDefault();
        $.ajax({url: $(this).attr("href"), success: function(result){
            toastr.success("'.Yii::t('app', 'Skopiowano!').'")
    }});
    });');