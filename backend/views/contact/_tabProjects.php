<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Customer; */
$user = Yii::$app->user;
?>
<div class="panel-body">
<div class="row">
    <div class="col-md-12">
        <?php if ($user->can('eventsEventAdd')) {
            echo Html::a(Yii::t('app', 'Dodaj'), ['contact/create', 'customerId'=>$model->id], ['class'=>'btn btn-success']);
        } ?>
    </div>
</div>

<div class="panel_mid_blocks">
    <div class="panel_block" style="margin-bottom: 0;">
        <div class="title_box">
            <h4><?php echo Yii::t('app', 'Kontakty'); ?></h4>
        </div>
    </div>


<div class="row">
    <div class="col-md-12">

        <div class="panel_mid_blocks">
            <div class="panel_block">
        <?php
        echo GridView::widget([
            'dataProvider'=>$model->getAssignedEvents(),
            'tableOptions' => [
                'class' => 'kv-grid-table table table-condensed kv-table-wrap'
            ],
            'columns' => [
                [
                    'class'=>\yii\grid\SerialColumn::className(),
                ],
                'name',
                [
                'value'=>'location.displayLabel',
                'attribute'=>'location_id'
                ],
                'event_start',
                [
                    'class'=>\common\components\ActionColumn::className(),
                    'controllerId' => 'event',
                    'template' => '{view}',
                    'visible' => $user->can('eventEventEditEye')
                ]
            ],

        ]);
        ?>
    </div>
</div>
    </div>
</div>
</div>
</div>
