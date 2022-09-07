<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Event; */
$user = Yii::$app->user;
?>
<h3><?= Yii::t('app', 'Załączniki') ?></h3>
<div class="row">
    <div class="col-md-12">
        <?php
        if ($user->can('fleetAttachmentsCreate')) {
            echo Html::a(Yii::t('app', 'Dodaj'), ['vehicle-attachment/create', 'id' => $model->id], ['class' => 'btn btn-success']);
        } ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="panel_mid_blocks">
            <div class="panel_block">
        <?php
        echo GridView::widget([
            'dataProvider'=>$model->getAssignedAttachements(),
            'tableOptions' => [
                'class' => 'kv-grid-table table table-condensed kv-table-wrap'
            ],
            'columns' => [
                [
                    'class'=>\yii\grid\SerialColumn::className(),
                ],
                [
                    'attribute' => 'filename',
                    'value'=>function($model) use ($user)
                    {
                        if ($user->can('fleetAttachmentsDownload')) {
                            return Html::a($model->filename, ['vehicle-attachment/download', 'id' => $model->id]);
                        }
                    },
                    'format' => 'html',
                ],
                [
                    'class'=>\yii\grid\ActionColumn::className(),
                    'template'=>'{download}',
                    'buttons'=>[

                        'download'=>function($url, $model, $key) use ($user)
                        {
                            if ($user->can('fleetAttachmentsDownload')) {
                                return Html::a(Html::icon('save-file'), ['vehicle-attachment/download',
                                    'id' => $model->id]);
                            }
                        }
                    ],

                ],
            ],

        ]);
        ?>
            </div>
        </div>
    </div>
</div>