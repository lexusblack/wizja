<?php

use yii\helpers\Html;
use yii\helpers\Url;

use kartik\tabs\TabsX;

/* @var $this yii\web\View */
/* @var $model common\models\HallGroup */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Powierzchnie'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hall-group-view">

    <div class="row">
        <div class="col-sm-3" style="margin-top: 15px">
            
            <?= Html::a(Yii::t('app', 'Edytuj'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ])
            ?>
        </div>
    </div>
    <div class="row">
    <div class="col-md-4">
    <div class="row">
            <div class="col-md-12">
                        <div class="ibox">
                        <div class="ibox-title">
                            <h5><?php echo $model->name; ?></h5>
                        </div>
                        <div class="ibox-content no-padding border-left-right">
                                <?= Html::img($model->getPhotoUrl(), ['class'=>'img-fluid', 'style'=>'max-width:100%;'])?>
                            </div>
                        <div class="ibox-content">
                        <p><b><?=Yii::t('app', 'Segmenty')?></b>: 
                        <?php $first = true; foreach ($model->halls as $hall){ if (!$first){ echo ", ";} $first = false; echo $hall->name;} ?></p>
                        <p><b><?=Yii::t('app', 'Powierzchnia')?></b>: <?=$model->area?> m<sup>2</sup></p>
                        <p><b><?=Yii::t('app', 'Wymiary')?></b>: <?=$model->width?> m x <?=$model->length?> m</p>
                        <p><b><?=Yii::t('app', 'Wysokość')?></b>: <?=$model->height?> m</p>
                        </div>
                        </div>
                    </div>
    </div>
    </div>
    <div class="col-md-4">
    <div class="row">
            <div class="col-md-12">
                        <div class="ibox">
                        <div class="ibox-title">
                            <h5><?php echo Yii::t('app', 'Ustawienia')?></h5>
                        </div>
                        <div class="ibox-content">
                        <table class="table">
                        <?php foreach (\common\models\HallAudienceType::find()->orderBy(['position'=>SORT_ASC])->all() as $type){ ?>
                            <tr><td><b><?=$type->name?>: </b></td><td>
                            <?php $audience = \common\models\HallAudience::find()->where(['hall_audience_type_id'=>$type->id, 'hall_group_id'=>$model->id])->one();
                                if ($audience)
                                {
                                    $val = $audience->audience;
                                }else{
                                    $val = 0;
                                }
                             ?>
                             <input type="text" value="<?=$val?>"/ data-typeid="<?=$type->id?>" class="form-control audience-input"></td>
                            </tr>

                        <?php    }?>
                        </table>
                        </div>
                        </div>
                    </div>
    </div>
    </div>
    <div class="col-md-4">
    <div class="row">
            <div class="col-md-12">
                        <div class="ibox">
                        <div class="ibox-title">
                            <h5><?php echo Yii::t('app', 'Najbliższe eventy')?></h5>
                        </div>
                        <div class="ibox-content">
                        </div>
                        </div>
                    </div>
    </div>
    </div>
    </div>

<div class="row">
<div class="tabs-container">
<?php
                $tabItems = [
                
                [
                    'label'=>Yii::t('app', 'Notatki'),
                    'content'=>$this->render('_tabNotes', ['model'=>$model]),
                    'options'=> [
                        'id'=>'tab-notes',
                    ],
                    'active'=>true,
                ],
                [
                    'label'=>Yii::t('app', 'Załączniki'),
                    'content'=>$this->render('_tabAttachment', ['model'=>$model]),
                    'options'=> [
                        'id'=>'tab-attachments',
                    ],
                ],
                
                [
                    'label'=>Yii::t('app', 'Sprzęt'),
                    'content'=>$this->render('_tabGear', ['model'=>$model]),
                    'options'=> [
                        'id'=>'tab-gear',
                    ]
                ],
                
                [
                    'label'=>Yii::t('app', 'Historia'),
                    'content'=>$this->render('_tabHistory', ['model'=>$model]),
                    'options'=> [
                        'id'=>'tab-history',
                    ]
                ],
                [
                    'label'=>Yii::t('app', 'Eventy'),
                    'content'=>$this->render('_tabEvent', ['model'=>$model]),
                    'options'=> [
                        'id'=>'tab-event',
                    ]
                ],                
                [
                    'label'=>Yii::t('app', 'Stawki'),
                    'content'=>$this->render('_tabPrice', ['model'=>$model]),
                    'options'=> [
                        'id'=>'tab-price',
                    ]
                ],
                [
                    'label'=>Yii::t('app', 'Koszty'),
                    'content'=>$this->render('_tabCost', ['model'=>$model]),
                    'options'=> [
                        'id'=>'tab-cost',
                    ]
                ],
                ];
            echo TabsX::widget([
                'items'=>$tabItems,
                'id'=>'eventTabs',
                'encodeLabels'=>false,
                'enableStickyTabs'=>true,]);
?>
</div>
</div>
</div>

<?php
$changeAudienceUrl = Url::to(['hall-group/save-audience', 'id'=>$model->id]);
$this->registerJs('
    $(".audience-input").change(function(e){
        data = {audience:$(this).val(), type_id:$(this).data("typeid")};
        $.ajax({
            data: data,
            type: "POST",
            url: "'.$changeAudienceUrl.'",

        }).done(function(success) {
              toastr.success("'.Yii::t('app', 'Zapisano').'");
            });
    });
    ');


