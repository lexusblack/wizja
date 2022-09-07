<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\GearTranslate */

$this->title = Yii::t('app', 'Edytuj tłumaczenie');
$this->params['breadcrumbs'][] = ['label' => $model->role->name, 'url' => ['/user-event-role/view', 'id'=>$model->role_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gear-translate-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
