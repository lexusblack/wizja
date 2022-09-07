<?php

use yii\helpers\Html;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model common\models\OutcomesWarehouse */
/* @var $modelsGear backend\models\OutcomesGearGeneral */
/* @var $event int */
/* @var $rent int */
/* @var $gear int */
/* @var $outer_gear int */
/* @var $group_gear int */

$outcomeFor = null;
$showAlert = false;
if ($event) {
    $em = \common\models\Event::find()->where(['id'=>$event])->one();
    $packlist = \common\models\Packlist::find()->where(['id'=>$packlist_id])->one();
    $outcomeFor =  " - " . $em->name." - ".$packlist->name;
    $start = $packlist->start_time;
    if (date('Y-m-d H:i:s')<substr($start, 0, 10)." 00:00:00")
    {
        $showAlert = true;
    }
}
if ($rent) {
    $em = \common\models\Rent::find()->where(['id'=>$rent])->one();
    $outcomeFor = " - " . $em->name;
    $start = $em->getTimeStart();
    if (date('Y-m-d H:i:s')<substr($start, 0, 10)." 00:00:00")
    {
        $showAlert = true;
    }
}

$this->title = Yii::t('app', 'Wydanie z magazynu') . $outcomeFor;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wydanie'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="outcomes-warehouse-create">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php if ($showAlert) { ?>
    <div class="alert alert-danger">
        <?=Yii::t('app', 'Sprzęt z tego wydarzenia jest zarezerwowany od ').$start.". ".Yii::t('app', 'Wydając sprzęt teraz zmienisz daty rezerwacji')?>
    </div>
    <?php } ?>

    <?= $this->render('_form', [
        'model' => $model,
        'modelsGear' => $modelsGear,
        'event' => $event,
        'rent' => $rent,
        'gear' => $gear,
        'customer' => $customer,
        'outer_gear' => $outer_gear,
        'group_gear' => $group_gear,
        'warehouse' => $warehouse,
        'packlist_id'=>$packlist_id
    ]) ?>

</div>

<?= $this->render('_rfid') ?>