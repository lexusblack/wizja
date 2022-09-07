<?php

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
use kartik\helpers\Enum;
use yii\bootstrap\Html;
use kartik\export\ExportMenu;
use common\components\grid\GridView;

$this->title = 'Historia aktualnoœci';
$this->params['breadcrumbs'][] = $this->title;
?>
 <?php 
        $months = Enum::monthList();
    $months = array_merge([Yii::t('app', 'Wszystkie')], $months);  ?>
<div class="note-index">
<?=Html::a('Historia logów wydarzeñ', ['/event-log/index'], ['class'=>'btn btn-primary'])." "?>
<?=Html::a('Historia logów wypo¿yczeñ', ['/event-log/rent-index'], ['class'=>'btn btn-info'])?>
    <h1><?= Html::encode($this->title) ?></h1>
            <div class="title_box row">
            <div class="col-lg-4">
            <form class="form-inline">
                <?php echo Html::a(Html::icon('arrow-left'), ['index', 'm'=>$prev['m'], 'y'=>$prev['y']], ['class'=>'btn btn-md btn-primary date-drop']); ?>
                <?php echo Html::dropDownList('y', $y, Enum::yearList(2016, date('Y'), true), ['class'=>'form-control grid-filter', 'id'=>'year']); ?>
                <?php echo Html::dropDownList('m',$m, $months, ['class'=>'form-control grid-filter', 'id'=>'month']); ?>
                <?php echo Html::a(Html::icon('arrow-right'), ['index', 'm'=>$next['m'], 'y'=>$next['y']], ['class'=>'btn btn-md  btn-primary date-drop']); ?>
                </form>
                <?php echo Html::activeHiddenInput($searchModel, 'useRange', ['class'=>'grid-filter', 'id'=>'date-use-range']); ?>
            </div>
                <div class="col-lg-3">
                    <?php
                    $this->registerJs('
                        $(".date-drop").on("change", function(e){
                            location.href="/admin/note/index?m="+$("#month").val()+"&y="+$("#year").val();
                        });
                    ');
                    ?>

                </div>
<div class="col-lg-3 right"></div>

            </div>
<?php 
    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'id', 'visible' => false],
        [
                'attribute' => 'user_id',
                'label' => 'U¿ytkownik',
                'value' => function($model){
                    if ($model->user)
                    {return $model->user->first_name." ".$model->user->last_name;}
                    else
                    {return NULL;}
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \common\models\User::getList(),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'User', 'id' => 'grid--user_id']
            ],
        ['attribute' => 'text',
                'label' => 'Tekst',
                'format' => 'html'],
        'datetime',
        [
                'attribute' => 'event_id',
                'label' => 'Event',
                'value' => function($model){
                    if ($model->event)
                    {return $model->event->name;}
                    else
                    {return NULL;}
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\Event::find()->asArray()->all(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Event', 'id' => 'grid--event_id']
            ],
        [
                'attribute' => 'rent_id',
                'label' => 'Wypo¿yczenie',
                'value' => function($model){
                    if ($model->rent)
                    {return $model->rent->name;}
                    else
                    {return NULL;}
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\Rent::find()->asArray()->all(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Rent', 'id' => 'grid--rent_id']
            ],

        [
                'attribute' => 'customer_id',
                'label' => 'Klient',
                'value' => function($model){
                    if ($model->customer)
                    {return $model->customer->name;}
                    else
                    {if ($model->event)
                        {return $model->event->customer->name;}
                        else {
                            if ($model->rent)
                            {return $model->rent->customer->name;}
                        else return null;}
                        return null;}
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\Customer::find()->asArray()->all(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Customer', 'id' => 'grid--customer_id']
            ],
    ]; 
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
        'columns' => $gridColumn,
                    'filterSelector' => 'select[name="per-page"], .grid-filter',

        'pjax' => false,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-note']],
        'export' => false,
        // your toolbar can include the additional full export menu
        'toolbar' => [
            '{export}',
            ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $gridColumn,
                'target' => ExportMenu::TARGET_BLANK,
                'fontAwesome' => true,
                'dropdownOptions' => [
                    'label' => 'Full',
                    'class' => 'btn btn-default',
                    'itemsBefore' => [
                        '<li class="dropdown-header">Export All Data</li>',
                    ],
                ],
                'exportConfig' => [
                    ExportMenu::FORMAT_PDF => false
                ]
            ]) ,
        ],
    ]); ?>

</div>
