<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Customer; */


if (!Yii::$app->user->can('clientClientsSeeContacts')) {
    return;
}

?>
<div class="panel-body">
<div class="row">
    <div class="col-md-12">
        <?php echo Html::a(Yii::t('app', 'Dodaj'), ['meeting/create', 'customerId'=>$model->id], ['class'=>'btn btn-success']); ?>
    </div>
</div>

<div class="panel_mid_blocks">
    <div class="panel_block" style="margin-bottom: 0;">
        <div class="title_box">
            <h4><?php echo Yii::t('app', 'Spotkania'); ?></h4>
        </div>
    </div>


<div class="row">
    <div class="col-md-12">

        <div class="panel_mid_blocks">
            <div class="panel_block">
        <?php
        echo GridView::widget([
            'dataProvider'=>$model->getAssignedMeetings(),
            'tableOptions' => [
                'class' => 'kv-grid-table table table-condensed kv-table-wrap'
            ],
            'columns' => [
            [
                'attribute'=>'name',
                'label'=>Yii::t('app', 'Nazwa'),
                'format'=>'html',
                'value'=>function($model)
                {
                    $content = Html::a($model->name, ['/meeting/view', 'id' => $model->id]);
                    return $content;
                },
            ],
                        'location',
                        'start_time',
                        'end_time',
                        [
                            'value'=>'contact.displayLabel',
                            'attribute' => 'contact_id',
                        ],
            ],

        ]);
        ?>
    </div>
</div>
    </div>
</div>
</div>
</div>
