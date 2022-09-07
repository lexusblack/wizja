<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\IncomesWarehouse */
/* @var $modelsGear backend\models\OutcomesGearGeneral */
/* @var $event int */
/* @var $rent int */
/* @var $gear int */
/* @var $outer_gear int */
/* @var $group_gear int */

$this->title = Yii::t('app', 'Przyjęcie do magazynu');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Przyjęte'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$outcomeFor = null;
$showAlert = false;
if ($event) {
    $em = \common\models\Event::find()->where(['id'=>$event])->one();
    $packlist = \common\models\Packlist::find()->where(['id'=>$packlist_id])->one();
    $outcomeFor =  " - " . $em->name." - ".$packlist->name;
}
if ($rent) {
    $em = \common\models\Rent::find()->where(['id'=>$rent])->one();
    $outcomeFor = " - " . $em->name;
}
?>
<div class="incomes-warehouse-create">

    <h1><?= Html::encode($this->title).$outcomeFor ?></h1>


    <?= $this->render('_form', [
        'model' => $model,
        'modelsGear' => $modelsGear,
        'event' => $event,
        'rent' => $rent,
        'customer' => $customer,
        'outer_gear' => $outer_gear,
        'group_gear' => $group_gear,
        'onlyEvent'=>$onlyEvent,
        'packlist_id'=>$packlist_id
    ]) ?>

</div>

<?= $this->render('_rfid') ?>