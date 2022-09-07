<?php

use kartik\tabs\TabsX;
use yii\helpers\Html;
use kartik\widgets\DatePicker;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\SettlementUser */
/* @var $user \common\models\User */
/* @var $tab String */
/* @var $month String */
/* @var $year String */

$formatter = Yii::$app->formatter;

$this->title = Yii::t('app', 'Rozliczenia');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="settlement-user-view">

    <h1><?= Html::encode($this->title) ?> </h1>
    <div style="margin-bottom: 20px;" class="row">
    <div class="col-lg-9">
        <?= Html::a('<<', $data['prevUrl'], ['class'=>'btn btn-primary']); ?>        
        <?= Html::dropDownList(null, $selectedItem, $dropdownItems, ['class' => 'changeMonth']) ?>
        <?= Html::a('>>', $data['nextUrl'], ['class'=>'btn btn-primary']); ?>
    </div>
    <div class="col-lg-3">
    <?php $form = ActiveForm::begin([
                'type'=>ActiveForm::TYPE_INLINE,
                'id' =>'report'
            ]) ?>
            <?php
            echo DatePicker::widget([
                'model' => $report,
                'attribute' => 'date_from',
                'options' => ['placeholder' => Yii::t('app', 'Wybierz...')],
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                    'autoclose' => true,
                ],
            ]);

            ?>
            <?php
            echo DatePicker::widget([
                'model' => $report,
                'attribute' => 'date_to',
                'options' => ['placeholder' => Yii::t('app', 'Wybierz...')],
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                    'autoclose' => true,
                ],
            ]);

            ?>
        <?= Html::a('<i class="fa fa-download"></i> Excel', ['/event-user-working-time/generate-raport'], ['class'=>'btn btn-success excel'] )?>
        <?php ActiveForm::end(); ?>
    </div>
    </div>

<?php

$activeFirst = false;
$activeSecond = false;
$renderString = '_tabSummaryFirst';
if ($tab == 'worker') {
    $activeFirst = true;
}
if ($tab == 'event') {
    $renderString ='_tabSummarySecond';
    $activeSecond = true;
}

$tabItems = [
    [
        'label'=>'<i class="fa fa-info"></i> '.Yii::t('app', 'Pracownicy'),
        'url' => 'index?month='.$month.'&year='.$year.'&tab=event',
        'active'=>$activeSecond,
    ],
    /*
    [
        'label'=>'<i class="fa fa-info"></i> '.Yii::t('app', 'Wydarzenie'),
        'url' => 'index?month='.$month.'&year='.$year.'&tab=worker',
        'active'=>$activeFirst,
    ],
    */
];

echo TabsX::widget([
    'items'=>$tabItems,
    'encodeLabels'=>false,
]);


echo $this->render($renderString, [
    'provider' => $provider,
    'data'=>$data,
    'dataProvider'=>$dataProvider,
    'searchModel'=>$searchModel,
    'paymentSum' => $paymentSum,
    'userSum' => $userSum,
    'userNormal' => $userNormal,
    'year' => $year,
    'month' => $month
]);

$this->registerJs('

$(".excel").click(function(e){
    e.preventDefault();
    $url = $(this).attr("href")+"?start="+$("#reportform-date_from").val()+"&end="+$("#reportform-date_to").val();
    location.href = $url;
});

$(".changeMonth").change(function(){
    if ($(this).val()==0) {
        return;
    }
    var month = $(this).val().substring(5,7);
    var year = $(this).val().substring(0,4);
    location.href = "index?month="+month+"&year="+year+"&tab='.$tab.'";
});

');

?>

</div>
