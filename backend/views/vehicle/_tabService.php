<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Event; */
$user = Yii::$app->user;
?>
<h3><?= Yii::t('app', 'Naprawy') ?></h3>
<div class="row">
    <div class="col-md-12">
        <div class="panel_mid_blocks">
            <div class="panel_block">
        <?php
        echo GridView::widget([
            'dataProvider'=>$model->getAssignedServices(),
            'tableOptions' => [
                'class' => 'kv-grid-table table table-condensed kv-table-wrap'
            ],
            'columns' => [
                [
                    'class'=>\yii\grid\SerialColumn::className(),
                ],
                'name',
                'description',
                'price',
                'create_time',
                'end_time',
                [
                    'class'=>\yii\grid\ActionColumn::className(),
                    'template'=>'{edit}{delete}',
                    'buttons'=>[

                        'edit'=>function($url, $model, $key)
                        {
                                return Html::a(Html::icon('pencil'), ['vehicle/edit-service',
                                    'id' => $model->id]);
                            
                        },
                        'delete'=>function($url, $model, $key)
                        {
                                return Html::a(Html::icon('trash'), ['vehicle/delete-service',
                                    'id' => $model->id,
                                                ],[ 'data' => [
                                                'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                                                'method' => 'post',
                                            ]]);
                            
                        },                        
                    ],

                ],
            ],

        ]);
        ?>
            </div>
        </div>
    </div>
</div>