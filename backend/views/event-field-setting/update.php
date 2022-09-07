<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\EventFieldSetting */

$this->title = Yii::t('app', 'Edycja');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Dodatkowe pola w wydarzeniu'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-field-setting-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
