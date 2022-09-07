<?php

use yii\helpers\Html;
use common\components\grid\GridView;
use kartik\helpers\Enum;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel common\models\MeetingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Spotkania');
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user;

?>
<div class="meeting-index">

    <p>
        <?php if ($user->can('eventMeetingAdd')) {
            echo Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']);
        }
        ?>
    </p>
    <div class="panel_mid_blocks">
        <div class="panel_block">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => [
                        'class' => 'kv-grid-table table table-condensed kv-table-wrap'
                    ],
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'name',
                'label'=>Yii::t('app', 'Nazwa'),
                'format'=>'html',
                'value'=>function($model)
                {
                    $content = Html::a($model->name, ['view', 'id' => $model->id]);
                    return $content;
                },
            ],
                        'location',
                        'start_time',
                        'end_time',
                        [
                            'value'=>'customer.displayLabel',
                            'filter' => \common\models\Customer::getList(),
                            'attribute' => 'customer_id',
                            'filterType' => GridView::FILTER_SELECT2,
                             'filterWidgetOptions' => [
                                'options' => [
                                    'placeholder' => Yii::t('app', 'Wybierz...'),
                                ],
                                'pluginOptions' => [
                                    'allowClear'=>true,
                                ],
                            ],
                        ],
                        [
                            'value'=>'contact.displayLabel',
                            'filter' => \common\models\Contact::getList(),
                            'attribute' => 'contact_id',
                            'filterType' => GridView::FILTER_SELECT2,
                             'filterWidgetOptions' => [
                                'options' => [
                                    'placeholder' => Yii::t('app', 'Wybierz...'),
                                ],
                                'pluginOptions' => [
                                    'allowClear'=>true,
                                ],
                            ],
                        ],
                        [
                            'label' => Yii::t('app', 'Powiadomienia sms'),
                            'format' => 'html',
                            'value' => function($model) {
                                if ($model->notificationSmses) {
                                    $button = null;
                                    if (new DateTime() < new DateTime($model->notificationSmses[0]->sending_time)) {
                                        $button = " " . Html::a(Yii::t('app', 'Usuń'), ['meeting/delete-sms', 'id' => $model->id], ['class' => 'btn btn-primary delete_sms']);
                                    }
                                    return $model->notificationSmses[0]->sending_time . $button;
                                }
                            }
                        ],
                        [
                            'label' => Yii::t('app', 'Powiadomienia mailowe'),
                            'format' => 'html',
                            'value' => function($model) {
                                if ($model->notificationMails) {
                                    $button = null;
                                    if (new DateTime() < new DateTime($model->notificationMails[0]->sending_time)) {
                                        $button = " " . Html::a(Yii::t('app', 'Usuń'), ['meeting/delete-mail', 'id' => $model->id], ['class' => 'btn btn-primary delete_sms']);
                                    }
                                    return $model->notificationMails[0]->sending_time . $button;
                                }
                            }
                        ],

                        [
                            'class' => 'yii\grid\ActionColumn',
                            'visibleButtons' => [
                                'view' => $user->can('eventMeetingView'),
                                'update' => $user->can('eventMeetingEdit'),
                                'delete' => $user->can('eventMeetingDelete'),
                            ],
                        ],
                    ],
                ]); ?>
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

$(".delete_sms").click(function(e){
    e.preventDefault();
    $(this).parent().html("-");
    $.post($(this).attr("href"));

});


');