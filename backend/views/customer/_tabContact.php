<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Customer; */


if (!Yii::$app->user->can('clientClientsSeeContacts')) {
    return;
}
$user = Yii::$app->user;
?>
<div class="panel-body">
<div class="row">
    <div class="col-md-12">
        <?php echo Html::a(Yii::t('app', 'Dodaj'), ['contact/create', 'customerId'=>$model->id], ['class'=>'btn btn-success']); ?>
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
            'dataProvider'=>$model->getAssignedContacts(),
            'tableOptions' => [
                'class' => 'kv-grid-table table table-condensed kv-table-wrap'
            ],
            'columns' => [
                [
                    'class'=>\yii\grid\SerialColumn::className(),
                ],
                [
                    'attribute'=>'photo',
                    'value'=>function($model, $key, $index, $grid)
                    {
                        return Html::img($model->getPhotoUrl(), ['style'=>'width:100px']);
                    },
                    'format' => 'html',
                ],
                'last_name',
                'first_name',
                'phone',
                'email:email',
                'position',
                [
                    'class'=>\common\components\ActionColumn::className(),
                    'controllerId' => 'contact',
                    'visibleButtons' => [
                    'view' => $user->can('clientContactsSee'),
                    'update' => $user->can('clientContactsEdit'),
                    'delete' => $user->can('clientContactsDelete'),
                ]
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
