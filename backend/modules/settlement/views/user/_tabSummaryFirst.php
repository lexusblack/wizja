<?php

use common\models\SettlementUser;
use common\models\User;
use kartik\grid\GridView;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model common\models\SettlementUser */
/* @var $user \common\models\User */
?>

    <div class="panel_mid_blocks">
        <div class="panel_block">
            <?= GridView::widget([
                'dataProvider' => $provider,
                'tableOptions' => [
                    'class' => 'kv-grid-table table table-condensed kv-table-wrap'
                ],
                'filterModel' => $searchModel,
                'rowOptions' => function($model, $key, $index, $grid) {
                    $options = [];
                    if ($model->status == \common\models\SettlementUser::STATUS_SETTLED)
                    {
                        $options['class'] = 'success';
                    }
                    return $options;
                },
                'panel' => [
                    'heading'=>false,
                    'footer'=>false,
                ],
                'columns' => [
                    [
                        'label'=>Yii::t('app', 'Data'),
                        'format' => 'raw',
                        'value'=>function($model)
                        {
	                        return "<div class='nowrap'>od: " . $model->event->getTimeStart() . "</div><div class='nowrap'>do: " . $model->event->getTimeEnd() . "</div>";
                        }
                    ],
                    [
                        'attribute'=>'user_id',
                        'filterType' => GridView::FILTER_SELECT2,
                        'filter'=>\common\models\User::getList(),
                        'filterWidgetOptions' => [
                            'options' => [
                                'placeholder' => Yii::t('app', 'Wybierz...'),
                            ],
                            'pluginOptions' => [
                                'allowClear'=>true,
                            ],
                        ],
                        'value'=>function($model)
                        {
                            return $model->user->displayLabel;
                        },

                    ],
                    [
                        'label'=>Yii::t('app', 'Wydarzenie'),
                        'attribute'=>'event_id',
                        'filterType' => GridView::FILTER_SELECT2,
                        'filter'=>\common\models\Event::getModelList(),
                        'filterWidgetOptions' => [
                            'options' => [
                                'placeholder' => Yii::t('app', 'Wybierz...'),
                            ],
                            'pluginOptions' => [
                                'allowClear'=>true,
                            ],
                        ],
                        'value'=>function($model)
                        {
                            return Html::a($model->event->name, ['/event/view', 'id'=>$model->event_id, '#'=>'tab-working-time']);
                        },
                        'format'=>'html',

                    ],
                    [
                        'label'=>Yii::t('app', 'Miejsce'),
                        'attribute'=>'locationId',
                        'filterType' => GridView::FILTER_SELECT2,
                        'filter'=>\common\models\Location::getModelList(),
                        'filterWidgetOptions' => [
                            'options' => [
                                'placeholder' => Yii::t('app', 'Wybierz...'),
                            ],
                            'pluginOptions' => [
                                'allowClear'=>true,
                            ],
                        ],
                        'value'=>function($model)
                        {
                            if ($model->event->location) {
                                return $model->event->location->displayLabel;
                            }
                        },
                        'format'=>'html',

                    ],
                    [
                        'attribute'=>'code',
                        'value'=>'event.code',
                        'label'=>Yii::t('app', 'ID imprezy'),
                    ],
                    [
                        'label'=>Yii::t('app', 'Manger'),
                        'attribute'=>'managerId',
                        'filterType' => GridView::FILTER_SELECT2,
                        'filter'=>User::getList([User::ROLE_PROJECT_MANAGER, User::ROLE_ADMIN]),
                        'filterWidgetOptions' => [
                            'options' => [
                                'placeholder' => Yii::t('app', 'Wybierz...'),
                            ],
                            'pluginOptions' => [
                                'allowClear'=>true,
                            ],
                        ],
                        'value'=>function($model)
                        {
                            return $model->event->managerDisplayLabel;
                        },
                        'format'=>'html',

                    ],
                    [
                        'attribute'=>'level',
                        'value'=>'event.level',
                        'label'=>Yii::t('app', 'Poziom'),
                    ],
                    'departmentsString:html:'.Yii::t('app', 'Dział'),
                    'rolesString:html:'.Yii::t('app', 'Wyznaczona funkcja'),
                    'workingHoursString:html:'.Yii::t('app', 'Godziny pracy'),
                    'rolesAddonsString:html:'.Yii::t('app', 'Funkcje dodatkowe/Stawka'),
                    'addonsString:html:'.Yii::t('app', 'Koszty dodatkowe/stawka'),
                    'sum:currency:'.Yii::t('app', 'Razem'),

                    [
                        'class' => \common\components\ActionColumn::className(),
                        'template'=>'{status}',
                        'buttons'=> [
                            'status'=>function ($url, $model, $key) {
                                $icon = $model->status == SettlementUser::STATUS_SETTLED ? 'lock' : 'unlock';
                                $status = $model->status == SettlementUser::STATUS_SETTLED ? SettlementUser::STATUS_UNSETTLED : SettlementUser::STATUS_SETTLED;
                                $url = \common\helpers\Url::to(['set-status', 'id'=>$model->id, 'status'=>$status]);
                                return Html::a(\kartik\icons\Icon::show($icon), $url);
                            }
                        ],
                        'visible' => Yii::$app->user->can('usersPaymentsChangeStatus')
                    ],
                ],
            ]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-offset-6 col-md-2">

        </div>
        <div class="col-md-4">
            <div class="panel_mid_blocks">
                <div class="panel_block" style="margin-bottom: 0;">
                    <div class="title_box">
                        <h4><?= Yii::t('app', 'Podsumowanie') ?></h4>
                    </div>
                </div>
            </div>

            <div class="panel_mid_blocks">
                <div class="panel_block">
                    <dl class="dl-horizontal">
                        <dt><?= Yii::t('app', 'Godziny pracy') ?></dt>
                        <dd><?= $data['summary']['salary']; ?></dd>
                        <dt><?= Yii::t('app', 'Funkcje dodatkowe') ?></dt>
                        <dd><?= $data['summary']['roleAddons']; ?></dd>
                        <dt><?= Yii::t('app', 'Dodatki') ?></dt>
                        <dd><?= $data['summary']['addons']; ?></dd>
                        <dt><?= Yii::t('app', 'Suma') ?></dt>
                        <dd><?= $data['summary']['sum']; ?></dd>
                    </dl>
                </div>
            </div>
        </div>

    </div>

<?php

$this->registerJs('

$(".table-bordered").each(function(){
    $(this).removeClass("table-bordered");
});
$(".table-striped").each(function(){
    $(this).removeClass("table-striped");
});


');

$this->registerCss('
.nowrap {
    white-space: nowrap;
}
');