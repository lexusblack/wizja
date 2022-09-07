<?php

use yii\helpers\Html;
use common\components\grid\GridView;
use kartik\grid\CheckboxColumn;
use kartik\grid\SerialColumn;
use common\components\grid\LabelColumn;
//use kartik\grid\GridView;
use kartik\helpers\Enum;
use kartik\dynagrid\DynaGrid;
/* @var $this yii\web\View */
/* @var $searchModel common\models\VehicleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Pojazdy');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="vehicle-index">

    <p>
        <?php
        if ($user->can('fleetVehiclesCreate')) {
            echo Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']);
        } ?>
    </p>
    <div class="panel_mid_blocks">
        <div class="panel_block">
    <?= DynaGrid::widget([
        'gridOptions'=>[
            'filterSelector'=>'.grid-filters',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap'
            ],
            'id'=>'vehicles-grid',

        
            'toolbar' => [
                '{dynagrid}',
                '{dynagridFilter}',
                '{dynagridSort}'

                ],
        ],
        'allowThemeSetting'=>false,
        'storage'=>DynaGrid::TYPE_COOKIE,
        'options'=>['id'=>'dynagrid-events'],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            ['class'=>\common\components\grid\PhotoColumn::className()],
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
            'name_in_offer',
            [
            'attribute' => 'description',
            'format'=>'raw'
            ],
            
            'registration_number',
            'vin_number',
            [
                'attribute'=>'type',
                'value'=>function($model)
                {
                    return $model->getTypeLabel();
                },
                'filter'=>\common\models\Vehicle::typeList(),

            ],
            'inspection_date',
            'oc_date',
            [
                'attribute'=>'status',
                'value'=>function($model)
                {
                   if ($model->status==1)
                   {
                    return Yii::t('app', 'Sprawny');
                   }else{
                    return Yii::t('app', 'W naprawie');
                   }
                },

            ],
            [
                'label' => Yii::t('app', 'Przypomnienie sms'),
                'format' => 'html',
                'value' => function($model) {
                    if ($model->notificationSmses) {
                        $button = null;
                        if (new DateTime() < new DateTime($model->notificationSmses[0]->sending_time)) {
                            $button = " " . Html::a(Yii::t('app', 'Usuń'), ['vehicle/delete-sms', 'id' => $model->id], ['class' => 'btn btn-primary delete_sms']);
                        }
                        return $model->notificationSmses[0]->sending_time . $button;
                    }
                }
            ],
            [
                'label' => Yii::t('app', 'Przypomnienie mailowe'),
                'format' => 'html',
                'value' => function($model) {
                    if ($model->notificationMails) {
                        $button = null;
                        if (new DateTime() < new DateTime($model->notificationMails[0]->sending_time)) {
                            $button = " " . Html::a(Yii::t('app', 'Usuń'), ['vehicle/delete-mail', 'id' => $model->id], ['class' => 'btn btn-primary delete_sms']);
                        }
                        return $model->notificationMails[0]->sending_time . $button;
                    }
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'visibleButtons' => [
                    'update'=>$user->can('fleetVehiclesEdit'),
                    'delete'=>$user->can('fleetVehiclesDelete'),
                    'view'=>$user->can('fleetVehiclesView'),
                ]
            ],
        ],
    ]); ?>
        </div>
    </div>
</div>

<?php
$this->registerJs('

$(".delete_sms").click(function(e){
    e.preventDefault();
    $(this).parent().html("-");
    $.post($(this).attr("href"));

});


');

$this->registerCss('
    .display_none {display: none;}
    .panel .panel-heading{display:none}
');